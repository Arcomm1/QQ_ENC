<?php

defined('BASEPATH') OR exit('No direct script access allowed');


/* Displays all statuses & messages  */


class Status_messenger extends CI_Controller
{
    public function auth_messenger()
    {
        $user_msg = $this->session->flashdata('msg');

        $data['page_title'] = lang('set_new_password');
        $data['user_msg']=$user_msg;
        $this->load->view('auth/auth_messenger', $data);
    }
}
