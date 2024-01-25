<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_field_to_config_2 extends CI_Migration 
{

    public function up()
    {
        $data = array(
            array(
                'name' => 'queue_log_rollback_with_deletion',
                'value' => 'no',
                'default' => 'no',
                'category' => 'application',
            ),
        );

        $this->db->insert_batch('qq_config', $data);
    }

}