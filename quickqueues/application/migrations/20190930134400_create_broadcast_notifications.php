<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_broadcast_notifications extends CI_Migration {


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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'description' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 400,
                )
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_broadcast_notifications');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_broadcast_notifications');
    }
}
