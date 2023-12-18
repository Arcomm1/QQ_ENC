<?php
class Settings_model extends MY_Model 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->database();
    }

    public function getSettings($queue_id = null) 
    {
        $settingsToRetrieve = array('queue_id', 'sms_content', 'sms_token', 'sms_type', 'status', 'call_overload');
        $finalSettings      = array();
        if ($queue_id !== null) 
        {
            $this->db->where('queue_id', $queue_id);
        }
        $query   = $this->db->get('qq_settings_logs');
        $setting = $query->row_array();
        foreach ($settingsToRetrieve as $settingName) 
        {
            $finalSettings[$settingName] = isset($setting[$settingName]) ? $setting[$settingName] : '';
        }
        return $finalSettings;
    }

    public function getAllSettings()
    {
        $this->db->select('*');
        $query = $this->db->get('qq_settings_logs');

        return $query->result_array();
    }

    public function updateSettings($data)
    {
        if (!empty($data)) 
        {
            $existingData = $this->db->get_where('qq_settings_logs', array('queue_id' => $data['queue_id']))->row_array();
    
            if ($existingData) 
            {
                $this->db->where('queue_id', $data['queue_id']);
                $this->db->update('qq_settings_logs', $data);
            } 
            else 
            {
                $this->db->insert('qq_settings_logs', $data);
            }
            return true; 
        }
    
        return false; 
    }
}
?>
