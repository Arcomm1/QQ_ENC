<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_type_to_news extends CI_Migration {


    public function up()
    {
        $fields = array(
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            )
        );

        $this->dbforge->add_column('qq_news', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_news', 'type');

    }

}
