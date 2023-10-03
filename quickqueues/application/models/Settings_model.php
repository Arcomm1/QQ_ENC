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
        $settingsToRetrieve = array('call_overload', 'sms_content', 'sms_token', 'sms_type');
        $finalSettings = array();

        foreach ($settingsToRetrieve as $settingName) 
        {
            $query = $this->db->get_where('qq_portal_user_settings', array('name' => $settingName));
            $setting = $query->row_array();

            if (!empty($setting['value']) || $setting['value'] === '0') 
            {
                $finalSettings[$settingName] = $setting['value'];
            } 
            else 
            {
                $finalSettings[$settingName] = $setting['default'];
            }
        }

        return $finalSettings;
    }
    
    public function updateSettings($name, $value)
    {
        $this->db->set('value', $value);
        $this->db->where('name', $name);
        $this->db->update('qq_portal_user_settings');
    }
}
?>
