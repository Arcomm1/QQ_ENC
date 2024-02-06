<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Agent_model.php - Quickqueues agent abstraction */


class Agent_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        $this->_soft_delete = true;
        parent::__construct();
        $this->_queue_agents_table  = $this->_table_prefix."queue_agents";
        $this->_settings_table      = $this->_table_prefix."agent_settings";
        $this->_user_agents_table   = $this->_table_prefix."user_agents";
        $this->_last_call_table     = $this->_table_prefix."agent_last_call";

    }


    /**
     * Create new agent
     *
     * @param array $params Row data
     * @return int ID of the new row
     */
    function create($params = false)
    {
        if (!$params || !is_array($params) || count($params) == 0) {
            return 0;
        }
        if (count($this->_required_fields) > 0) {
            foreach ($this->_required_fields as $field) {
                if (!array_key_exists($field, $this->_required_fields)) {
                    return 0;
                }
            }
        }
        $this->db->insert($this->_table, $params);
        $id = $this->db->insert_id();
        $this->set_default_settings($id);
        return $id;
    }

    /* Create new agent modified tp prevent duplicates
	function create($params = false)
	{
		if (!$params || !is_array($params) || count($params) == 0) {
			return 0; // Indicate failure or invalid input
		}

		// Ensure all required fields are present
		foreach ($this->_required_fields as $field) {
			if (!array_key_exists($field, $params)) {
				return 0; // Missing a required field, return failure
			}
		}

		// Check if a record with the same name already exists
		if (isset($params['name'])) {
			$this->db->from($this->_table);
			$this->db->where('name', $params['name']);
			$existingCount = $this->db->count_all_results();
			if ($existingCount > 0) {
				// A record with this name already exists, do not create a new one.
				return 0; // or another value to indicate duplication
			}
		} else {
			// 'name' is required to check for existence, if not provided, handle error
			return 0; // Indicate failure or missing name
		}

		// Proceed with inserting the new record as no duplicate name exists
		$this->db->insert($this->_table, $params);
		$id = $this->db->insert_id();
		$this->set_default_settings($id); // Set default settings for the new record, if necessary
		return $id;
	}
    */


    /**
     * Set extension for specific agent
     *
     * @param string $id Agent ID
     * @return bool True on success, false otherwise
     */
    public function set_extension($id = false)
    {
        if (!$id) {
            return false;
        }
        $agent = $this->get($id);
        if (!$agent) {
            return false;
        }
        $this->db->limit(1);
        $ast_user = $this->db->get_where('users', array('name' => $agent->name))->row();
        if (!$ast_user) {
            return false;
        }
        $res = $this->update($id, array('extension' => $ast_user->extension));
        if ($res != 1) {
            return false;
        }
        return true;
    }


    /**
     * Set primary queue to for agent
     *
     * @param int $id Agent ID
     * @param int $queue_id Queue ID
     * @param bool $force If set to false, update is not permitted, only setting new value will work
     * @return bool
     */
    public function add_primary_queue($id = false, $queue_id = false, $force = false)
    {
        if (!$id || !$queue_id) {
            return false;
        }

        $agent = $this->get($id);
        if (!$agent) {
            return false;
        }

        if (!$force) {
            if ($agent->primary_queue_id) {
                return false;
            }
            $this->update($id, array('primary_queue_id' => $queue_id));
            return true;
        }

        $this->update($id, array('primary_queue_id' => $queue_id));
        return true;

    }


    /**
     * Get list of queues agent is associated to
     *
     * @param int $id Agent ID
     * @return bool|array List of queues, false on error
     */
    public function get_queues($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        $queues = array();
        $qids = $this->db->get($this->_queue_agents_table);
        foreach ($qids->result() as $qid) {
            $queues[] = $this->Queue_model->get($qid->queue_id);
        }
        return $queues;
    }


    /**
     * Get list of queue IDs agent is associated to
     *
     * @param int $id Agent ID
     * @return bool|array List of queue IDs, false on error
     */
    public function get_queue_ids($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        $queues = array();
        $qids = $this->db->get($this->_queue_agents_table);
        foreach ($qids->result() as $qid) {
            $queues[] = $qid->queue_id;
        }
        return $queues;
    }

    public function get_hourly_stats_for_agents($id)
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $stats = $this->Call_model->get_queue_stats_for_agent_page($id, $date_range);

        // Calculate avg_holdtime for each item in $stats
        foreach ($stats as $s) {
            $s->avg_holdtime = ceil(($s->total_holdtime + $s->total_waittime) == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / ($s->calls_answered + $s->calls_unanswered));
        }

        // Create an array to hold sorted stats
        $sortedStats = [];

        // Iterate through hours (0 to 23) and add items to $sortedStats
        for ($i = 0; $i < 24; $i++) {
            $has = false;
            foreach ($stats as $s) {
                if (intval($s->hour) == $i) {
                    $has = true;
                    // Add item to $sortedStats with leading zeros for hour
                    $s->hour = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $sortedStats[] = $s;
                    break; // No need to continue searching for this hour
                }
            }
            if (!$has) {
                // Create a new item for missing hours
                $newTime = new stdClass();
                $newTime->hour = str_pad($i, 2, "0", STR_PAD_LEFT);
                $newTime->calls_answered = 0;
                $newTime->calls_unanswered = 0;
                $newTime->incoming_total_calltime = 0;
                $newTime->calls_outgoing_answered = 0;
                $newTime->calls_outgoing_unanswered = 0;
                $newTime->outgoing_total_calltime = 0;
                $newTime->total_holdtime = 0;
                $newTime->total_waittime = 0;
                $newTime->avg_holdtime = 0;

                $sortedStats[] = $newTime;
            }
        }

        // Sort $sortedStats by hour with leading zeros
        usort($sortedStats, function ($a, $b) {
            return strcmp($a->hour, $b->hour);
        });

        $this->r->status = 'OK';
        $this->r->message = 'Call distribution data will follow';
        $this->r->data = $sortedStats;

        $this->_respond();
    }


    /**
     * Update agent settings
     *
     * @param int $id Agent ID
     * @param string $name configuration item name
     * @param string $value configuration item value
     * @return bool True on success, false on fail
     */
    public function update_settings($id = false, $name = false, $value = false)
    {
        if (!$id || !$name || !$value) {
            return false;
        }

        $this->db->where('agent_id', $id);
        $this->db->where('name', $name);

        $this->db->update(
            $this->_settings_table,
            array(
                'value' => $value
            )
        );

        return true;
    }


    /**
     * Set all settings for specific agent
     *
     * This will overwrite any current agent settings
     *
     * @param int $id Agent ID
     * @return bool True on success, false on fail
     */
    public function set_default_settings($id = false)
    {
        if (!$id) {
            return false;
        }

        $this->db->where('agent_id', $id);
        $this->db->delete($this->_settings_table);

        $defaults = $this->Config_model->get_many_by(
            'name',
            array(
                'agent_work_start_time',
                'agent_work_end_time',
                'agent_max_pause_time',
                'agent_call_restrictions',
            )
        );

        foreach ($defaults as $d) {
            $this->db->insert(
                $this->_settings_table,
                array(
                    'agent_id'  => $id,
                    'name'      => $d->name,
                    'value'     => $d->value
                )
            );
        }
    }


    /**
     * Get settings for specific agent
     *
     * @param int $id Agent ID
     * @return mixed false on error, array of setting items on success
     */
    public function get_settings($id = false)
    {
        if (!$id) {
            return false;
        }

        $settings = array();

        $this->db->where('agent_id', $id);
        foreach ($this->db->get($this->_settings_table)->result() as $s) {
            $settings[$s->name] = $s;
        }
        return $settings;
    }


    /**
     * Check if agent is associated with any user
     *
     * @param int $id Agent ID
     * @return bool, True if agent is associated with user, false otherwise
     */
    public function has_user($id = false)
    {
        if (!$id) {
            return false;
        }

        $ua = $this->db->get_where($this->_user_agents_table, array('agent_id' => $id));
        foreach ($ua->result() as $u) {
            $a = $this->User_model->get($u->user_id);
            if ($a->role == 'agent') {
                return true;
            }
            unset($a);
        }
        return false;
    }


    /**
     * Retrun number of days agent had at least one call from date range
     *
     * @param int $id Agent ID
     * @param string $date_gt Start date
     * @param string $date_lt End date
     */
    public function count_days_with_calls($id = false, $date_gt = QQ_TODAY_START, $date_lt = QQ_TODAY_END)
    {
        if (!$id) {
            return false;
        }

        $this->db->select('count(*)');
        $this->db->where('agent_id', $id);
        $this->db->where('date >', $date_gt);
        $this->db->where('date <', $date_lt);
        $this->db->group_by('DAY(date)');
        return $this->db->get('qq_calls')->num_rows();
    }


    /**
     * Update agent last call
     *
     * @param int $id Agent ID
     * @param string $uniqueid Unique ID of call
     * @param string $src Caller number
     * @return bool True/False, based on success
     */
    public function update_last_call($id = false, $uniqueid = false, $src = false)
    {
        if (!$id || !$uniqueid || !$src) {
            return false;
        }

        $sql = "INSERT INTO $this->_last_call_table (agent_id, uniqueid, src) ";
        $sql .= "VALUES('$id', '$uniqueid', '$src') ON DUPLICATE KEY UPDATE ";
        $sql .= "uniqueid = '$uniqueid', src = '$src'";
        $this->db->query($sql);

        return true;
    }


    /**
     * Get last call for agent
     *
     * @param int $id Agent ID
     * @return bool|obj Object containing last call data, false on error
     */
    public function get_last_call($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        return $this->db->get($this->_last_call_table)->row();
    }


    /**
     * Get last call for agent
     *
     * @param int $id Agent ID
     * @return bool|obj Object containing last call data, false on error
     */
    public function vendoo_get_last_call($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        // die($this->db->get_compiled_select());
        $result = $this->db->get($this->_last_call_table)->row();

        if (!$result) {
            return array();
        }

        $call = $this->Call_model->get_one_by_complex(
            array(
                'uniqueid' => $result->uniqueid,
                'event_type' => array('COMPLETEAGENT', 'COMPLETECALLER')
            )
        );
        // If call data was modified through future event, we must return full
        // call information to show it afterwards, otherwise just return the result from
        // last_call table
        return $call ? $call : $result;
    }


}
