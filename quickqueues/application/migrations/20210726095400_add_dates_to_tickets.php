<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_dates_to_tickets extends CI_Migration {


    public function up()
    {
        $fields = array(
            'created_at' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'due_at' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
        );

        $this->dbforge->add_column('qq_tickets', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_tickets', 'creted_at');
        $this->dbforge->drop_column('qq_tickets', 'due_at');

    }

}
