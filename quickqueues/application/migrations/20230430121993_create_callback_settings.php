<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_callback_settings extends CI_Migration 
{

    public function up()
    {
        $this->dbforge->create_table('qq_callback_settings');

        $fields = array (
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
        );
        
        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'queue_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_column('qq_callback_settings', $fields);
        
        $fields = array (
            'sla_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );
            
        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'timeout_callbacks' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );  
        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'sla_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        );  
        
        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'timeout_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
            ),
        ); 
         
        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'resolution' => array(
                'type'=> 'VARCHAR',
                'constraint' => 255,
            ),
        );

        $this->dbforge->add_column('qq_callback_settings', $fields);

        $fields = array (
            'date' => array(
            'type' => 'TIMESTAMP',
            'default' => 'CURRENT_TIMESTAMP',
            'on update CURRENT_TIMESTAMP' => TRUE,
           ),
       );

       $this->dbforge->add_column('qq_callback_settings', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_callback_settings');
    }

}
