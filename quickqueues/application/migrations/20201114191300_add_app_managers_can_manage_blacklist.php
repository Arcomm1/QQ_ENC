<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_app_managers_can_manage_blacklist extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_managers_can_manage_blacklist',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );
        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_managers_can_manage_blacklist'");
    }

}