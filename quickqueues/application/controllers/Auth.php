<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Auth.php - Quickqueues authentication routines */


class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->app_language = $this->Config_model->get_item('app_language');

        if ($this->app_language) {
            $this->lang->load(array('main', 'help'), $this->app_language);
        } else {
            $this->lang->load(array('main', 'help'), 'english');
        }

        $this->data = new stdClass();
    }


    public function signin()
    {
        if ($this->session->userdata('logged_in')) {
            redirect(site_url('start'));
        }
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        if ($this->form_validation->run() == false) {
            $data['auth_errors'] = validation_errors();
        } else {
            $user = $this->User_model->get_by('name', $this->input->post('username'));
            if (!$user) {
                $data['auth_errors'] = lang('incorrect_username');
            } else {
                if ($user->enabled == 'no') {
                    $data['auth_errors'] = lang('user_deactivated');
                } else {
                    if (md5($this->input->post('password')) != $user->password) {
                        $data['auth_errors'] = lang('incorrect_password');
                        $this->User_log_model->add_activity($user->id, 'LOGIN_ATTEMPT', "INCORRECT_PASSWORD");
                    } else {
                        $this->session->set_userdata('app_language', $this->input->post('app_language'));
                        if ($user->role == 'admin') {
                            $this->User_model->update($user->id, array('last_login' => date('Y-m-d H:i:s')));
                            $this->session->set_userdata('logged_in', true);
                            $this->session->set_userdata('user', $user->name);
                            $this->session->set_userdata('user_id', $user->id);

                            $this->session->set_userdata('role', $user->role);

                            // set_flash_notif('success', lang('auth_success'));
                            $this->User_log_model->add_activity($user->id, 'LOGIN', "");
                            redirect(site_url('start'), 'refresh');
                        } else {
                            if ($user->role == 'agent') {
                                if (!$user->associated_agent_id) {
                                    $data['auth_errors'] = lang('user_no_agents');
                                    $this->User_log_model->add_activity($user->id, 'LOGIN_ATTEMPT', "NO_AGENTS");
                                } else {
                                    $this->User_model->update($user->id, array('last_login' => date('Y-m-d H:i:s')));
                                    $this->session->set_userdata('logged_in', true);
                                    $this->session->set_userdata('user', $user->name);
                                    $this->session->set_userdata('role', $user->role);
                                    // set_flash_notif('success', lang('auth_success'));
                                    redirect(site_url('start'), 'refresh');
                                }
                            }
                            if ($user->role == 'manager') {
                                $queues = $this->User_model->get_queues($user->id);
                                if (count($queues) < 1) {
                                    $data['auth_errors'] = lang('user_no_queues');
                                    $this->User_log_model->add_activity($user->id, 'LOGIN_ATTEMPT', "NO_QUEUES");

                                } else {
                                    $this->User_model->update($user->id, array('last_login' => date('Y-m-d H:i:s')));
                                    $this->session->set_userdata('logged_in', true);
                                    $this->session->set_userdata('user', $user->name);
                                    $this->session->set_userdata('role', $user->role);
                                    // set_flash_notif('success', lang('auth_success'));
                                    redirect(site_url('start'), 'refresh');
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['page_title'] = lang('sign_in');
        $this->load->view('auth/signin', $data);
    }


    public function reset_password()
    {
        $data['display_status']='yes';

        $username = $this->input->post('username');
        $find_name_or_email = $this->User_model->find_name_or_email($username);

        if($find_name_or_email){
            $user_email=$find_name_or_email['email'];
            $sender_email='quickqueues.resetpass@gmail.com';
            $user_password='roma123456.';
            $receiver_email=$user_email;
            $subject='Password Reset from qqueues';
            $new_password_url= site_url('auth/check_isvalid');
            $md5_token=md5($user_email.$find_name_or_email['id'].time('u'));
            $message='';
            $message.=lang('password_reset_link_below').' <br>';
            $message.="<a href=".$new_password_url.$md5_token.">";
            $message.='<b>'.lang('set_new_password').'</b>';
            $message.="</a>";

            $email_config['protocol']   = 'smtp';
            $email_config['smtp_host']  = "smtp.googlemail.com";
            $email_config['smtp_user']  = $sender_email;
            $email_config['smtp_pass']  = $user_password;
            $email_config['wordwrap']   = FALSE;
            $email_config['smtp_port']  = '587';
            $email_config['smtp_timeout'] = 15;
            $email_config['smtp_crypto'] = 'tls';
            $email_config['mailtype']  = 'html';
            $email_config['charset']   = 'utf-8';

            $this->load->library('email', $email_config);
            $this->email->set_newline("\r\n");

            $this->email->to($receiver_email);
            $this->email->from("Arcomm");
            $this->email->subject($subject);
            $this->email->message($message);

            if ($this->email->send()) {
                $data=array(
                    'uid'=> $find_name_or_email['id'],
                    'md5_token' => $md5_token,
                );

                $fix_record=$this->Reset_password_tmp_model->create_reset_password_record($data);

                $msg=lang('password_reset_email_sent_succesfully');
                $this->session->set_flashdata('msg', $msg);
                redirect(site_url('status_messenger/auth_messenger'), 'refresh');
            }
            else {
                $this->session->set_flashdata('msg_type','danger');
                $this->session->set_flashdata('msg_body','Could not send email');
            }

        }
        else{
            //echo 'User Not Found';
        }

        $data['page_title'] = lang('forgot_password');
        $this->load->view('auth/reset_password', $data);
    }


    // Chek if All Data Is Valid(token length, relased 0-1, out of date(1 Day))
    public function check_isvalid($md_token=false)
    {
        $user_data=$this->Reset_password_tmp_model->find_token($md_token);
        $record_id=$user_data['id'].'<br>';
        $user_id=$user_data['uid'].'<br>';
        $md_token_db=$user_data['md5_token'];
        $relased=$user_data['relased'].'<br>';
        $created_at=$user_data['created_at'];
        $curr_date_time=date("Y-m-d H:i:s");

        $datetime1 = new DateTime($created_at);
        $datetime2 = new DateTime($curr_date_time);
        $interval = $datetime1->diff($datetime2);
        $interval=$interval->format('%d');


        if(strlen($md_token)==32){
            if($relased==1){
                //Password reset Link is Olready used before
                $msg='Password reset Link is Olready used before';
                $this->session->set_flashdata('msg', $msg);
                redirect(site_url('status_messenger/auth_messenger'), 'refresh');
            }

            if($interval>0){
                //After request to reset password time interval more than 24 hours
                $msg='After request to reset password time interval more than 24 hours';
                $this->session->set_flashdata('msg', $msg);
                redirect(site_url('status_messenger/auth_messenger'), 'refresh');
            }

            //Success
            $data['page_title'] = lang('set_new_password');
            $data['md_token']=$md_token;
            $this->load->view('auth/new_password', $data);
        }
        else{
            //Token is Invalid
            $msg= 'Invalid Request';
            $this->session->set_flashdata('msg', $msg);
            redirect(site_url('status_messenger/auth_messenger'), 'refresh');
        }
    }


    public function set_password()
    {
        $md_token=$this->input->post('md_token');
        $new_password=$this->input->post('new_password');

        $user_data=$this->Reset_password_tmp_model->find_token($md_token);
        $user_id=$user_data['uid'];
        $this->Reset_password_tmp_model->set_new_password($md_token, $user_id, $new_password);

        $msg=lang('password_change_success');
        $this->session->set_flashdata('msg', $msg);
        redirect(site_url('status_messenger/auth_messenger'), 'refresh');
    }


    public function signout()
    {
        $this->User_log_model->add_activity($this->session->userdata('user_id'), 'LOGOUT', '');
        $this->session->sess_destroy();
        redirect(site_url('start'));
    }


}
