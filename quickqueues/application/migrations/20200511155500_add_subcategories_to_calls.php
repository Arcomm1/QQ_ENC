<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_subcategories_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'subcategory_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'subcategory_id');

    }

}
