<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_service_module_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_service_module',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_service_module'");
    }

}
