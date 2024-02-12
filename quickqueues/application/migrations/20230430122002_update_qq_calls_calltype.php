<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_qq_calls_calltype extends CI_Migration {

    public function up()
    {
        // Update to 'local'
        $this->db->query("
            UPDATE qq_calls 
            SET calltype = 'local' 
            WHERE 
                (src IN (SELECT extension FROM qq_agents) 
                OR src IN (SELECT extension FROM users) 
                OR src IN (SELECT extension FROM qq_agents_archived)) 
            AND 
                (dst IN (SELECT extension FROM qq_agents) 
                OR dst IN (SELECT extension FROM users) 
                OR dst IN (SELECT extension FROM qq_agents_archived))
        ");

        // Update to 'local_abandoned'
        $this->db->query("
            UPDATE qq_calls 
            SET calltype = 'local_abandoned' 
            WHERE 
                (src IN (SELECT extension FROM users) OR src IN (SELECT extension FROM qq_agents_archived)) 
                AND (dst IS NULL OR dst = '') 
                AND event_type = 'ABANDON' 
                AND agent_id = 0
        ");

        // Update to 'local_queue'
        $this->db->query("
            UPDATE qq_calls 
            SET calltype = 'local_queue' 
            WHERE 
                src IN (SELECT extension FROM users) 
                AND dst IN (SELECT extension FROM queues_config)
        ");
		
        // Update to 'local_fcode'
        $this->db->query("
            UPDATE qq_calls 
            SET calltype = 'local_fcode' 
            WHERE 
                src IN (SELECT extension FROM users) 
                AND dst LIKE '%*%'
        ");
    }

    public function down()
    {
        // Optionally, add logic to revert the changes made in this migration.
        // This might involve setting calltype back to its original value or a default one,
        // but that depends on your application's needs and whether you can accurately revert these changes.
    }
}
