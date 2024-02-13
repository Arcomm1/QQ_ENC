<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_qq_callback_history extends CI_Migration 
{

    public function up()
    {
        // Check if the table already exists
        if (!$this->db->table_exists('qq_callback_history')) 
        {
            // Define the fields for the qq_callback_history table
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'number' => array(
                    'type' => 'BIGINT',
                    'unsigned' => true,
                ),
                'queue' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'try' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
                'request_date' => array(
                    'type' => 'DATETIME',
                ),
                'connection_time' => array(
                    'type' => 'DATETIME',
                ),
                'SLA' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
                'connection_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'calltime' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
                'waittime' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
                'agent' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
            ));
            
            // Define the primary key
            $this->dbforge->add_key('id', true);
            // Create the table
            $this->dbforge->create_table('qq_callback_history');
        }
    }

    public function down()
    {
        // Drop the qq_callback_history table if it exists
        if ($this->db->table_exists('qq_callback_history')) {
            $this->dbforge->drop_table('qq_callback_history');
        }
    }
}
