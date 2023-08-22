<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_password_reset_requests extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 9,
                    'unsigned' => true,
                    'auto_increment' => true,
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
                    'null' => true,
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
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('qq_password_reset_requests');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_password_reset_requests');
    }
}
