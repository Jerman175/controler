<?php
defined('BASEPATH') or exit('No direct script access allowed');

class auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header');
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            //sukses
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');


        $user = $this->db->get_where('user', ['username' => $username])->row_array();

        if ($user) {
            if ($password == $user['password']) {

                $data = [
                    'username' => $username['username']

                ];

                $this->session->set_userdata($data);
                redirect('user');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong Password!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Username Not Found!</div>');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('password');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">You Have Been Logout!</div>');
        redirect('auth');
    }
    private function _sendEmail($type)
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'abellsmkti@gmail.com',
            'smtp_pass' => 'abell123%%',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"

        ];
        $this->load->library('email', $config);

        $this->email->from('abellsmkti@gmail.com', 'Kelompok 1 RPL');
        $this->email->to($this->input->post('email'));

        if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to reset password : <a href="'
                . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '">Reset Password</a>');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }
    public function forgotpassword()
    {
        // $this->_sendEmail();
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $this->_sendEmail('forgot');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please Chek Your Email To Riset Password!</div>');
            redirect('auth');
        }
    }
}
