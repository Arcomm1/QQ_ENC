<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_track_agent_timers_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_track_agent_pause_time',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_agent_session_time',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_agent_session_time'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_agent_pause_time'");
    }

}
