<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_track_incoming_from_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_track_incoming_from',
            'value' => '0000-00-00 00:00:00',
            'default' => '0000-00-00 00:00:00',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_incoming_from'");
    }

}