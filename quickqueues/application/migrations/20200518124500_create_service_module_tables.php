<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_service_module_tables extends CI_Migration {


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
        $this->dbforge->create_table('qq_services');


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
                'service_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_service_products');


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
                'service_product_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_service_product_types');


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
                'service_product_type_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_service_product_subtypes');

        $fields = array(
            'service_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL
            ),
            'service_product_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL
            ),
            'service_product_type_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL
            ),
            'service_product_subtype_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL
            ),
        );

        $this->dbforge->add_column('qq_calls', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_services');
        $this->dbforge->drop_table('qq_service_products');
        $this->dbforge->drop_table('qq_service_product_types');
        $this->dbforge->drop_table('qq_service_product_subtypes');
        $this->dbforge->drop_column('qq_calls', 'service_id');
        $this->dbforge->drop_column('qq_calls', 'service_product_id');
        $this->dbforge->drop_column('qq_calls', 'service_product_type_id');
        $this->dbforge->drop_column('qq_calls', 'service_product_subtype_id');

    }
}
