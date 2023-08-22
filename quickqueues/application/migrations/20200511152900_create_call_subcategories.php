<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_call_subcategories extends CI_Migration {


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
                'color' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                )
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_call_subcategories');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_call_subcategories');
    }
}
