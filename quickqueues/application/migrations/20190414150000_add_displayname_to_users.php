<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_displayname_to_users extends CI_Migration {


    public function up()
    {
        $fields = array(
            'display_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            )
        );
        $this->dbforge->add_column('qq_users', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_users', 'display_name');
    }

}
