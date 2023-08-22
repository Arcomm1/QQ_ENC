<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_reset_password_tmp extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ),
                'uid' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'md5_token' => array(
                    'type' => 'varchar',
                    'constraint' => 32,
                ),
                'relased' => array(
                    'type' => 'int',
                    'constraint' => 1,
                    'default' => 0,
                ),
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_reset_password_tmp');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_reset_password_tmp');
    }
}
