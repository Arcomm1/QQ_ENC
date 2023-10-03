<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

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
        $this->load->model('Settings_model');

        // Get settings data from the model
        $settings_data = $this->Settings_model->getSettings();
       

        // Pass the settings data to the view
        $this->data->settings = $settings_data;

        // Load the view
        load_views('settings/index', $this->data, true);
    }

    public function update_settings()
    {
        if($this->input->post())
        {
            $this->load->model('Settings_model');

            $newOverload   = $this->input->post('overload');
            $newSmsContent = $this->input->post('sms_text');
            $smsKey        = $this->input->post('sms_key');
            $smsType       = $this->input->post('sms_type');

            $this->Settings_model->updateSettings('call_overload', $newOverload);
            $this->Settings_model->updateSettings('sms_content', $newSmsContent);
            $this->Settings_model->updateSettings('sms_token', $smsKey);
            $this->Settings_model->updateSettings('sms_type', $smsType);

            redirect('settings/index');
        }
    }
}
