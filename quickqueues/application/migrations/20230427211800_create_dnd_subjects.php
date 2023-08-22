<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_dnd_subjects extends CI_Migration {

//20230427203500
    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                        'type' => 'int',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                ),
                'title' => array(
                        'type' => 'varchar',
                        'constraint' => 255,
                        'null' => false,
                ),
                'comment' => array(
                        'type' => 'varchar',
                        'constraint' => 255,
                        'null' => true,
                ),
                'visible'=> array(
                        'type' => 'int',
                        'constraint' => 1,
                        'null' => false,
                        'default' => 1,
                ),
                'dnd_started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'dnd_ended_at datetime',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_dnd_subjects');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_dnd_subjects');
    }

}
