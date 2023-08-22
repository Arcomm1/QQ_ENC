<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_primary_queue_to_agents extends CI_Migration {


    public function up()
    {
        $fields = array(
            'primary_queue_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
            )
        );
        $this->dbforge->add_column('qq_agents', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('qq_agents', 'primary_queue_id');
    }

}
