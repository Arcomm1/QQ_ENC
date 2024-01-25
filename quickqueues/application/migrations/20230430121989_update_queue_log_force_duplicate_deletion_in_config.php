<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_queue_log_force_duplicate_deletion_in_config extends CI_Migration 
{

    public function up()
    {
        $data = array(
            'value' => 'no',
            'default' => 'no',
        );

        $this->db->where('name', 'queue_log_force_duplicate_deletion');
        $this->db->update('qq_config', $data);
    }

}
