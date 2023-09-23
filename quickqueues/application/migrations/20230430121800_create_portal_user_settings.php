<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Create_portal_user_settings extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(
            array(
                'call_overload' => array(
                        'type' => 'int',
                        'constraint' => 255,
                        'null' => false,
                ),
                'sms_content' => array(
                        'type' => 'varchar',
                        'constraint' => 255,
                        'null' => true,
                )
            )
        );
        $this->dbforge->create_table('qq_portal_user_settings');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');
    }

}
