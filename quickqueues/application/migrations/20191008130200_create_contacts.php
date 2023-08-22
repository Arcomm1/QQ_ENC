<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_contacts extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'number' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'custom1' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'custom2' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'custom3' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'custom4' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_contacts');

        $data[] = array(
            'name' => 'app_contacts',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_contacts');
        $this->db->query("DELETE from qq_config WHERE name = 'app_contacts'");
    }
}
