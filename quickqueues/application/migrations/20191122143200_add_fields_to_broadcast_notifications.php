<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_fields_to_broadcast_notifications extends CI_Migration {


    public function up()
    {
        $fields = array(
            'deleted' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => '0',
            ),
            'creator_user_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'default' => NULL,
            ),
            'creation_date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00',
            )
        );
        $this->dbforge->add_column('qq_broadcast_notifications', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_broadcast_notifications', 'deleted');
        $this->dbforge->drop_column('qq_broadcast_notifications', 'creator_user_id');
        $this->dbforge->drop_column('qq_broadcast_notifications', 'creation_date');

    }

}
