<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_app_round_to_hundredth_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_round_to_hundredth',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );
        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_round_to_hundredth'");
    }

}
