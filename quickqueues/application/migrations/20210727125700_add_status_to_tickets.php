<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_status_to_tickets extends CI_Migration {


    public function up()
    {
        $fields = array(
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            )
        );

        $this->dbforge->add_column('qq_tickets', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_tickets', 'status');

    }

}
