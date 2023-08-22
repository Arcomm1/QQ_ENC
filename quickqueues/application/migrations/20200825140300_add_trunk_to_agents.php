<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_trunk_to_agents extends CI_Migration {


    public function up()
    {
        $fields = array(
            'trunk' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => null,
                'null' => true,
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'trunk');
    }

}
