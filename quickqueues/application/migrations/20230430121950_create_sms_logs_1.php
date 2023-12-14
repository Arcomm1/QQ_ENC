<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_sms_logs_1 extends CI_Migration 
{

    public function up()
    {
        
        $data[] = array(
            'id'          => NULL,
        );

        $this->db->insert_batch('qq_sms_logs', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_sms_logs');
    }
}
