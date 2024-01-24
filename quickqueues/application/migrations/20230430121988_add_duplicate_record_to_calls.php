<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_duplicate_record_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'duplicate_record' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => NULL
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }
}
