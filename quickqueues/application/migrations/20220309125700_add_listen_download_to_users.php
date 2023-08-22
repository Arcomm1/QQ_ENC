<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_listen_download_to_users extends CI_Migration {


    public function up()
    {
        $fields = array(
            'can_listen' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'yes',
            ),
            'can_download' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'yes',
            )
        );
        $this->dbforge->add_column('qq_users', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_users', 'can_listen');
        $this->dbforge->drop_column('qq_users', 'can_download');

    }

}
