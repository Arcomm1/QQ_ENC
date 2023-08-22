<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_drop_agent_sessions extends CI_Migration {


    public function up()
    {
        $this->dbforge->drop_table('qq_agent_sessions');
    }

    public function down()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 9,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                ),
                'start_date' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'end_date' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'comment' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->add_key('agent_id');
        $this->dbforge->create_table('qq_agent_sessions');
    }
}
