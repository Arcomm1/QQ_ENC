<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_agent_config_call_restrictions extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'agent_call_restrictions',
            'value' => 'own',
            'default' => 'own',
            'category' => 'agent',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'agent_call_restrictions'");
    }

}
