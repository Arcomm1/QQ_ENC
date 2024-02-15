<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_settings_logs_2 extends CI_Migration 
{
    public function up()
    {
        $columnsToAdd = [
            'calls_without_service_queue_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'sla_callbacks' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'timeout_callbacks' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'sla_calls' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'timeout_calls' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'date' => [
                'type' => 'DATETIME',
                // Note: Using '0000-00-00 00:00:00' as a default value might cause issues with strict SQL modes in MySQL 5.7+.
                // Consider using a valid default DATETIME value or CURRENT_TIMESTAMP if appropriate for your application.
                'default' => '0000-00-00 00:00:00',
            ],
        ];

        foreach ($columnsToAdd as $columnName => $columnDefinition) {
            if (!$this->db->field_exists($columnName, 'qq_settings_logs')) {
                $this->dbforge->add_column('qq_settings_logs', [$columnName => $columnDefinition]);
            }
        }
    }

    public function down()
    {
        $columnsToDrop = [
            'calls_without_service_queue_id',
            'sla_callbacks',
            'timeout_callbacks',
            'sla_calls',
            'timeout_calls',
            'date',
        ];

        foreach ($columnsToDrop as $columnName) {
            if ($this->db->field_exists($columnName, 'qq_settings_logs')) {
                $this->dbforge->drop_column('qq_settings_logs', $columnName);
            }
        }
    }
}
