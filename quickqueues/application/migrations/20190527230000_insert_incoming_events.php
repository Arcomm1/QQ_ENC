<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_incoming_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'INC_ANSWERED',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'INC_NOANSWER',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'INC_BUSY',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'INC_FAILED',
            'has_calls' => 'yes',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name LIKE 'INC_%'");
    }

}
