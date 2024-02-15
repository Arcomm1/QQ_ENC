<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_settings_logs_3 extends CI_Migration 
{
    public function up()
    {
        // Check if the column 'resolution' does not exist before attempting to add it
        if (!$this->db->field_exists('resolution', 'qq_settings_logs')) {
            $fields = array (
                'resolution' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
            );

            $this->dbforge->add_column('qq_settings_logs', $fields);
        }
    }

    public function down()
    {
        // It's safe to check for the column existence before trying to drop it
        // However, `drop_column` doesn't throw an error if the column doesn't exist
        // So, the check here is more about consistency with the `up` method logic
        if ($this->db->field_exists('resolution', 'qq_settings_logs')) {
            $this->dbforge->drop_column('qq_settings_logs', 'resolution');
        }
    }
}
