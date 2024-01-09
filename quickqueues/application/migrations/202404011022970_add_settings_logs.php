<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_settings_logs_1 extends CI_Migration 
{

    public function up()
    {
        $fields = array(
            'calls_without_service_queue_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ),
            'sla_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
            'timeout_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
            'sla_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
            'timeout_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
            'date' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        );

        $this->dbforge->add_column('qq_settings_logs', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_settings_logs','calls_without_service_queue_id');
        $this->dbforge->drop_column('qq_settings_logs','sla_callbacks');
        $this->dbforge->drop_column('qq_settings_logs','timeout_callbacks');
        $this->dbforge->drop_column('qq_settings_logs','sla_calls');
        $this->dbforge->drop_column('qq_settings_logs','timeout_calls');
        $this->dbforge->drop_column('qq_settings_logs','date');
    }
}
