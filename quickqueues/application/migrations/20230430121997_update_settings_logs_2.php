<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_settings_logs_2 extends CI_Migration 
{
    public function up()
    {

        $this->dbforge->drop_column('qq_settings_logs', 'queue_id');
        $this->dbforge->drop_column('qq_settings_logs', 'sla_callbacks');
        $this->dbforge->drop_column('qq_settings_logs', 'timeout_callbacks');
        $this->dbforge->drop_column('qq_settings_logs', 'sla_calls');
        $this->dbforge->drop_column('qq_settings_logs', 'timeout_calls');
        $this->dbforge->drop_column('qq_settings_logs', 'resolution');
        $this->dbforge->drop_column('qq_settings_logs', 'data');

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_settings_logs');
    }

}
