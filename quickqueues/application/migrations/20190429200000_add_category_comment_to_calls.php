<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_category_comment_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'comment' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
            ),
            'category_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'comment');
        $this->dbforge->drop_column('qq_calls', 'category_id');

    }

}
