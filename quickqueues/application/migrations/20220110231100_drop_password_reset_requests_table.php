<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_password_reset_requests_table extends CI_Migration {

    public function up()
    {
        // Check if the table exists before attempting to drop it
        if ($this->db->table_exists('qq_password_reset_requests')) {
            $this->dbforge->drop_table('qq_password_reset_requests', TRUE);
        }
    }

    public function down()
    {
        // Schema for recreating the qq_password_reset_requests table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 3,
            ),
            'created' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
                'default' => NULL,
            ),
            'has_error' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            ),
            'processed' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            ),
        ));
        $this->dbforge->add_key('id', TRUE); // Primary Key
        $this->dbforge->add_key('user_id'); // Index
        $this->dbforge->create_table('qq_password_reset_requests');
    }
}
