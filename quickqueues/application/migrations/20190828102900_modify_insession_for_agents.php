<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_modify_insession_for_agents extends CI_Migration {


    public function up()
    {
        $this->dbforge->drop_column('qq_agents', 'in_session');
        $fields = array(
            'in_session' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'in_session');
        $fields = array(
            'in_session' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);
    }

}
