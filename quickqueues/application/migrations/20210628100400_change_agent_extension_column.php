<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_change_agent_extension_column extends CI_Migration {


    public function up()
    {
        $fields = array(
                'extension' => array(
                        'name' => 'extension',
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                ),
        );
        $this->dbforge->modify_column('qq_agents', $fields);

    }


    public function down()
    {
        $fields = array(
            'extension' => array(
                    'name' => 'extension',
                    'type' => 'INT',
                    'constraint' => 10,
            ),
    );
    $this->dbforge->modify_column('qq_agents', $fields);
    }

}
