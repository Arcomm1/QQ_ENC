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
        $settings_data                  = $this->Settings_model->getSettings();
        $queues                         = $this->Queue_model->get_queue_entries();
        $queueDuplicateSettings         = $this->Settings_model->getDuplicateSettings();
        $settings_data['queues']        = $queues;
        $this->data->settings           = $settings_data;
        $this->data->duplicateSettings  = $queueDuplicateSettings;
        load_views('settings/index', $this->data, true);
    }

    public function get_settings()
    {
        $queue_id = $this->input->get('queue_id');
        $settings = $this->Settings_model->getSettings($queue_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($settings));
    }

    public function get_duplicate_settings()
    {
        $duplicateSettings = $this->Settings_model->getDuplicateSettings();
        $this->output->set_content_type('application/json')->set_output(json_encode($duplicateSettings));
    }

    public function update_settings()
    {
        if ($this->input->post()) 
        {
            // Update General Settings
            $general_settings = array(
                'call_overload' => $this->input->post('overload'),
                'sms_content'   => $this->input->post('sms_text'),
                'sms_token'     => $this->input->post('sms_key'),
                'sms_type'      => $this->input->post('sms_type'),
                'queue_id'      => $this->input->post('selected_queue_id'),
                'status'        => $this->input->post('status'),
            );
            $this->Settings_model->updateSettings($general_settings);

            // Update Duplicate Settings
            $duplicate_settings = array(
                'queue_log_force_duplicate_deletion' => $this->input->post('force_duplicate_deletion'),
                'queue_log_rollback_with_deletion'   => $this->input->post('rollback_with_deletion'),
                'queue_log_rollback'                 => $this->input->post('rollback'),
                'queue_log_rollback_days'            => $this->input->post('rollback_days'),
                'queue_log_fix_agent_duplicates'     => $this->input->post('fix_agent_duplicates'),
            );
            $this->Settings_model->updateDuplicateSettings($duplicate_settings);
        }
    }

   
}

