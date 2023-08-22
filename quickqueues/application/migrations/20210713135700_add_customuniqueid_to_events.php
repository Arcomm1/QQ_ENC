<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_customuniqueid_to_events extends CI_Migration {


    public function up()
    {
        $fields = array(
            'custom_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => 30,
                'unsigned' => true,
                'null' => true,
                'default' => NULL,
            )
        );

        $this->dbforge->add_column('qq_events', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_events', 'custom_uniqueid');
    }

}
