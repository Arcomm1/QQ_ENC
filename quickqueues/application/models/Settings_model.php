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

    public function getDuplicateSettings()
    {
        $settingsToRetrieve = array('queue_log_rollback', 'queue_log_rollback_days', 'queue_log_rollback_with_deletion', 'queue_log_force_duplicate_deletion', 'queue_log_fix_agent_duplicates', 'app_enable_switchboard');
        $retrievedSettings = array();
        $query             = $this->db->get('qq_config');
        if ($query->num_rows() > 0) 
        {
            foreach ($query->result() as $row)
            {
                $settingName = $row->name;
                if (in_array($settingName, $settingsToRetrieve)) 
                {
                    $retrievedSettings[$settingName] = $row->value;
                }
            }
        }
        return $retrievedSettings;
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

    public function updateDuplicateSettings($settingsToUpdate)
    {  
        foreach ($settingsToUpdate as $settingName => $settingValue) 
        {
            $this->db->where('name', $settingName);
            $query = $this->db->get('qq_config');

            if ($query->num_rows() > 0) 
            {
                $this->db->where('name', $settingName);
                $this->db->update('qq_config', array('value' => $settingValue));
            } 
        }
    }

}
?>
