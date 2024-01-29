<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** MY_Controller.php - base CodeIgniter controller upon which most of the quickqueues controller should derive from */


class MY_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect(site_url('auth/signin'));
        }
        $this->data = new stdClass();

        // Set default page title
        $this->data->page_title = 'Quickqueues';

        $this->data->logged_in_user = $this->User_model->get_by('name', $this->session->userdata('user'));
        unset($this->data->logged_in_user->password);

        $this->data->user_queues = $this->User_model->get_queues($this->data->logged_in_user->id);
        $this->data->user_agents = $this->User_model->get_agents($this->data->logged_in_user->id);

        $this->data->config = new stdClass();
        foreach ($this->Config_model->get_all() as $item) {
            $this->data->config->{$item->name} = $item->value;
        }


        if ($this->session->userdata('app_language')) {
            $this->lang->load(array('main', 'help'), $this->session->userdata('app_language'));
        } else {
            if ($this->data->config->app_language) {
                $this->lang->load(array('main', 'help'), $this->data->config->app_language);
            } else {
                $this->lang->load(array('main', 'help'), 'georgian');
            }
        }

        if ($this->data->config->app_notifications == 'yes') {
            $this->data->notifications = $this->Notification_model->get_new_for_user($this->data->logged_in_user->id);
        }
    }


    public function get_or_add_cached_data($key,$data = false)
    {
        if(empty($key))
        {
            return false;
        }
        if(!isset($_SESSION['cached_data'])) 
        {
            $_SESSION['cached_data'] = [];
        }
       
        if($data)
        {
            $_SESSION['cached_data'][$key] = $data;
        }
        
        if(isset($_SESSION['cached_data'][$key]))
        {
            return $_SESSION['cached_data'][$key];
        }

        return false;
    }

    public function checkOrAddKey($key)
    {
        var_dump($_SESSION['request_keys']);
        if(empty($key))
        {
            return false;
        }
        if(!isset($_SESSION['request_keys']))
        {
            $_SESSION['request_keys'] = [];
        }
        $entries = $_SESSION['request_keys'];

        if(in_array($key,$entries))
        {
            return true;
        }

        array_push($entries,$key);
        $_SESSION['request_keys'] = $entries;
        return false;
    }

}
