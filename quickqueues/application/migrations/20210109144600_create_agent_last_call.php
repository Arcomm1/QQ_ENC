<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_agent_last_call extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'agent_id' => array(
                    'type' => 'INT',
                    'unique' => true,
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'src' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                )
            )
        );
        $this->dbforge->create_table('qq_agent_last_call');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_agent_last_call');
    }
}
