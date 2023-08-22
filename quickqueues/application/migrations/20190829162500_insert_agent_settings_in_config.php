<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_agent_settings_in_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'agent_work_start_time',
            'value' => '09:00',
            'default' => '09:00',
            'category' => 'agent',
        );
        $data[] = array(
            'name' => 'agent_work_end_time',
            'value' => '09:00',
            'default' => '09:00',
            'category' => 'agent',
        );
        $data[] = array(
            'name' => 'agent_max_pause_time',
            'value' => '60',
            'default' => '60',
            'category' => 'agent',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'agent_work_start_time'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_work_end_time'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_max_pause_time'");


    }

}
