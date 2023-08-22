<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_associated_agent_to_users extends CI_Migration {


    public function up()
    {
        $fields = array(
            'associated_agent_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            )
        );
        $this->dbforge->add_column('qq_users', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_users', 'associated_agent_id');
    }

}
