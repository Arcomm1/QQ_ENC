<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_subjects_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'subject_family' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => NULL,
            ),
            'subject_comment' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'subject_family');
        $this->dbforge->drop_column('qq_calls', 'subject_comment');


    }

}
