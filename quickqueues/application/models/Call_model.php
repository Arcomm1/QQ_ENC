<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Call_model.php - Quickqueues call detail record abstraction */


class Call_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
        $this->cdrdb = $this->load->database('cdrdb', true);
    }


    /**
     * Search calls
     *
     * @param array $where Array of values to match with WHERE clause
     * @param array $like  Array of values to match with LIKE clause
     * @param int $limit Limit for pagination
     * @param int $offset Offset for pagination
     * @param bool Whether random calls should be returned
     * @return obj CodeIgniter database object
     */
    public function search($where = false, $like = false, $offset = false, $limit = false, $random = false)
    {
        if ($where and is_array($where)) {
            foreach ($where as $column => $value) {
                if ($value) {
                    if ($value == 'isnull') {
                        $this->db->where("$column IS NULL");
                        continue;
                    }
                    if (is_array($value)) {
                        $this->db->where_in($column, $value);
                    } else {
                        $this->db->where($column, $value);
                    }
                }
            }
        }

        if ($like and is_array($like)) {
            foreach ($like as $column => $value) {
                if ($value) {
                    if (is_array($value)) {
                        $this->db->group_start();
                        foreach ($value as $v) {
                            $this->db->or_like($column, $v);
                        }
                        $this->db->group_end();
                    } else {
                        $this->db->like($column, $value);
                    }
                }
            }
        }

        if ($offset) {
            $this->db->limit($offset, $limit);
        }
        if ($random) {
            $this->db->order_by('RAND()');
        } else {
            $this->db->order_by('id DESC');
        }


        // die($this->db->get_compiled_select());
        return $this->db->get($this->_table)->result();
    }


    /**
     * Count calls
     *
     * @param array $where Array of values to match with WHERE clause
     * @param array $like  Array of values to match with LIKE clause
     * @return obj CodeIgniter database object
     */
    public function count($where = false, $like = false)
    {
        if ($where and is_array($where)) {
            foreach ($where as $column => $value) {
                if ($value) {
                    if ($value == 'isnull') {
                        $this->db->where("$column IS NULL");
                        continue;
                    }
                    if (is_array($value)) {
                        $this->db->where_in($column, $value);
                    } else {
                        $this->db->where($column, $value);
                    }
                }
            }
        }

        if ($like and is_array($like)) {
            foreach ($like as $column => $value) {
                if ($value) {
                    if (is_array($value)) {
                        $this->db->group_start();
                        foreach ($value as $v) {
                            $this->db->or_like($column, $v);
                        }
                        $this->db->group_end();
                    } else {
                        $this->db->like($column, $value);
                    }
                }
            }
        }
        $this->db->from($this->_table);
        // $this->db->order_by('id DESC');

        // die($this->db->get_compiled_select());
        return $this->db->count_all_results();
    }


    public function get_recording($uniqueid = false)
    {
        if (!$uniqueid) {
            return false;
        }
        $result = $this->cdrdb->get_where('cdr', array('uniqueid' => $uniqueid));
        if ($result->num_rows() == 0) {
            return false;
        }
        if ($result->num_rows() == 1) {
            $cdr = $result->row();
            $this->update_by('uniqueid', $uniqueid, array('recording_file' => $cdr->recordingfile));
            return true;
        } else {
            // if somehow there are multiple CDR records, just go with the first one
            // BUT SHOULD WE?
            $cdrs = $result->result();
            $cdr = $cdrs[0];
            $this->update_by('uniqueid', $uniqueid, array('recording_file' => $cdr->recordingfile));
            return true;
        }
    }


    /**
     * Get calls by number, whether source or destination
     *
     * @param string $number Number to search
     * @param array $where Arbitrary WHERE filter
     * @param array $like Arbitrary LIKE filter
     * @param int $limit Limit results
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_many_by_number($number = false, $where = array(), $like = array(), $limit = 20)
    {
        if (!$number) {
            return false;
        }
        $this->db->group_start();
        $this->db->or_like('src', $number);
        $this->db->or_like('dst', $number);
        $this->db->group_end();

        if ($where and is_array($where)) {
            foreach ($where as $column => $value) {
                if ($value) {
                    if (is_array($value)) {
                        $this->db->where_in($column, $value);
                    } else {
                        $this->db->where($column, $value);
                    }
                }
            }
        }

        if ($like and is_array($like)) {
            foreach ($like as $column => $value) {
                if ($value) {
                    if (is_array($value)) {
                        $this->db->group_start();
                        foreach ($value as $v) {
                            $this->db->or_like($column, $v);
                        }
                        $this->db->group_end();
                    } else {
                        $this->db->like($column, $value);
                    }
                }
            }
        }

        $this->db->limit($limit);
        $this->db->order_by('id DESC');
        // die($this->db->get_compiled_select());
        return $this->db->get($this->_table)->result();
    }


    /**
     * Mark for survey
     *
     * @param string $uniqueid Unique ID of call
     * @return bool
     */
    public function mark_for_survey($uniqueid = false)
    {
        if (!$uniqueid) {
            log_to_file('ERROR', "Call_model->mark_for_survey - no Unique ID provided, exiting...");
            return false;
        }

        $call = $this->get_by('uniqueid', $uniqueid);
        if (!$call) {
            log_to_file('ERROR', "Call_model->mark_for_survey $uniqueid - Call not found, exiting...");
            return false;
        }

        $queue_config = $this->Queue_model->get_config($call->queue_id);
        if (!$queue_config) {
            log_to_file('ERROR', "Call_model->mark_for_survey $uniqueid - Queue config not found, exiting...");
            return false;
        }

        if ($queue_config['queue_enable_survey']->value == 'no') {
            log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - Queue has survey mode disabled, exiting...");
            return false;
        }

        if ($call->calltime < $queue_config['queue_survey_min_calltime']->value) {
            log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - Call time is too short for survey ".$call->calltime." <> ".$queue_config['queue_survey_min_calltime']->value.", exiting...");
            return false;
        }

        $grace_period = date('Y-m-d H:i:s', strtotime('-'.$queue_config['queue_survey_grace_period']->value.' days'));

        // Check if caller already participated in survey
        $calls_complete = $this->count_by_complex(
            array(
                'src'               => $call->src,
                'survey_complete'   => '1',
                'date >'            => $grace_period
            )
        );

        if ($calls_complete > 0) {
            log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - $call->src Already participated in survey, exiting...");
            return false;
        }

        // Check if caller is in survey queue
        $calls_queued = $this->count_by_complex(
            array(
                'src'           => $call->src,
                'survey_queue'  => '1',
                'date >'        => $grace_period
            )
        );

        if ($calls_queued > 0) {
            log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - $call->src Already in survey queue, exiting...");
            return false;
        }

        $total_complete = $this->Call_model->count_by_complex(
            array(
                'survey_complete'   => '1',
                'date >'            => QQ_TODAY_START
            )
        );

        if ($total_complete > $queue_config['queue_survey_max_results']->value) {
            log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - Already completed maximum amount of survey calls, exiting...");
            return false;
        }


        log_to_file('NOTICE', "Call_model->mark_for_survey $uniqueid - Marking call for survey queue");
        $this->update($call->id, array('survey_queue' => 1));

        return true;

    }


    /**
     * Get list of calls in survey queue
     *
     * @param void
     * @return mixed List of calls in survey queue
     */
    public function get_survey_queue()
    {
        return $this->get_many_by_complex(
            array(
                'survey_queue'  => 1,
                'date >'        => QQ_TODAY_START
            )
        );
    }


    /**
     * Try to make survey call
     *
     * @param obj $call Call object
     * @return bool True on success, False otherwise
     */
    public function make_survey_call($call = false)
    {
        if (!$call || !is_object($call)) {
            log_to_file('ERROR', "Call_model->make_survey_call - No Call object provided");
            return false;
        }

        $agent = $this->Agent_model->get($call->agent_id);
        $queue = $this->Queue_model->get($call->queue_id);

        // $content =  "Channel: SIP/".$src."\n";
        $content =  "Channel: SIP/trunk-2620000/995598412127\n";
        // $content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 60\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-survey-ivr-template\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "Set: AGENT=".$agent->name."\n";
        $content .= "Set: QUEUE=".$queue->name."\n";
        $content .= "Set: QQ_UNIQUEID=".$call->uniqueid."\n";

        // return $content;

        $callfile = time().'-'.$call->uniqueid.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        // rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);
        $this->update($call->id, array('survey_queue' => '0'));
    }


    /**
     * Get list of calls we need to call back automatically
     *
     * @param void
     * @return mixed List of calls in survey queue
     */
    public function get_auto_callback_queues()
    {
        /**
         * TODO
         * The starting time from which we select the queue should be in a config switch?
         */

         // Get the IDs of queues that have this enabled
         $queue_ids = array();
         foreach ($this->Queue_model->get_all() as $q) {
            $config = $this->Queue_model->get_config($q->id);
            if ($config['queue_auto_callback_enable']->value == 'yes') {
                $queue_ids[] = $q->id;
            }
            unset($config);
         }

         // Get actual list of calls
         $calls =  $this->get_many_by_complex(
            array(
                'called_back'   => 'no',
                'date >'        => QQ_TODAY_START,
                'event_type'    => array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY'),
            )
        );

        $result = array();
        foreach ($calls as $c) {
            if (in_array($c->queue_id, $queue_ids)) {
                $result[] = $c;
            }
        }

        return $result;
    }


    /**
     * Try to make survey call
     *
     * @param obj $call Call object
     * @return bool True on success, False otherwise
     */
    public function make_auto_callback($call = false)
    {
        if (!$call || !is_object($call)) {
            log_to_file('ERROR', "Call_model->make_auto_callback - No Call object provided");
            return false;
        }

        $queue = $this->Queue_model->get($call->queue_id);

        $queue_config = $this->Queue_model->get_config($call->queue_id);



        // $content =  "Channel: SIP/".$src."\n";
        $content =  "Channel: SIP/trunk-2620000/995598412127\n";
        // $content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 60\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: ".$queue_config['queue_auto_callback_dst']->value."\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "Set: QUEUE=".$queue->name."\n";
        $content .= "Set: QQ_UNIQUEID=".$call->uniqueid."\n";

        // return $content;

        $callfile = time().'-'.$call->uniqueid.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        // rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);
    }


    /**
     * Get the very first call
     *
     * @param void
     * @return obj Call object
     */
    public function get_first()
    {
        $this->db->order_by('id ASC');
        $this->db->limit(1);
        return $this->db->get($this->_table)->row();
    }

     /* Sats For Start Page */
    public function get_stats_for_start($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('COUNT(CASE WHEN event_type = "DID" THEN 1 END) AS calls_unique');
        $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type LIKE "COMPLETE%" THEN 1 END) AS unique_incoming_calls_answered');
        $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") THEN 1 END) AS unique_incoming_calls_unanswered');
		
		$this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type = "OUT_ANSWERED" THEN 1 END) AS unique_outgoing_calls_answered');
        $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS unique_outgoing_calls_unanswered');
		
        $this->db->select('COUNT(CASE WHEN event_type LIKE "COMPLETE%" THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
        $this->db->select('COUNT(CASE WHEN event_type IN ("INCOMINGOFFWORK") THEN 1 END) AS calls_offwork');

        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS max_calltime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS max_holdtime');
        $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND called_back = "no" AND waittime > 5 AND answered_elsewhere IS NULL THEN 1 END) AS calls_without_service');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND answered_elsewhere > 1 THEN 1 END) AS answered_elsewhere');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "yes" THEN 1 END) AS called_back');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "no" THEN 1 END) AS called_back_no');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "nah" THEN 1 END) AS called_back_nah');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "nop" THEN 1 END) AS called_back_nop');

        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND duplicate = "yes" THEN 1 END) AS calls_duplicate');
        $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
        $this->db->select('MAX(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_max');

        /* ------ FOR SLA: Hold Time ------ */

        
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 0 THEN 1 END) AS sla_count_total');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime <= 10 THEN 1 END) AS sla_count_less_than_or_equal_to_10');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 10 AND ringtime <= 20 THEN 1 END) AS sla_count_greater_than_10_and_less_than_or_equal_to_20');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 20 THEN 1 END) AS sla_count_greater_than_20');

        /* ------ End Of  FOR SLA: Hold Time ------ */

        /* ------ FOR ATA: Hold Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHKEY", "EXITWITHTIMEOUT") AND waittime > 0 THEN 1 END) AS ata_count_total');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHKEY", "EXITWITHTIMEOUT"), waittime, 0)) AS ata_total_waittime');
        /* ------ End Of  FOR ATA: Hold Time ------ */

        /* ------ FOR Incoming: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_max_calltime');
        /* ------ End Of  FOR Incoming: Total & AVG Time  ------ */

        /* ------ FOR Outgoing: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_max_calltime');
        /* ------ End Of  FOR Outgoing: Total & AVG Time  ------ */

        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        return $this->db->get($this->_table)->row();
    }

    /* here we are for agents*/
    public function get_agent_stats_for_agent_stats_page($agent_id = false, $date_range = array())
    {
        if (!$agent_id || count($date_range) == 0)
		{
            return false;
        }

        $this->db->select('agent_id');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "OUT_%" AND agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" AND agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND agent_id > 0 THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), ringtime, 0)) AS total_ringtime');

        /* ------ FOR SLA: Hold Time ------ */

        
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 0 THEN 1 END) AS sla_count_total');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime <= 10 THEN 1 END) AS sla_count_less_than_or_equal_to_10');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 10 AND ringtime <= 20 THEN 1 END) AS sla_count_greater_than_10_and_less_than_or_equal_to_20');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 20 THEN 1 END) AS sla_count_greater_than_20');

        /* ------ End Of  FOR SLA: Hold Time ------ */

        $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS max_holdtime');
        $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
        $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), ringtime, 0)) AS max_ringtime_answered');

        /* ------ FOR Incoming: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_max_calltime');
        /* ------ End Of  FOR Incoming: Total & AVG Time  ------ */

        /* ------ FOR Outgoing: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_max_calltime');
        /* ------ End Of  FOR Outgoing: Total & AVG Time  ------ */

        $this->db->where_in('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->from('qq_calls');
        return $this->db->get()->row();
    }


    public function get_agent_stats_for_start($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('qq_agents.display_name');
        $this->db->select('qq_agents.extension');
        $this->db->select('COUNT(CASE WHEN qq_calls.event_type LIKE "OUT_%" THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN qq_calls.event_type = "OUT_ANSWERED" THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN qq_calls.event_type IN("COMPLETECALLER", "COMPLETEAGENT") THEN 1 END) AS calls_answered');
        $this->db->where_in('qq_calls.queue_id', $queue_ids);
        $this->db->where('qq_calls.date >', $date_range['date_gt']);
        $this->db->where('qq_calls.date <', $date_range['date_lt']);
        $this->db->where('qq_agents.id', 'qq_calls.agent_id', FALSE);
        $this->db->group_by('agent_id');
        $this->db->from('qq_calls, qq_agents');
        return $this->db->get()->result();
    }

