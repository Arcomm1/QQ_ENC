<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_calltype_to_calls extends CI_Migration {

    public function up()
    {
        // Check if the column 'calltype' already exists in the 'qq_calls' table
        if (!$this->db->field_exists('calltype', 'qq_calls')) {
            $fields = array(
                'calltype' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 10, // Updated constraint to 10
                    'null' => TRUE, // To allow NULL values
                )
            );

            $this->dbforge->add_column('qq_calls', $fields);
        }
    }
}
