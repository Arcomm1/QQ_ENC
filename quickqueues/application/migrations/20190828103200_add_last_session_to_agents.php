<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_last_session_to_agents extends CI_Migration {


    public function up()
    {
        $fields = array(
            'last_session' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'last_session');
    }

}
