<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_product_information extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'nickname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'company_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'company_email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'company_phone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'system_details' => array(
                    'type' => 'TEXT',
                ),
                'last_verified' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'registration_message' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
            )
        );
        $this->dbforge->create_table('qq_product_information');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_product_information');
    }
}
