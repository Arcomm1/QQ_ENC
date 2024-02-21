<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_reset_password_tmp extends CI_Migration {

    public function up()
    {
        // Check if the table already exists
        if (!$this->db->table_exists('qq_reset_password_tmp')) {
            // Define the fields for the qq_reset_password_tmp table
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ),
                'uid' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'md5_token' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                ),
                'relased' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'default' => 0,
                ),
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            ));

            // Specify the primary key
            $this->dbforge->add_key('id', TRUE);

            // Create the qq_reset_password_tmp table
            $this->dbforge->create_table('qq_reset_password_tmp');
        }
    }

    public function down()
    {
        // Drop the qq_reset_password_tmp table if it exists
        $this->dbforge->drop_table('qq_reset_password_tmp', TRUE);
    }
}