/* Stats For Agents*/
    public function get_agent_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('agent_id');
        $this->db->select('qq_agents.display_name, qq_agents.extension, qq_agents.last_call');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "OUT_%" AND agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" AND agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND agent_id > 0 THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND agent_id >= 0 THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), ringtime, 0)) AS total_ringtime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');

         /* ------ FOR SLA: Hold Time ------ */
         $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 0 THEN 1 END) AS sla_count_total');
         $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime <= 10 THEN 1 END) AS sla_count_less_than_or_equal_to_10');
         $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 10 AND ringtime <= 20 THEN 1 END) AS sla_count_greater_than_10_and_less_than_or_equal_to_20');
         $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 20 THEN 1 END) AS sla_count_greater_than_20');
         /* ------ End Of  FOR SLA: Hold Time ------ */

        $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS max_holdtime');
        $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
        $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), ringtime, 0)) AS max_ringtime_answered');

        /* ------ FOR Incoming: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_max_calltime');
        /* ------ End Of  FOR Incoming: Total & AVG Time  ------ */


        /* ------ FOR Outgoing: Total & AVG Time ------ */
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_max_calltime');

        /* ------ End Of  FOR Outgoing: Total & AVG Time  ------ */
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->join('qq_agents', 'qq_calls.agent_id = qq_agents.id', 'left');
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('agent_id');
        $this->db->from('qq_calls');
        return $this->db->get()->result();
    }


    public function get_queue_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
            $this->db->select('COUNT(CASE WHEN event_type = "DID" THEN 1 END) AS calls_unique');
            $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type LIKE "COMPLETE%" THEN 1 END) AS unique_incoming_calls_answered');
            $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") THEN 1 END) AS unique_incoming_calls_unanswered');
            
            $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type = "OUT_ANSWERED" THEN 1 END) AS unique_outgoing_calls_answered');
            $this->db->select('COUNT(DISTINCT(src), CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS unique_outgoing_calls_unanswered');
            

            $this->db->select('COUNT(CASE WHEN event_type IN ("INCOMINGOFFWORK") THEN 1 END) AS calls_offwork');


            $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
            $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS max_calltime');
            $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
            $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
            $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS max_holdtime');
            $this->db->select('MAX(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS max_waittime');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND called_back = "no" AND waittime > 5 AND answered_elsewhere IS NULL THEN 1 END) AS calls_without_service');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND answered_elsewhere > 1 THEN 1 END) AS answered_elsewhere');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "yes" THEN 1 END) AS called_back');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "no" THEN 1 END) AS called_back_no');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "nah" THEN 1 END) AS called_back_nah');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "nop" THEN 1 END) AS called_back_nop');

            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND duplicate = "yes" THEN 1 END) AS calls_duplicate');
            $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
            $this->db->select('MAX(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_max');

            /* ------ FOR SLA: Hold Time ------ */

            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 0 THEN 1 END) AS sla_count_total');
            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime <= 10 THEN 1 END) AS sla_count_less_than_or_equal_to_10');
            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 10 AND ringtime <= 20 THEN 1 END) AS sla_count_greater_than_10_and_less_than_or_equal_to_20');
            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND ringtime > 20 THEN 1 END) AS sla_count_greater_than_20');

            /* ------ End Of  FOR SLA: Hold Time ------ */

            /* ------ FOR ATA: Hold Time ------ */
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHKEY", "EXITWITHTIMEOUT") AND waittime > 0 THEN 1 END) AS ata_count_total');
            $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHKEY", "EXITWITHTIMEOUT"), waittime, 0)) AS ata_total_waittime');
            /* ------ End Of  FOR ATA: Hold Time ------ */

            /* ------ FOR Incoming: Total & AVG Time ------ */
            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
            $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
            $this->db->select('MAX(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_max_calltime');
            /* ------ End Of  FOR Incoming: Total & AVG Time  ------ */

            /* ------ FOR Outgoing: Total & AVG Time ------ */
            $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
            $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
            $this->db->select('MAX(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_max_calltime');
            /* ------ End Of  FOR Outgoing: Total & AVG Time  ------ */
            $this->db->select('qq_queues.display_name');
            $this->db->select('qq_calls.queue_id');
                
            $this->db->select('COUNT(CASE WHEN qq_calls.event_type LIKE "OUT_%" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing');
            $this->db->select('COUNT(CASE WHEN qq_calls.event_type = "OUT_ANSWERED" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
            $this->db->select('COUNT(CASE WHEN qq_calls.event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND qq_calls.agent_id > 0 THEN 1 END) AS calls_answered');
            $this->db->select('COUNT(CASE WHEN qq_calls.event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
            $this->db->select('SUM(IF(qq_calls.event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), qq_calls.calltime, 0)) AS total_calltime');
            $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
            $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
            $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND called_back = "no" AND waittime > 5 AND answered_elsewhere IS NULL THEN 1 END) AS calls_without_service');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND answered_elsewhere > 1 THEN 1 END) AS answered_elsewhere');
            $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "yes" THEN 1 END) AS called_back');
            $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
            $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
            $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
            $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
            $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
            $this->db->where_in('qq_calls.queue_id', $queue_ids);
            $this->db->where('qq_calls.date >', $date_range['date_gt']);
            $this->db->where('qq_calls.date <', $date_range['date_lt']);
            $this->db->where('qq_queues.id', 'qq_calls.queue_id', FALSE);
            $this->db->group_by('queue_id');
            $this->db->from('qq_calls, qq_queues');
            return $this->db->get()->result();
    }

    // public function get_hourly_stats_for_agent_page($agent_id, $date_range = array())
    // {
    //     if (count($agent_id) == 0 || count($date_range) == 0) {
    //         return false;
    //     }
    //     $this->db->select('DATE_FORMAT(date, "%H") AS hour');
    //     $this->db->select('qq_agents.display_name');
    //     $this->db->select('qq_calls.queue_id');
    //     $this->db->select('COUNT(CASE WHEN qq_calls.event_type LIKE "OUT_%" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing');
    //     $this->db->select('COUNT(CASE WHEN qq_calls.event_type = "OUT_ANSWERED" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing_answered');

    //     $this->db->select('COUNT(DISTINCT(timestamp), CASE WHEN qq_calls.event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND qq_calls.agent_id > 0 THEN 1 END) AS calls_answered');

    //     $this->db->select('COUNT(CASE WHEN qq_calls.event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
    //     $this->db->select('SUM(IF(qq_calls.event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), qq_calls.calltime, 0)) AS total_calltime');
    //     $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
    //     $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
    //     $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
    //     $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND called_back = "no" AND waittime > 5 AND answered_elsewhere IS NULL THEN 1 END) AS calls_without_service');
    //     $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND answered_elsewhere > 1 THEN 1 END) AS answered_elsewhere');
    //     $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "yes" THEN 1 END) AS called_back');
    //     $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
    //     $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
    // 

    //     $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
    //     $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
    //     $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
    //     $this->db->where('qq_calls.agent_id', $agent_id);
    //     $this->db->where('qq_calls.date >', $date_range['date_gt']);
    //     $this->db->where('qq_calls.date <', $date_range['date_lt']);
    //     $this->db->where('qq_agents.id', 'qq_calls.agent_id', FALSE);
    //     $this->db->group_by('agent_id');
    //     $this->db->group_by('HOUR(date)');
    //     $this->db->from('qq_calls, qq_queues, qq_agents');
        
    //     return $this->db->get()->result();
    // }

    public function get_hourly_stats_for_agent_page($agent_id, $date_range = array()) 
    {
        if (empty($agent_id) || empty($date_range)) 
        {
            return false;
        }
    
        $this->db->select('DATE_FORMAT(date, "%H") AS hour');
        $this->db->select('qq_agents.display_name');
        $this->db->select('qq_calls.queue_id');
        $this->db->select('COUNT(CASE WHEN qq_calls.event_type LIKE "OUT_%" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN qq_calls.event_type = "OUT_ANSWERED" AND qq_calls.agent_id > 0 THEN 1 END) AS calls_outgoing_answered');

        $this->db->select('COUNT(DISTINCT(timestamp), CASE WHEN qq_calls.event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND qq_calls.agent_id > 0 THEN 1 END) AS calls_answered');

        $this->db->select('COUNT(CASE WHEN qq_calls.event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(qq_calls.event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), qq_calls.calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND called_back = "no" AND waittime > 5 AND answered_elsewhere IS NULL THEN 1 END) AS calls_without_service');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY", "IVRABANDON") AND answered_elsewhere > 1 THEN 1 END) AS answered_elsewhere');
        $this->db->select('COUNT(CASE WHEN event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY") AND called_back = "yes" THEN 1 END) AS called_back');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
    

        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
    
        $this->db->from('qq_calls');
        $this->db->join('qq_agents', 'qq_agents.id = qq_calls.agent_id');
        $this->db->join('qq_queues', 'qq_queues.id = qq_calls.queue_id');
    
        $this->db->where('qq_calls.agent_id', $agent_id);
        $this->db->where('qq_calls.date >', $date_range['date_gt']);
        $this->db->where('qq_calls.date <', $date_range['date_lt']);
    
        $this->db->group_by('DATE_FORMAT(date, "%H")');

    
        return $this->db->get()->result();
    }
    

    public function get_daily_stats_for_agent_page($agent_id, $date_range = array())
    {
        if (count($agent_id) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('DATE_FORMAT(date, "%Y-%m-%d") AS date');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "OUT_%" AND agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" AND agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND agent_id > 0 THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
        $this->db->where('agent_id', $agent_id);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('YEAR(date), MONTH(date), DAY(date)');
        return $this->db->get($this->_table)->result();
    }

    public function get_daily_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('DATE_FORMAT(date, "%Y-%m-%d") AS date');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "OUT_%" AND agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" AND agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND agent_id > 0 THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('YEAR(date), MONTH(date), DAY(date)');
        return $this->db->get($this->_table)->result();
    }


    public function get_hourly_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('DATE_FORMAT(date, "%H") AS hour');
        $this->db->select('COUNT(CASE WHEN event_type LIKE "OUT_%" AND agent_id > 0 THEN 1 END) AS calls_outgoing');
        $this->db->select('COUNT(CASE WHEN event_type = "OUT_ANSWERED" AND agent_id > 0 THEN 1 END) AS calls_outgoing_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("COMPLETECALLER", "COMPLETEAGENT") AND agent_id > 0 THEN 1 END) AS calls_answered');
        $this->db->select('COUNT(CASE WHEN event_type IN("ABANDON", "EXITEMPTY", "EXITWITHLKEY", "EXITWITHTIMEOUT", "IVRABANDON") THEN 1 END) AS calls_unanswered');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED", "COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS total_calltime');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), holdtime, 0)) AS total_holdtime');
        $this->db->select('SUM(IF(event_type IN ("ABANDON", "EXITWITHKEY", "EXITWITHTIMEOUT", "EXITEMPTY"), waittime, 0)) AS total_waittime');
        $this->db->select('AVG(IF(event_type in ("COMPLETECALLER", "COMPLETEAGENT", "ABANDON", "EXITEMPTY", "EXITWITHTIMEOUT", "EXITWITHKEY"), origposition, 0)) AS origposition_avg');
        $this->db->select('SUM(IF(event_type IN ("COMPLETECALLER", "COMPLETEAGENT"), calltime, 0)) AS incoming_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("COMPLETECALLER", "COMPLETEAGENT") AND calltime > 0 THEN 1 END) AS incoming_total_calltime_count');
        
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_ANSWERED") AND calltime > 0 THEN 1 END) AS outgoing_total_calltime_count');
        $this->db->select('SUM(IF(event_type IN ("OUT_ANSWERED"), calltime, 0)) AS outgoing_total_calltime');
        $this->db->select('COUNT(CASE WHEN event_type IN ("OUT_BUSY", "OUT_NOANSWER", "OUT_FAILED") THEN 1 END) AS calls_outgoing_unanswered');
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('HOUR(date)');
        return $this->db->get($this->_table)->result();
    }


    public function get_category_stats_for_start_page($queue_ids = array(), $date_range = array())
    {
        if (count($queue_ids) == 0 || count($date_range) == 0) {
            return false;
        }
        $this->db->select('category_id, COUNT(*) as count');
        $this->db->where_in('queue_id', $queue_ids);
        $this->db->where('date >', $date_range['date_gt']);
        $this->db->where('date <', $date_range['date_lt']);
        $this->db->group_by('category_id');
        return $this->db->get($this->_table)->result();
    }

    /* ----------- SMS API------------*/
    public function get_number_for_sms($uniqueid = false){
        $this->db->where('uniqueid', $uniqueid);
        return $this->db->get($this->_table)->row_array();
    }

}
