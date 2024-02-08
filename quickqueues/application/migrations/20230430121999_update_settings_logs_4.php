<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_settings_logs_4 extends CI_Migration 
{
    public function up()
    {
        // Check if 'queue_id' column exists before trying to add it
        if (!$this->db->field_exists('queue_id', 'qq_settings_logs')) {
            $fields = array(
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
            );
            $this->dbforge->add_column('qq_settings_logs', $fields);
        }

        // Use $this->db->field_exists() to check if other columns exist before dropping them
        if ($this->db->field_exists('calls_without_service_queue_id', 'qq_settings_logs')) 
        {
            $this->dbforge->drop_column('qq_settings_logs', 'calls_without_service_queue_id');
        }

        if ($this->db->field_exists('date', 'qq_settings_logs')) 
        {
            $this->dbforge->drop_column('qq_settings_logs', 'date');
        }
    }

    public function down()
    {
        // Add the logic to revert the 'up' method changes if necessary
    }
}
