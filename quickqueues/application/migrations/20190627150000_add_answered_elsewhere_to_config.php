<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_answered_elsewhere_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_mark_answered_elsewhere',
            'value' => '0',
            'default' => '0',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_mark_answered_elsewhere'");
    }

}
