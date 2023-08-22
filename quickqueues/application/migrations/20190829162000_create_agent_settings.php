<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_agent_settings extends CI_Migration {


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
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'value' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'default' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                )
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_agent_settings');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_agent_settings');
    }
}
