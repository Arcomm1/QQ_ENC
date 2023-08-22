<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_called_back_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'called_back' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            )
        );
        $this->dbforge->add_column('qq_calls', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'called_back');
    }

}
