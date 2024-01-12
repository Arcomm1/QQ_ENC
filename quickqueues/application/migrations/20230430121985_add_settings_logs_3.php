<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_settings_logs_3 extends CI_Migration 
{

    public function up()
    {
        $fields = array (
            'resolution' => array(
                'type'       => 'VARCHAR',
            ),
        );

        $this->dbforge->add_column('qq_settings_logs', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_settings_logs','resolution');
    }
}
