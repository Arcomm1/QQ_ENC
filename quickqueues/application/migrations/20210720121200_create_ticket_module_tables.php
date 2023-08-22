<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_ticket_module_tables extends CI_Migration {


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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_ticket_departments');

        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'department_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_ticket_categories');

        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'category_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_ticket_subcategories');

    }


    public function down()
    {
        $this->dbforge->drop_table('qq_ticket_departments');
        $this->dbforge->drop_table('qq_ticket_categories');
        $this->dbforge->drop_table('qq_ticket_subcategories');
    }


}
