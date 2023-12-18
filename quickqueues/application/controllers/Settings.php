<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Settings_model');
        $this->load->model('Queue_model');
        
        $this->app_language = $this->Config_model->get_item('app_language');

        if ($this->app_language) 
        {
            $this->lang->load(array('main', 'help'), $this->app_language);
        } 
        else 
        {
            $this->lang->load(array('main', 'help'), 'english');
        }

        $this->data = new stdClass();
        $this->data->page_title = lang('settings');   
    }
    
    public function index()
    {
        $settings_data           = $this->Settings_model->getSettings();
        $queues                  = $this->Queue_model->get_queue_entries();
        $settings_data['queues'] = $queues;
        $this->data->settings    = $settings_data;
        load_views('settings/index', $this->data, true);
    }

    public function get_settings()
    {
        $queue_id = $this->input->get('queue_id');
        $settings = $this->Settings_model->getSettings($queue_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($settings));
    }

    public function update_settings()
    {
        if ($this->input->post())
        {
            $data = array(
                'call_overload' => $this->input->post('overload'),
                'sms_content'   => $this->input->post('sms_text'),
                'sms_token'     => $this->input->post('sms_key'),
                'sms_type'      => $this->input->post('sms_type'),
                'queue_id'      => $this->input->post('selected_queue_id'),
                'status'        => $this->input->post('status'),
            );
            $this->Settings_model->updateSettings($data);
        }
    }
}

