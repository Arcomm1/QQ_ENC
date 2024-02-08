<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_qq_agents_archived extends CI_Migration {

    public function up()
    {
        // Check if the table already exists
        if (!$this->db->table_exists('qq_agents_archived')) {
            // Define the fields for the qq_agents_archived table
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'default' => null,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ),
                'display_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ),
                'extension' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                    'default' => null,
                ),
                'date' => array(
                    'type' => 'DATETIME',
                    'null' => true,
                    'default' => null,
                ),
            ));
            // Define the primary key
            $this->dbforge->add_key('id', true);
            // Create the table
            $this->dbforge->create_table('qq_agents_archived');
        }
    }

    public function down()
    {
        // Drop the qq_agents_archived table if it exists
        if ($this->db->table_exists('qq_agents_archived')) {
            $this->dbforge->drop_table('qq_agents_archived');
        }
    }
}
