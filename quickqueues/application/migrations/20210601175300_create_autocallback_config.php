<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_autocallback_config extends CI_Migration {

    public function up()
    {
        $this->db->query("TRUNCATE qq_queue_config");

        $data[] = array(
            'name'      => 'queue_auto_callback_enable',
            'value'     => 'no',
            'default'   => 'no',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_auto_callback_dst',
            'value'     => 'qq-auto-callback',
            'default'   => 'qq-auto-callback',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_auto_callback_conditions',
            'value'     => 'free',
            'default'   => 'ignore',
            'category'  => 'queue',
        );

        $this->db->insert_batch('qq_config', $data);

        echo "Please don't forget to run tools reset_queue_config\n";
        echo "WARNING: This is backwards incompatible change!\n";

    }

    public function down()
    {
        $this->db->query("DELETE FROM qq_config WHERE name = 'queue_auto_callback_enable'");
        $this->db->query("DELETE FROM qq_config WHERE name = 'queue_auto_callback_dst'");
        $this->db->query("DELETE FROM qq_config WHERE name = 'queue_auto_callback_conditions'");
    }

}
