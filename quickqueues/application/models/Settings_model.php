<?php
class Settings_model extends MY_Model 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->database();
    }

    public function getSettings() 
    {
        $settingsToRetrieve = array('queue_id', 'sms_content', 'sms_token', 'sms_type', 'status');
        $finalSettings = array();
    
        // Assuming there's only one row or you want to fetch the first row
        $query = $this->db->get('qq_sms_logs');
        $setting = $query->row_array();
    
        foreach ($settingsToRetrieve as $settingName) 
        {
            // Use the ternary conditional operator to provide a default value of ''
            $finalSettings[$settingName] = isset($setting[$settingName]) ? $setting[$settingName] : '';
        }
    
        return $finalSettings;
    }


    public function updateSettings($name, $value)
    {
        $this->db->set($name, isset($value) ? $value : null);
        $this->db->update('qq_sms_logs');
    }
}
?>
