<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_agent_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'agent_listen_calls',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'agent',
        );
        $data[] = array(
            'name' => 'agent_download_calls',
            'value' => 'no',
            'default' => 'no',
            'category' => 'agent',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'agent_listen_calls'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_download_calls'");

    }

}