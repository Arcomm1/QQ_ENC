<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_call_statuses_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_call_statuses',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);


        $this->dbforge->drop_column('qq_calls', 'status');

        $fields = array(
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'default' => 'open',
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_call_statuses'");
    }

}
