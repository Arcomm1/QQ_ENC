<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_call_priorities_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_call_priorities',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);


        $this->dbforge->drop_column('qq_calls', 'priority');

        $fields = array(
            'priority' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'default' => 'normal',
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_call_priorities'");
    }

}
