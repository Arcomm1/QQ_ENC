<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_time_distribution_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_time_distribution_map',
            'value' => '15,30,60,120',
            'default' => '15,30,60,120',
            'category' => 'queue',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_time_distribution_map'");
    }

}
