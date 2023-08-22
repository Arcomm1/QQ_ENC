
<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_Increase_status_column_constraint_in_tickets_and_calls extends CI_Migration {


    public function up()
    {
        $fields = array(
            'status' => array(
                    'name' => 'status',
                    'type' => 'VARCHAR',
                    'constraint' => 100,
            ),
        );
        $this->dbforge->modify_column('qq_calls', $fields);

        $fields = array(
            'status' => array(
                    'name' => 'status',
                    'type' => 'VARCHAR',
                    'constraint' => 100,
            ),
        );
        $this->dbforge->modify_column('qq_tickets', $fields);
    }


    public function down()
    {
        $fields = array(
            'status' => array(
                    'name' => 'status',
                    'type' => 'VARCHAR',
                    'constraint' => 20,
            ),
        );
        $this->dbforge->modify_column('qq_calls', $fields);

        $fields = array(
            'status' => array(
                    'name' => 'status',
                    'type' => 'VARCHAR',
                    'constraint' => 50,
            ),
        );
        $this->dbforge->modify_column('qq_tickets', $fields);
    }

}
