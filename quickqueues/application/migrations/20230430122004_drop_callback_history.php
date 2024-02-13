<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_qq_callback_history extends CI_Migration 
{

    public function up()
    {
        $this->down();
    }

    public function down()
    {
        // Drop the qq_callback_history table if it exists
        if ($this->db->table_exists('qq_callback_history')) {
            $this->dbforge->drop_table('qq_callback_history');
        }
    }
}
