<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_sensible_defaults extends CI_Migration {


    public function up()
    {
        $this->db->query("UPDATE qq_config SET `value` = 'yes', `default` = 'yes' WHERE name = 'app_track_outgoing'");
        $this->db->query("UPDATE qq_config SET `value` = '480', `default` = '480' WHERE name = 'app_auto_mark_called_back'");
        $this->db->query("UPDATE qq_config SET `value` = 'yes', `default` = 'yes' WHERE name = 'app_track_called_back_calls'");
        $this->db->query("UPDATE qq_config SET `value` = '120', `default` = '120' WHERE name = 'app_mark_answered_elsewhere'");
        $this->db->query("UPDATE qq_config SET `value` = '120', `default` = '120' WHERE name = 'app_track_duplicate_calls'");
        $this->db->query("UPDATE qq_config SET `value` = 'no', `default` = 'no' WHERE name = 'app_track_agent_pause_time'");
        $this->db->query("UPDATE qq_config SET `value` = 'no', `default` = 'no' WHERE name = 'app_track_agent_session_time'");
        $this->db->query("UPDATE qq_config SET `value` = 'no', `default` = 'no' WHERE name = 'app_archive_calls'");
        $this->db->query("UPDATE qq_config SET `value` = 'georgian', `default` = 'georgian' WHERE name = 'app_language'");
    }

    public function down()
    {
        $this->db->query("UPDATE qq_config SET `value` = 'no', `default` = 'no' WHERE name = 'app_track_outgoing'");
        $this->db->query("UPDATE qq_config SET `value` = '0', `default` = '0' WHERE name = 'app_auto_mark_called_back'");
        $this->db->query("UPDATE qq_config SET `value` = 'no', `default` = 'no' WHERE name = 'app_track_called_back_calls'");
        $this->db->query("UPDATE qq_config SET `value` = '0', `default` = '0' WHERE name = 'app_mark_answered_elsewhere'");
        $this->db->query("UPDATE qq_config SET `value` = '0', `default` = '0' WHERE name = 'app_track_duplicate_calls'");
        $this->db->query("UPDATE qq_config SET `value` = 'yes', `default` = 'yes' WHERE name = 'app_track_agent_pause_time'");
        $this->db->query("UPDATE qq_config SET `value` = 'yes', `default` = 'yes' WHERE name = 'app_track_agent_session_time'");
        $this->db->query("UPDATE qq_config SET `value` = 'yes', `default` = 'yes' WHERE name = 'app_archive_calls'");
        $this->db->query("UPDATE qq_config SET `value` = 'english', `default` = 'english' WHERE name = 'app_language'");

    }

}
