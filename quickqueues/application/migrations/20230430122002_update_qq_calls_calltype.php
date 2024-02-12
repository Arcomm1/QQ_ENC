<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_qq_calls_calltype extends CI_Migration {

    public function up()
    {
        // Common SRC condition for all updates
        $srcCondition = "(src IN (SELECT extension FROM qq_agents) 
                          OR src IN (SELECT extension FROM users) 
                          OR src IN (SELECT extension FROM qq_agents_archived))";
        
        // Update to 'local'
        $this->db->query("
            UPDATE qq_calls 
            SET call_type = 'local' 
            WHERE 
                $srcCondition
            AND 
                (dst IN (SELECT extension FROM qq_agents) 
                OR dst IN (SELECT extension FROM users) 
                OR dst IN (SELECT extension FROM qq_agents_archived))
        ");

        // Update to 'local_abandoned' (Note: src condition is already included, dst condition adapted for consistency)
        $this->db->query("
            UPDATE qq_calls 
            SET call_type = 'local_abandoned' 
            WHERE 
                $srcCondition
                AND (dst IS NULL OR dst = '') 
                AND event_type = 'ABANDON' 
                AND agent_id = 0
        ");

        // Update to 'local_queue'
        $this->db->query("
            UPDATE qq_calls 
            SET call_type = 'local_queue' 
            WHERE 
                $srcCondition
                AND dst IN (SELECT extension FROM queues_config)
        ");
        
        // Update to 'local_fcode'
        $this->db->query("
            UPDATE qq_calls 
            SET call_type = 'local_fcode' 
            WHERE 
                $srcCondition
                AND dst LIKE '%*%'
        ");
    }

    public function down()
    {
        // Optionally, add logic to revert the changes made in this migration.
        // Reverting data updates can be complex and depends on the original state of your data.
    }
}
