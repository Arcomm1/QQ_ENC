<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_notifications extends CI_Migration {


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
                'created_at' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00',
                ),
                'content' => array(
                    'type' => 'TEXT',
                ),
                'author_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0
                ),
                'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0
                ),
                'url' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'seen' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => 'no',
                ),
            )
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('seen');

        $this->dbforge->create_table('qq_notifications');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_notifications');
    }

}
