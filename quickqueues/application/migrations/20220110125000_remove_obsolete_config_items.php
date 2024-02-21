<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_obsolete_config_items extends CI_Migration {

    public function up()
    {
        // Config items deletion
        $configItems = [
            'app_application_name',
            'app_track_incoming',
            'app_archive_calls',
            'app_archive_calls_older_than',
            'app_archive_calls_dest',
            'app_archive_calls_action',
            'app_time_distribution_map',
            'app_holdtime_distribution_map',
            'agent_show_other_agent_status',
            'agent_download_calls',
            'agent_listen_calls',
        ];

        foreach ($configItems as $item) {
            $this->db->query("DELETE FROM qq_config WHERE name = '{$item}'");
        }

        // Event types deletion
        $eventTypes = [
            'INC_ANSWERED',
            'INC_NOANSWER',
            'INC_BUSY',
            'INC_FAILED',
        ];

        foreach ($eventTypes as $eventType) {
            $this->db->query("DELETE FROM qq_event_types WHERE name = '{$eventType}'");
            $this->db->query("DELETE FROM qq_events WHERE event_type = '{$eventType}'");
            $this->db->query("DELETE FROM qq_calls WHERE event_type = '{$eventType}'");
        }

        // Check if the column exists before attempting to drop it
        if ($this->db->field_exists('archived', 'qq_calls')) {
            $this->dbforge->drop_column('qq_calls', 'archived');
        }
    }

    public function down()
    {
        // Rollback actions if necessary
        // This example simply returns true, indicating no rollback actions are defined
        return true;
    }
}
