<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_system_stats extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'date' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'calls_ongoing' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
            )
        );
        $this->dbforge->add_key('date');
        $this->dbforge->create_table('qq_system_stats');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_system_stats');
    }
}
