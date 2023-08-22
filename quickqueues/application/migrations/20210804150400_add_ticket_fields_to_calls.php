<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_ticket_fields_to_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'ticket_department_id' => array(
                'type' => 'INT',
                'constraint' => 30,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );
        $this->dbforge->add_column('qq_calls', $fields);

        $fields = array(
            'ticket_category_id' => array(
                'type' => 'INT',
                'constraint' => 30,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );
        $this->dbforge->add_column('qq_calls', $fields);

        $fields = array(
            'ticket_subcategory_id' => array(
                'type' => 'INT',
                'constraint' => 30,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );
        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_calls', 'ticket_department_id');
        $this->dbforge->drop_column('qq_calls', 'ticket_category_id');
        $this->dbforge->drop_column('qq_calls', 'ticket_subcategory_id');

    }

}
