<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_duplicate_record_to_calls extends CI_Migration {

    public function up()
    {
        // Check if the 'duplicate_record' column already exists in the 'qq_calls' table
        if (!$this->db->field_exists('duplicate_record', 'qq_calls')) {
            $fields = array(
                'duplicate_record' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => NULL
                )
            );

            $this->dbforge->add_column('qq_calls', $fields);
        }
    }
}
