<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_ticket_comments extends CI_Migration {


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
                'ticket_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'author_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'comment' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
            )
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('author_id', true);
        $this->dbforge->add_key('ticket_id', true);
        $this->dbforge->create_table('qq_ticket_comments');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_ticket_comments');
    }

}
