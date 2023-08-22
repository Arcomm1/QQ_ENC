<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_insession_to_agents extends CI_Migration {


    public function up()
    {
        $fields = array(
            'in_session' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'in_session');
    }

}
