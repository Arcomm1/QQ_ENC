<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_settings_logs extends CI_Migration 
{
    public function up()
    {
        // Check if the table does not exist before attempting to create it
        if (!$this->db->table_exists('qq_settings_logs')) {
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ),
                'sms_content' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'sms_token' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'sms_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'queue_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'call_overload'=> array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'default' => 0,
                ),
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('qq_settings_logs');
        } else {
            echo "The table 'qq_settings_logs' already exists.";
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_settings_logs', TRUE);
    }
}
