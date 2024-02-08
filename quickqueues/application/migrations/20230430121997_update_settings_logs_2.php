<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_settings_logs_2 extends CI_Migration 
{
    public function up()
    {
        // List of columns to potentially drop
        $columns = [
            'queue_id', 
            'sla_callbacks', 
            'timeout_callbacks', 
            'sla_calls', 
            'timeout_calls', 
            'resolution', 
            'data'
        ];

        // Check and drop each column if it exists
        foreach ($columns as $column) {
            if ($this->db->field_exists($column, 'qq_settings_logs')) {
                $this->dbforge->drop_column('qq_settings_logs', $column);
            }
        }
    }

    public function down()
    {
        // Intentionally left empty
    }
}
