<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_introduce_ivr_abandon extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'IVRABANDON',
            'has_calls' => 'yes',
        );

        $this->db->insert_batch('qq_event_types', $data);
        unset($data);

        $data[] = array(
            'name' => 'app_track_ivr_abandon',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name = 'IVRABANDON'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_ivr_abandon'");
    }

}