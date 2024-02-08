<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_settings_logs_3 extends CI_Migration 
{
    public function up()
    {

        $fields = array (
            'id' => array(
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
        );

        $this->dbforge->add_column('qq_settings_logs', $fields);
        $this->dbforge->drop_column('qq_settings_logs', 'calls_without_service_queue_id');
        $this->dbforge->drop_column('qq_settings_logs', 'date');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_settings_logs');
    }

}
