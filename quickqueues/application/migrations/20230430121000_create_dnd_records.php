<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_dnd_records extends CI_Migration {

    public function up()
    {
        // Check if the table does not exist before attempting to create it
        if (!$this->db->table_exists('qq_dnd_records')) {
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ),
                'agent_id' => array(
                    'type' => 'int',
                    'constraint' => 11,
                    'null' => false,
                ),
                'dnd_status' => array(
                    'type' => 'varchar',
                    'constraint' => 3,
                    'null' => false,
                    'default' => 'off',
                ),
                'title' => array(
                    'type' => 'int',
                    'constraint' => 2,
                    'null' => false,
                ),
                'comment' => array(
                    'type' => 'varchar',
                    'constraint' => 255,
                    'null' => true,
                ),
                'visible' => array(
                    'type' => 'int',
                    'constraint' => 1,
                    'null' => false,
                    'default' => 1,
                ),
                'dnd_started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'dnd_ended_at datetime',
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('qq_dnd_records');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_dnd_records', TRUE);
    }

}
