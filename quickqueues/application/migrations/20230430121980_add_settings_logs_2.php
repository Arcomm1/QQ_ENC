<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_settings_logs_2 extends CI_Migration 
{

    public function up()
    {
        $this->down();

        $fields = array (
            'calls_without_service_queue_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_column('qq_settings_logs', $fields);

        $fields = array (
            'sla_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );
            
        $this->dbforge->add_column('qq_settings_logs', $fields);

        $fields = array (
            'timeout_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );  
        $this->dbforge->add_column('qq_settings_logs', $fields);

        $fields = array (
            'sla_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );  
        
        $this->dbforge->add_column('qq_settings_logs', $fields);

        $fields = array (
            'timeout_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );  
            
         $this->dbforge->add_column('qq_settings_logs', $fields);
         $fields = array (
            'date' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP(6)',
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
