<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_custom_time_intervals extends CI_Migration {


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
                'type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'description' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'user_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_custom_time_intervals');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_custom_time_intervals');
    }
}
