<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_session_late_to_events extends CI_Migration {


    public function up()
    {
        $fields = array(
            'session_start_late' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );
        $this->dbforge->add_column('qq_events', $fields);


        $fields = array(
            'session_end_early' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_events', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_events', 'session_start_late');
        $this->dbforge->drop_column('qq_events', 'session_end_early');
    }

}