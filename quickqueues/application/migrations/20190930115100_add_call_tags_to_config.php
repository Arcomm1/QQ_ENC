<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_call_tags_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_call_tags',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);


        $this->dbforge->drop_column('qq_calls', 'subcategory_id');

        $fields = array(
            'tag_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_call_tags'");
    }

}