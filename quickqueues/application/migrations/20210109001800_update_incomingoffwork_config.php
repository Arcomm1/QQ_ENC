<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_update_incomingoffwork_config extends CI_Migration {


    public function up()
    {
        $this->db->query("UPDATE qq_event_types SET `has_calls` = 'yes'  WHERE name = 'INCOMINGOFFWORK'");
    }

    public function down()
    {
        $this->db->query("UPDATE qq_event_types SET `has_calls` = 'no'  WHERE name = 'INCOMINGOFFWORK'");
    }

}
