<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_category_to_subcategories extends CI_Migration {


    public function up()
    {
        $fields = array(
            'category_id' => array(
                'type' => 'INT',
                'constraint' => 3,
            )
        );
        $this->dbforge->add_column('qq_call_subcategories', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_call_subcategories', 'category_id');
    }

}
