<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Additional_portal_user_settings_2 extends CI_Migration 
{
    public function up()
    {
        $data[] = array(
            'name'      => 'queue_id',
            'value'     => '[]', 
            'default'   => ''
        );

        $this->db->insert_batch('qq_portal_user_settings', $data);

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');
    }

}
