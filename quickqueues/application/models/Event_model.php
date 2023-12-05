<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Event_model.php - Quickqueues event abstraction */


class Event_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
    }


    /**
     * Get last PAUSE related event for agent
     *
     * @param int $id Agent ID
     * @return mixed Event, or false on error
     */
    public function get_agent_last_pause_event($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        $this->db->where_in('event_type', array('STARTPAUSE', 'STOPPAUSE'));
        $this->db->order_by('id DESC');
        $this->db->limit(1);
        return $this->db->get($this->_table)->row();
    }


    /**
     * Get last session related event for agent
     *
     * @param int $id Agent ID
     * @return mixed Event, or false on error
     */
    public function get_agent_last_session_event($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where('agent_id', $id);
        $this->db->where_in('event_type', array('STARTSESSION', 'STOPSESSION'));
        $this->db->order_by('id DESC');
        $this->db->limit(1);
        return $this->db->get($this->_table)->row();
    }


    /**
     * Get multiple rows matching complex WHERE statement
     *
     * @param array $where Multidimensional array of column names and values
     * @param int $limit Limit for pagination
     * @param int $offset Offset for pagination
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_many_by_complex($where = false, $limit = false, $offset = false)
    {
        if (!$where || !is_array($where) || count($where) == 0) {
            return false;
        }

        foreach ($where as $field => $value) {

            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }

        if ($limit && $offset) {
            $this->db->limit($offset, $limit);
        }

        $this->db->order_by('id DESC');

        return $this->db->get($this->_table)->result();
    }


    public function get_stats_for_start($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
    
        $this->db->select('queue_id');
        $this->db->select('COUNT(CASE WHEN event_type = "DID" THEN 1 END) AS calls_unique');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "COMPLETE%" THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
    
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('queue_id'); // Group by queue_id to get results for each queue
    
        return $this->db->get($this->_table)->result();
    }
    
    public function get_agent_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('agent_id');
        $this->db->select('COUNT(CASE WHEN event_type = "RINGNOANSWER" AND qq_events.ringtime > 1 THEN 1 END) AS calls_missed');
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('agent_id');
        return $this->db->get($this->_table)->result();
    }


    public function get_agent_pause_stats_for_start_page($date_range = array())
    {
        if (count($date_range) == 0) {
            return false;
        }
        $this->db->select('agent_id');
        $this->db->select('SUM(IF(event_type = "STOPPAUSE", pausetime, 0)) AS total_pausetime');
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->where('pausetime <', 25000);
        $this->db->group_by('agent_id');
        return $this->db->get($this->_table)->result();
    }


    public function get_agent_stats_for_agent_stats_page($agent_id = array(), $date_range = array())
    {
        if (!$agent_id|| count($date_range) == 0) {
            return false;
        }
        $this->db->select('agent_id');
        $this->db->select('COUNT(CASE WHEN event_type = "RINGNOANSWER" AND qq_events.ringtime > 1 THEN 1 END) AS calls_missed');
        $this->db->select('SUM(IF(event_type = "STOPPAUSE", pausetime, 0)) AS total_pausetime');
        $this->db->where_in('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        return $this->db->get($this->_table)->row();
    }

    public function get_agent_hourly_stats_for_agent_stats_page($agent_id = array(), $date_range = array())
    {
        if (!$agent_id || count($date_range) == 0) 
        {
            return false;
        }

        $this->db->select('DATE_FORMAT(date, "%H") AS hour');
        $this->db->select('agent_id');
        $this->db->select('COUNT(CASE WHEN event_type = "RINGNOANSWER" AND qq_events.ringtime > 1 THEN 1 END) AS calls_missed');
        $this->db->where_in('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('DATE_FORMAT(date, "%H")');
        return $this->db->get($this->_table)->result();
    }
    public function get_agent_daily_stats_for_agent_stats_page($agent_id = array(), $date_range = array())
    {
        if (!$agent_id || count($date_range) == 0) 
        {
            return false;
        }

        $this->db->select('DATE_FORMAT(date, "%Y-%m-%d") AS date');
        $this->db->select('agent_id');
        $this->db->select('COUNT(CASE WHEN event_type = "RINGNOANSWER" AND qq_events.ringtime > 1 THEN 1 END) AS calls_missed');
        $this->db->where_in('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('YEAR(date), MONTH(date), DAY(date)');
        return $this->db->get($this->_table)->result();
    }

    public function get_ring_no_answer_calls($agent_id = array(), $date_range = array())
    {
        if (count($agent_id) == 0 || count($date_range) == 0) {
            return false;
        }
    
        $this->db->select('*');
        $this->db->where_in('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->where('event_type', 'RINGNOANSWER');
        $this->db->where('ringtime >', 1); // Adjust the condition based on your requirements
    
        $result = $this->db->get($this->_table)->result();
    
    
        return $result;
    }
    

}
