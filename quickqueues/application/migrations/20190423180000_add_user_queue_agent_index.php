<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_user_queue_agent_index extends CI_Migration {


    public function up()
    {
        $query_user_queues = "ALTER TABLE qq_user_queues ADD CONSTRAINT user_queues UNIQUE KEY (user_id, queue_id);";
        $query_user_agents = "ALTER TABLE qq_user_agents ADD CONSTRAINT user_agents UNIQUE KEY (user_id, agent_id);";
        $this->db->query($query_user_queues);
        $this->db->query($query_user_agents);
    }

    public function down()
    {
        $query_user_queues = "ALTER TABLE qq_user_queues DROP INDEX user_queues;";
        $query_user_agents = "ALTER TABLE qq_user_agents DROP INDEX user_agents;";
        $this->db->query($query_user_queues);
        $this->db->query($query_user_agents);
    }

}
