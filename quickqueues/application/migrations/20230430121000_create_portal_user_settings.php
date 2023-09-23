<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Create_portal_user_settings extends CI_Migration {

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
                'overload' => array(
                        'type' => 'int',
                        'constraint' => 255,
                        'null' => false,
                ),
                'sms_comment' => array(
                        'type' => 'varchar',
                        'constraint' => 255,
                        'null' => true,
                ),
                'dnd_started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'dnd_ended_at datetime',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_portal_user_settings');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');
    }

}
