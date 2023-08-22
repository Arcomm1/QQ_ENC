<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_tickets extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'author_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'owner_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'description' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'department_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'category_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'subcategory_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'number' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                ),
                'customer_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
            )
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('number', true);
        $this->dbforge->add_key('author_id', true);
        $this->dbforge->add_key('owner_id', true);
        $this->dbforge->create_table('qq_tickets');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_tickets');

    }

}
