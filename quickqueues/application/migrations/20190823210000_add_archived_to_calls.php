<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_archived_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'archived' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no'
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'archived');

    }

}
