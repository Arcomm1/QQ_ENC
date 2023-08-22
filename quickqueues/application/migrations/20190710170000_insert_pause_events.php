<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_pause_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'STARTPAUSE',
            'has_calls' => 'no',
        );

        $data[] = array(
            'name' => 'STOPPAUSE',
            'has_calls' => 'no',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name = 'STARTPAUSE'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'STOPPAUSE'");

    }

}
