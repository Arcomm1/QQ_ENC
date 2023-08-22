<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Users extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('users');

        if ($this->session->userdata('role') != 'admin') {
            redirect(site_url('start'));
        }
    }


    public function index()
    {
        $this->data->users = $this->User_model->get_all();
        load_views('users/index', $this->data, true);
    }


    public function create()
    {
        $this->data->js_include = base_url('assets/js/components/users/create.js');

        $this->data->from_agent = $this->input->get('from_agent');
        $this->data->js_vars['from_agent'] = $this->input->get('from_agent');
        $this->data->js_vars['agent'] = json_encode($this->Agent_model->get($this->data->from_agent));

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['password'] = hash('md5', $data['pwd']);
            unset($data['pwd']);
            unset($data['from_agent_id']);
            $data['enabled'] = 'yes';
            $id = $this->User_model->create($data);
            if ($this->input->post('from_agent_id')) {
                $this->User_model->assign_agent($id, $this->input->post('from_agent_id'));
            }
            if ($id) {
                set_flash_notif('success', lang('user_create_success'));
                redirect(site_url('users/edit/'.$id));
            }
        }

        load_views('users/create', $this->data, true);
    }


    public function edit($id = false)
    {
        if (!$id) {
            redirect(site_url('users'));
        }

        if (!$this->User_model->exists($id)) {
            redirect(site_url('users'));
        }

        $this->data->user = $this->User_model->get($id);

        $this->data->js_include = base_url('assets/js/components/users/edit.js');
        $this->data->js_vars = array('user_id' => $id);
        $this->data->user_id = $id;

        if ($this->input->post()) {
            $data = $this->input->post();
            if (strlen($data['pwd']) > 1) {
                $data['password'] = hash('md5', $data['pwd']);
            }
            unset($data['pwd']);
            $this->User_model->update($id, $data);
            set_flash_notif('success', lang('user_edit_success'));
        }


        load_views('users/edit', $this->data, true);
    }


}
