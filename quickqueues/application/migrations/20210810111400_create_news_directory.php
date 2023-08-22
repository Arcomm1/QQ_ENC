<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_news_directory extends CI_Migration {


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
                'ends_at' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00',
                ),
                'title' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'content' => array(
                    'type' => 'TEXT',
                ),
                'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0
                ),
                'deleted' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'default' => 0
                )
            )
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('qq_news');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_news');
    }

}
