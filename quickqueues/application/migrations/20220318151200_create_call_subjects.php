<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_call_subjects extends CI_Migration {


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
                'subject_id'=>array(
                        'type'=> 'int',
                        'constraint' => 11,
                        'default' => 0,
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
                'hided_at datetime',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_call_subjects_parent');


        $this->dbforge->add_field(
            array(
                'id' => array(
                        'type' => 'int',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                ),
                'parent_id'=> array(
                        'type' => 'int',
                        'constraint' => 11,
                        'null' => false,
                ),
                'subject_id'=>array(
                        'type'=> 'int',
                        'constraint' => 11,
                        'default' => 1,// I Child SubCategory
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
                'hided_at datetime',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_call_subjects_child_1');


        $this->dbforge->add_field(
            array(
                'id' => array(
                        'type' => 'int',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                ),
                'parent_id'=> array(
                        'type' => 'int',
                        'constraint' => 11,
                        'null' => false,
                ),
                'subject_id'=>array(
                        'type'=> 'int',
                        'constraint' => 11,
                        'default' => 2,
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
                'hided_at datetime',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_call_subjects_child_2');


        $this->dbforge->add_field(
            array(
                'id' => array(
                        'type' => 'int',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                ),
                'parent_id'=> array(
                        'type' => 'int',
                        'constraint' => 11,
                        'null' => false,
                ),
                'subject_id'=>array(
                        'type'=> 'int',
                        'constraint' => 11,
                        'default' => 3,
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
                'hided_at datetime',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_call_subjects_child_3');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_call_subjects_parent');
        $this->dbforge->drop_table('qq_call_subjects_child_1');
        $this->dbforge->drop_table('qq_call_subjects_child_2');
        $this->dbforge->drop_table('qq_call_subjects_child_3');
    }

}
