<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_qq_agents_archived_last_call extends CI_Migration {

    public function up()
    {
        // Check if the 'last_call' column does not exist in 'qq_agents_archived' table
        if (!$this->db->field_exists('last_call', 'qq_agents_archived')) {
            // Define the field
            $fields = array(
                'last_call' => array(
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'extension' // Specify after which column you want to add 'last_call'
                ),
            );
            // Add the 'last_call' column
            $this->dbforge->add_column('qq_agents_archived', $fields);
        }
    }

    public function down()
    {
        // Check if the 'last_call' column exists
        if ($this->db->field_exists('last_call', 'qq_agents_archived')) {
            // Drop the 'last_call' column
            $this->dbforge->drop_column('qq_agents_archived', 'last_call');
        }
    }
}
