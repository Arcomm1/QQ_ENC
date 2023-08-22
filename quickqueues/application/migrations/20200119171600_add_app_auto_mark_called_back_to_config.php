<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_app_auto_mark_called_back_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_auto_mark_called_back',
            'value' => '480',
            'default' => '480',
            'category' => 'application',
        );
        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_auto_mark_called_back'");
    }

}
