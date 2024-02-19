<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* User_model.php - Quickqueues user abstraction */


class User_model extends MY_Model {

    

    public function __construct()
    {
        $this->_reqquired_fields = array('name', 'password', 'role', 'enabled');
        $this->_table_prefix = 'qq_';
        parent::__construct();
        $this->_user_agents_table = 'qq_user_agents';
        $this->_user_queues_table = 'qq_user_queues';

    }
    /**
     * Get all queues for specific user
     *
     * @param string $id User ID
     * @return array Array containing queues
     */
    public function get_queues($id = false)
    {
        if (!$id) {
            return array();
        }
        $user = $this->User_model->get($id);
        if ($id == 1 || $user->role == 'admin') 
		{
            return $this->Queue_model->get_all();
        }
        $queues = array();

        if ($user->role == 'manager') 
		{
            $this->db->where('user_id', $id);
            $qids = $this->db->get($this->_user_queues_table);
            foreach ($qids->result() as $qid)
			{
                $queues[] = $this->Queue_model->get($qid->queue_id);
            }
            return $queues;
        }

        if ($user->role == 'agent')
		{
            return $this->Agent_model->get_queues($user->associated_agent_id);
        }

    }


    /**
     * Get all agents for specific user
     *
     * @param string $id User ID
     * @return array Array containing agents
     */
    public function get_agents($id = false)
    {
        if (!$id) {
            return array();
        }
        $user = $this->User_model->get($id);

        if ($id == 1 || $user->role == 'admin') {
            return $this->Agent_model->get_all('extension', 'ASC');
        }

        if ($user->role == 'manager') {
            $agent_ids = array();
            $agents = array();
            $this->db->where('user_id', $id);
            $qids = $this->db->get($this->_user_queues_table);
            foreach ($qids->result() as $qid) {
                $queue_agents = $this->Queue_model->get_agents($qid->queue_id);
                foreach ($queue_agents as $qa) {
					if (is_object($qa) && isset($qa->id)) {
						$agent_ids[] = $qa->id;
						if ($qa->deleted == '0') {
							$agents[$qa->id] = $qa;
						}
					}
                }
            }
            return $agents;
        }

        if ($user->role == 'agent') {
            $agent_queues = $this->Agent_model->get_queues($user->associated_agent_id);
            foreach ($agent_queues as $q) {
                $queue_agents = $this->Queue_model->get_agents($q->id);
                foreach ($queue_agents as $qa) {
                    if (is_object($qa) && $qa->deleted == '0') {
                        $agents[$qa->id] = $qa;
                    }
                }
            }
            return $agents;
        }

    }


    public function activate_or_deactivate($id = false)
    {
        if (!$id) {
            return false;
        }
        $user = $this->get($id);
        if (!$user) {
            return false;
        }

        $user->enabled == 'yes' ? $n = 'no' : $n = 'yes';

        $s = $this->update($id, array('enabled' => $n));
        if ($s == 0) {
            return false;
        } else {
            return true;
        }

    }


    public function assign_queue($id = false, $queue_id = false)
    {
        if (!$id || !$queue_id) {
            return false;
        }

        $q = 'INSERT IGNORE INTO '.$this->_user_queues_table.' (user_id, queue_id) ';
        $q .= 'VALUES('.$id.','.$queue_id.');';
        $this->db->query($q);
        return true;
    }


    public function unassign_queue($id = false, $queue_id = false)
    {
        if (!$id || !$queue_id) {
            return false;
        }

        $this->db->where('user_id', $id);
        $this->db->where('queue_id', $queue_id);
        $this->db->delete($this->_user_queues_table);
        return true;
    }


    public function assign_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            return false;
        }

        $q = 'INSERT IGNORE INTO '.$this->_user_agents_table.' (user_id, agent_id) ';
        $q .= 'VALUES('.$id.','.$agent_id.');';
        $this->db->query($q);
        return true;
    }


    public function unassign_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            return false;
        }

        $this->db->where('user_id', $id);
        $this->db->where('agent_id', $agent_id);
        $this->db->delete($this->_user_agents_table);
        return true;
    }


    public function delete_completely($id = false)
    {
        if (!$id) {
            return 0;
        }

        // Delete user agents
        $this->db->where('user_id', $id);
        $this->db->delete($this->_user_agents_table);

        // Delete user queues
        $this->db->where('user_id', $id);
        $this->db->delete($this->_user_queues_table);

        // Delete user logs

        return $this->delete($id);
        $this->User_log_model->delete_by('user_id', $id);
    }


    public function find_name_or_email($username)
    {
        $this->db->select('email, id');
        $this->db->from($this->_table);
        $this->db->where('name', $username);
        $this->db->or_where('email', $username);
        $users_email=$this->db->get();

        return $users_email->row_array();
    }





}
