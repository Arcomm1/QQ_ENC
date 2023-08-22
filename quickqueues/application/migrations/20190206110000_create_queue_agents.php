<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_queue_agents extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
            )
        );
        $this->dbforge->add_key('queue_id', true);
        $this->dbforge->add_key('agent_id', true);
        $this->dbforge->create_table('qq_queue_agents');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_queue_agents');
    }

}
