<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_transfered_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'transferred' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
        $fields = array(
            'transferdst' => array(
                'type' => 'INT',
                'constraint' => 30,
                'default' => null,
            )
        );
        $this->dbforge->add_column('qq_calls', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'transferred');
        $this->dbforge->drop_column('qq_calls', 'transferdst');

    }

}
