<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_future_events extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                ),
                'priority' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 20
                ),
                'category_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'subcategory_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'curator_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'comment' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                    'null' => true,
                    'default' => NULL,
                ),
                'service_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'service_product_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'service_product_type_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'service_product_subtype_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'custom_1' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 70,
                    'null' => true,
                    'default' => NULL,
                ),
                'custom_2' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 70,
                    'null' => true,
                    'default' => NULL,
                ),
                'custom_3' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 70,
                    'null' => true,
                    'default' => NULL,
                ),
                'custom_4' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 70,
                    'null' => true,
                    'default' => NULL,
                ),
                'ticket_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'default' => NULL,
                ),
                'src' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                    'default' => NULL,
                ),
                'ticket_department_id' => array(
                    'type' => 'INT',
                    'constraint' => 30,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'ticket_category_id' => array(
                    'type' => 'INT',
                    'constraint' => 30,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'ticket_category_id' => array(
                    'type' => 'INT',
                    'constraint' => 30,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'subject_family' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => true,
                    'default' => NULL,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_future_events');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_future_events');
    }

}