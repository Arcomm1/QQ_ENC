<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_session_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'STARTSESSION',
            'has_calls' => 'no',
        );

        $data[] = array(
            'name' => 'STOPSESSION',
            'has_calls' => 'no',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name = 'STARTSESSION'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'STOPSESSION'");

    }

}
