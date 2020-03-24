<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {
        $this->load->view('user/index');
    }

    public function changepassword()
    {


        $user = $this->db->get_where('user', ['id' =>
        $this->session->userdata('id')])->row_array();

        $jika = $this->db->get_where('user', ['password' =>
        $this->session->userdata('password')])->row_array();



        $this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('password1', 'New Password', 'required|trim|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Confirm Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('templates/topbar');
            $this->load->view('user/changepassword');
            $this->load->view('templates/footer');
        } else {
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('password1');
            if ($current_password !== $jika) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong Current Password !</div>');
                redirect('user/changepassword');
            } else {
                if ($current_password == $new_password) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">New Password Cannot Be The Same As Current Password!</div>');
                    redirect('user/changepassword');
                } else {

                    $this->db->set('password', $new_password);
                    $this->db->update('user');
                    $this->db->where('id', $this->session->userdata('id'));
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password Changed!</div>');
                    redirect('user/changepassword');
                }
            }
        }
    }
}
