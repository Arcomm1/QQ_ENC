<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_remove_collect_outgoing_from_from_config extends CI_Migration {


    public function up()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_outgoing_from'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_incoming_from'");
    }


    public function down()
    {
        $data[] = array(
            'name' => 'app_track_outgoing_from',
            'value' => '0000-00-00 00:00:00',
            'default' => '0000-00-00 00:00:00',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_incoming_from',
            'value' => '0000-00-00 00:00:00',
            'default' => '0000-00-00 00:00:00',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

}
