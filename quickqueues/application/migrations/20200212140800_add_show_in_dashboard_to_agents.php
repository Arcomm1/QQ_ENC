<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_show_in_dashboard_to_agents extends CI_Migration {


    public function up()
    {
        $fields = array(
            'show_in_dashboard' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'yes',
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'show_in_dashboard');
    }

}
