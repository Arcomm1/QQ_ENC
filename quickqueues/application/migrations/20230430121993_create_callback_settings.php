<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_callback_settings extends CI_Migration 
{
    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'queue_id' => array(
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
            'resolution' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'date' => array(
                'type' => 'TIMESTAMP',
                // Removed the default attribute here to avoid the initial creation error
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_callback_settings', TRUE);

        // After successfully creating the table, modify the 'date' column
        // to have CURRENT_TIMESTAMP as both its default value and on update value.
        $this->db->query('ALTER TABLE qq_callback_settings MODIFY COLUMN date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_callback_settings');
    }
}