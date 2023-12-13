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

    public function update_settings()
    {
        if ($this->input->post())
        {
            $newOverload   = $this->input->post('overload');
            $newSmsContent = $this->input->post('sms_text');
            $smsKey        = $this->input->post('sms_key');
            $smsType       = $this->input->post('sms_type');
            $queueId       = $this->input->post('selected_queue_id'); // Updated from 'queue_id'

            $this->Settings_model->updateSettings('call_overload', $newOverload);
            $this->Settings_model->updateSettings('sms_content', $newSmsContent);
            $this->Settings_model->updateSettings('sms_token', $smsKey);
            $this->Settings_model->updateSettings('sms_type', $smsType);
            $this->Settings_model->updateSettings('queue_id', $queueId);

            redirect('settings/index');
        }
    }
}
