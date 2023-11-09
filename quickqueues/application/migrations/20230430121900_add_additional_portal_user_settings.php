<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Additional_portal_user_settings extends CI_Migration {

    public function up()
    {
        $data[] = array(
            'name'      => 'sms_token',
            'value'     => '',
            'default'   => ''
        );

        $data[] = array(
            'name'      => 'sms_type',
            'value'     => '1',
            'default'   => '1'
        );

        $this->db->insert_batch('qq_portal_user_settings', $data);

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');
    }

}