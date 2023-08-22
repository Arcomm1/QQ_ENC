<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_dialout_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'DIALOUTATTEMPT',
            'has_calls' => 'no',
        );
        $data[] = array(
            'name' => 'DIALOUTFAILED',
            'has_calls' => 'no',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name = 'DIALOUTATTEMPT'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'DIALOUTFAILED'");
    }

}
