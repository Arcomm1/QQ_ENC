<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_fields_to_config extends CI_Migration 
{

    public function up()
    {
        $data = array(
            array(
                'name' => 'queue_log_rollback',
                'value' => 'no',
                'default' => 'no',
                'category' => 'application',
            ),
            array(
                'name' => 'queue_log_rollback_days',
                'value' => '1',
                'default' => '1',
                'category' => 'application',
            ),
            array(
                'name' => 'queue_log_force_duplicate_deletion',
                'value' => 'yes',
                'default' => 'yes',
                'category' => 'application',
            ),
        );

        $this->db->insert_batch('qq_config', $data);
    }

}