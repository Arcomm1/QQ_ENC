<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_called_back_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_track_called_back_calls',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_called_back_calls'");
    }

}