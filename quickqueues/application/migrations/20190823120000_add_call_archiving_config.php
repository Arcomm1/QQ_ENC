<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_call_archiving_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_archive_calls',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_archive_calls_older_than',
            'value' => '90',
            'default' => '90',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_archive_calls_action',
            'value' => 'move',
            'default' => 'move',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_archive_calls_dest',
            'value' => '/var/BACKUP/monitor',
            'default' => '/var/BACKUP/monitor',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_older_than'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_action'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_dest'");

        
    }

}