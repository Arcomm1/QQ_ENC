<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_insert_surveyresult_event extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'SURVEYRESULT',
            'has_calls' => 'no',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name LIKE 'SURVEYRESULT'");
    }

}
