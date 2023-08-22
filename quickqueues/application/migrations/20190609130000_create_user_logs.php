<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_user_logs extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'ip_address' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ),
                'date' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'action' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'data' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'url' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
            )
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('user_id', true);
        $this->dbforge->add_key('action', true);
        $this->dbforge->create_table('qq_user_logs');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_user_logs');
    }

}