<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_calltype_to_calls extends CI_Migration {

    public function up()
    {
        // Check if the column 'call_type' already exists in the 'qq_calls' table
        if (!$this->db->field_exists('call_type', 'qq_calls')) {
            $fields = array(
					'call_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 30, // Updated constraint to 30
                    'null' => TRUE, // To allow NULL values
                )
            );

            $this->dbforge->add_column('qq_calls', $fields);
        }
    }
}
