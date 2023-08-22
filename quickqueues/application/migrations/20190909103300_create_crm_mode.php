<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_crm_mode extends CI_Migration {


    public function up()
    {
        $fields = array(
            'priority' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

        $fields = array(
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

        $fields = array(
            'subcategory_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

        $fields = array(
            'curator_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

        $data[] = array(
            'name' => 'app_crm_mode',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'priority');
        $this->dbforge->drop_column('qq_calls', 'status');
        $this->dbforge->drop_column('qq_calls', 'subcategory_id');
        $this->dbforge->drop_column('qq_calls', 'curator_id');
        $this->db->query("DELETE from qq_config WHERE name = 'app_crm_mode'");
    }

}