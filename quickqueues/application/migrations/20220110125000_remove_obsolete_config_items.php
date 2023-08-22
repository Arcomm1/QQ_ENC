<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_remove_obsolete_config_items extends CI_Migration {


    public function up()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_application_name'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_track_incoming'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_older_than'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_dest'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_archive_calls_action'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_time_distribution_map'");
        $this->db->query("DELETE from qq_config WHERE name = 'app_holdtime_distribution_map'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_show_other_agent_status'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_download_calls'");
        $this->db->query("DELETE from qq_config WHERE name = 'agent_listen_calls'");

        $this->db->query("DELETE from qq_event_types WHERE name = 'INC_ANSWERED'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'INC_NOANSWER'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'INC_BUSY'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'INC_FAILED'");

        $this->db->query("DELETE from qq_events WHERE event_type = 'INC_ANSWERED'");
        $this->db->query("DELETE from qq_events WHERE event_type = 'INC_NOANSWER'");
        $this->db->query("DELETE from qq_events WHERE event_type = 'INC_BUSY'");
        $this->db->query("DELETE from qq_events WHERE event_type = 'INC_FAILED'");

        $this->db->query("DELETE from qq_calls WHERE event_type = 'INC_ANSWERED'");
        $this->db->query("DELETE from qq_calls WHERE event_type = 'INC_NOANSWER'");
        $this->db->query("DELETE from qq_calls WHERE event_type = 'INC_BUSY'");
        $this->db->query("DELETE from qq_calls WHERE event_type = 'INC_FAILED'");

        $this->dbforge->drop_column('qq_calls', 'archived');
    }


    public function down()
    {
        return true();
    }

}
