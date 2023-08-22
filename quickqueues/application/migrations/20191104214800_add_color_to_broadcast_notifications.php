<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_color_to_broadcast_notifications extends CI_Migration {


    public function up()
    {
        $fields = array(
            'color' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'danger',
            )
        );
        $this->dbforge->add_column('qq_broadcast_notifications', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_broadcast_notifications', 'color');
    }

}
