<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_outgoing_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'OUT_ANSWERED',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'OUT_NOANSWER',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'OUT_BUSY',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'OUT_FAILED',
            'has_calls' => 'yes',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name LIKE 'OUT_%'");
    }

}