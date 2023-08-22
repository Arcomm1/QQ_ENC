<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_agent_config_agent_status extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'agent_show_other_agent_status',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'agent',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'agent_show_other_agent_status'");
    }

}
