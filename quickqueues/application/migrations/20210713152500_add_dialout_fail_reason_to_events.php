<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_dialout_fail_reason_to_events extends CI_Migration {


    public function up()
    {
        $fields = array(
            'dialout_fail_reason' => array(
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
        $this->dbforge->drop_column('qq_events', 'dialout_fail_reason');
    }

}
