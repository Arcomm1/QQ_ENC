<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Broadcast_notifications extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('broadcast_notifs');

        foreach ($this->User_model->get_all() as $u) {
            $this->data->all_users[$u->id] = $u->display_name;
        }

        if ($this->session->userdata('role') == 'agent') {
            redirect(site_url('start'));
        }

    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/broadcast_notifications/index.js');
        $this->data->js_vars['users'] = json_encode($this->data->all_users);
        $this->data->broadcast_notifications = $this->Broadcast_notification_model->get_all();
        load_views(array('broadcast_notifications/index'), $this->data);
    }


    public function archive()
    {
        $this->data->js_include = base_url('assets/js/components/broadcast_notifications/archive.js');
        $this->data->js_vars['users'] = json_encode($this->data->all_users);
        load_views(array('broadcast_notifications/archive'), $this->data);
    }


}
