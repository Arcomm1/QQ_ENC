<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_settings_logs_4 extends CI_Migration 
{
    public function up()
    {
        $fields = array(
            'queue_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
        );
        $this->dbforge->add_column('qq_settings_logs', $fields);

        // Use $this->db->field_exists() to check if the column exists
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
      $this->dbforge->drop_table('qq_settings_logs');
    }
}
