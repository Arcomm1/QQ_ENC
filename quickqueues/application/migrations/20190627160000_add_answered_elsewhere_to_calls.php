<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_answered_elsewhere_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'answered_elsewhere' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'answered_elsewhere');

    }

}
