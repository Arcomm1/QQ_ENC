<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_duplicate_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'duplicate' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

        $data[] = array(
            'name' => 'app_track_duplicate_calls',
            'value' => '0',
            'default' => '0',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'duplicate');
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_duplicate_calls'");

    }

}