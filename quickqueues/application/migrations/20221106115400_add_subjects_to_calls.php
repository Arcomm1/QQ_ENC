<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_subjects_to_calls extends CI_Migration {

    public function up()
    {
        // Check if columns do not exist before adding them
        if (!$this->db->field_exists('subject_family', 'qq_calls')) {
            $this->dbforge->add_column('qq_calls', [
                'subject_family' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'default' => NULL,
                ]
            ]);
        }
        
        if (!$this->db->field_exists('subject_comment', 'qq_calls')) {
            $this->dbforge->add_column('qq_calls', [
                'subject_comment' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'default' => NULL,
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('subject_family', 'qq_calls')) {
            $this->dbforge->drop_column('qq_calls', 'subject_family');
        }

        if ($this->db->field_exists('subject_comment', 'qq_calls')) {
            $this->dbforge->drop_column('qq_calls', 'subject_comment');
        }
    }
}
