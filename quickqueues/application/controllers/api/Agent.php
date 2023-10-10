<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Agent extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->r = new stdClass();

        // Just default to error
        $this->r->status = 'FAIL';
        $this->r->message = 'Internal error';
        $this->r->data = new stdClass();

        $this->data->queue_ids = array();
        foreach ($this->data->user_queues as $q) {
            $this->data->queue_ids[] = $q->id;
        }

        $this->data->track_pauses = $this->Config_model->get_item('app_track_agent_pause_time');
        $this->data->track_outgoing = $this->Config_model->get_item('app_track_outgoing');
        $this->data->track_ringnoanswer = $this->Config_model->get_item('app_track_ringnoanswer');

    }


    private function _respond() {
        header('Content-Type: application/json');
        echo json_encode($this->r, JSON_FORCE_OBJECT);
    }


    public function get($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $agent = $this->Agent_model->get($id);

        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent data will follow';
        $this->r->data = $agent;

        $this->_respond();

    }


    public function get_all()
    {
        $agents = array();
        foreach ($this->data->user_agents as $a) {
            $agents[$a->id] = $a;
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent data will follow';
        $this->r->data = $agents;

        $this->_respond();

    }

    // TODO obsolete
    public function get_overview($restrict_by_role = false) {
        $overview = array();

        if ($restrict_by_role) {
            $t_agents = $this->Agent_model->get_all();
        } else {
            $t_agents = $this->data->user_agents;
        }

        $this->load->library('asterisk_manager');
        foreach ($t_agents as $a) {
            $overview[$a->id]['data'] = $a;
            $overview[$a->id]['realtime'] = $this->asterisk_manager->get_agent_status($a->extension);
            $overview[$a->id]['current_calls'] = $this->asterisk_manager->get_agent_call($a->extension);
            $overview[$a->id]['calls_answered'] = $this->Event_model->count_by_complex(
                array(
                    'agent_id' => $a->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'queue_id' => $this->data->queue_ids,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                )
            );
            $overview[$a->id]['calls_outgoing'] = $this->Event_model->count_by_complex(
                array(
                    'agent_id' => $a->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => array('OUT_BUSY', 'OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER'),
                )
            );
            $overview[$a->id]['work_start'] = $this->Agent_model->get_first_event($a->id, 'STOPPAUSE');
            $overview[$a->id]['work_end'] = $this->Agent_model->get_last_event($a->id, 'STARTPAUSE');

            $overview[$a->id]['calls_missed'] = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $a->id,
                    'date >'        => QQ_TODAY_START,
                    'date <'        => QQ_TODAY_END,
                    'event_type'    => 'RINGNOANSWER',
                    'queue_id'      => $this->data->queue_ids,
                    'ringtime >'    => 1,
                )
            );

            $overview[$a->id]['call_time'] = $this->Event_model->sum_by_complex(
                'calltime',
                array(
                    'agent_id'      => $a->id,
                    'date >'        => QQ_TODAY_START,
                    'date <'        => QQ_TODAY_END,
                    'queue_id'      => $this->data->queue_ids,
                )
            );

            if ($this->data->track_pauses == 'yes') {
                $overview[$a->id]['pause_time'] = $this->Event_model->sum_by_complex(
                    'pausetime',
                    array(
                        'agent_id'      => $a->id,
                        'date >'        => QQ_TODAY_START,
                        'date <'        => QQ_TODAY_END,
                        'pausetime <'   => '28800', // Ignore large pauses, they are not pauses, rather end of work
                        'event_type'    => 'STOPPAUSE'
                    )
                );
            }
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent overview will follow';
        $this->r->data = $overview;

        $this->_respond();

    }


    public function get_realtime_status($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }
        $this->load->library('asterisk_manager');
        $this->r->status = 'OK';
        $this->r->message = 'Agent realtime status will follow';
        $this->r->data = $this->asterisk_manager->get_agent_status($agent->extension);
        $this->_respond();
    }


    public function get_realtime_status_for_all_agents()
    {
        $agent_statuses = array();
        $agents = array();

        $this->load->library('asterisk_manager');
        $this->r->status = 'OK';
        $this->r->message = 'Realtime status for all agents will follow';
        $ami_response = $this->asterisk_manager->get_extension_state_list();

        $extensions = array();
        foreach ($this->data->user_agents as $a) {
            $extensions[] = $a->extension;
        }

        foreach ($ami_response as $ar) {
            $state_colors = get_extension_state_colors();
            if (array_key_exists('Context', $ar)) {
                if ($ar['Context'] == QQ_AGENT_CONTEXT) {
                    if (in_array($ar['Exten'], $extensions)) {
                        $ar['status_color'] = $state_colors[$ar['Status']];
                        $agent_statuses[$ar['Exten']] = $ar;
                    }
                }
            }
        }
        $this->r->data = $agent_statuses;
        $this->_respond();
    }


    public function get_current_call($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }
        $this->load->library('asterisk_manager');
        $this->r->status = 'OK';
        $this->r->message = 'Agent current call will follow';
        $this->r->data =  $this->asterisk_manager->get_agent_call($agent->extension);
        $this->_respond();
        //echo json_encode(rand(10,100));
    }


    public function get_current_calls_for_all_agents()
    {
        $all_calls = array();

        $this->load->library('asterisk_manager');
        $this->r->status = 'OK';
        $this->r->message = 'Agent current call will follow';
        $ami_response = $this->asterisk_manager->get_status();

        foreach ($ami_response as $r) {
            if (array_key_exists('Channel', $r)) {
                if (array_key_exists('Seconds', $r) && ($r['Context'] == 'macro-dial-one' || $r['Context'] == 'macro-dialout-trunk')) {
                    $r['second_party'] = '';
                    $r['agent_exten'] = '';
                    $r['duration'] = $r['Seconds'];
                    $r['direction'] = '';
                    if ($r['Context'] == 'macro-dial-one') {
                        $r['direction'] = 'down';
                        $r['second_party'] = $r['ConnectedLineNum'];
                        $r['agent_exten'] = $r['CallerIDNum'];
                    }
                    if ($r['Context'] == 'macro-dialout-trunk') {
                        $r['direction'] = 'up';
                        $r['second_party'] = $r['ConnectedLineNum'];
                        $num = explode('/', $r['Channel']);
                        $num = explode('-', $num[1]);
                        $r['agent_exten'] = $num[0];
                    }
                    $all_calls[$r['agent_exten']] = $r;
                }
            }
        }
        $this->r->data = $all_calls;

        $this->_respond();
    }


    public function vendoo_get_last_call($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }
       /*  $this->r->data = $this->Agent_model->vendoo_get_last_call($id);
        $this->r->status = 'OK';
        $this->r->message = 'Agent last call will follow'; */
        //$this->_respond();
        $result=$this->Agent_model->vendoo_get_last_call($id);
        echo json_encode(array(
            "statusCode"=>200,
            "current_call"=>$result,
            ));
    }


    public function gorgia_get_last_call($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }
        $this->r->data = $this->Agent_model->vendoo_get_last_call($id);
        $this->r->status = 'OK';
        $this->r->message = 'Agent last call will follow';
        $this->_respond();
    }


    public function get_pause_events($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $this->r->data = $this->Event_model->get_many_by_complex(
            array(
                'agent_id'      => $id,
                'event_type'    => array('STARTPAUSE', 'STOPPAUSE'),
                'date >'        => $date_gt,
                'date <'        => $date_lt,
            )
        );

        $this->r->status = 'OK';
        $this->r->message = 'Agent pause events will follow';
        $this->_respond();

    }

    // TODO obsolete
    public function get_stats($id = false, $queue_id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $track_ringnoanswer = $this->Config_model->get_item('app_track_ringnoanswer');
        $track_outgoing = $this->Config_model->get_item('app_track_outgoing');


        $this->r->data->answered_10s = $this->Event_model->count_by_complex(
            array(
                'agent_id'      => $id,
                'event_type'    => 'CONNECT',
                'date >'        => $date_gt,
                'date <'        => $date_lt,
                'queue_id'      => $this->data->queue_ids,
                'ringtime <'    => 10
            )
        );

        $this->r->data->calls_completecaller = $this->Event_model->count_by_complex(
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids,
                'event_type' => array('COMPLETECALLER'),
            )
        );

        $this->r->data->calls_completeagent = $this->Event_model->count_by_complex(
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids,
                'event_type' => array('COMPLETEAGENT'),
            )
        );

        $this->r->data->calls_answered = $this->r->data->calls_completeagent + $this->r->data->calls_completecaller;

        $this->r->data->calls_answered_total = $this->Event_model->count_by_complex(
            array(
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
            )
        );


        if ($track_ringnoanswer != 'no') {
            $this->r->data->calls_missed = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'event_type'    => 'RINGNOANSWER',
                    'queue_id'      => $this->data->queue_ids,
                    'ringtime >'    => 1,
                )
            );
            $this->r->data->calls_missed_total = $this->Event_model->count_by_complex(
                array(
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'event_type'    => 'RINGNOANSWER',
                    'queue_id'      => $this->data->queue_ids,
                    'ringtime >'    => 1,
                )
            );
        }

        if ($track_ringnoanswer == '10sec') {
            $this->r->data->calls_missed = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'event_type'    => 'RINGNOANSWER',
                    'queue_id'      => $this->data->queue_ids,
                    'ringtime >'    => 1
                )
            );
        }

        $this->r->data->total_calltime = $this->Event_model->sum_by_complex(
            'calltime',
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        if ($track_outgoing == 'yes') {
            $this->r->data->calls_outgoing = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'queue_id'      => $this->data->queue_ids,
                    'event_type'    => array('OUT_BUSY', 'OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER'),
                )
            );

            $this->r->data->calltime_out = $this->Event_model->sum_by_complex(
            'calltime',
                array(
                    'agent_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'queue_id' => $this->data->queue_ids,
                    'event_type' => array('OUT_ANSWERED')
                )
            );

            $this->r->data->total_calltime = $this->r->data->total_calltime + $this->r->data->calltime_out;
        }

        $this->r->data->max_calltime = $this->Event_model->max_by_complex(
            'calltime',
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT', 'OUT_ANSWERED')
            )
        );

        $this->r->data->total_ringtime = $this->Event_model->sum_by_complex(
            'ringtime',
            array(
                'agent_id'      => $id,
                'event_type'    => 'CONNECT',
                'date >'        => $date_gt,
                'date <'        => $date_lt,
                'queue_id'      => $this->data->queue_ids,
            )
        );

        $this->r->data->max_ringtime = $this->Event_model->max_by_complex(
            'ringtime',
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids
            )
        );

        if ($this->data->track_pauses == 'yes') {
            $this->r->data->pause_time = $this->Event_model->sum_by_complex(
                'pausetime',
                array(
                    'agent_id'      => $id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'pausetime <'   => '28800', // Ignore large pauses, they are not pauses, rather end of work
                    'event_type'    => 'STOPPAUSE'
                )
            );
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent stats will follow';
        $this->_respond();

    }


    public function get_calls($id = false)
    {
        $calls = array();
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $calls = $this->Call_model->search(
            array(
                'agent_id'  => $id,
                'date >'    => $date_gt,
                'date <'    => $date_lt
            )
        );

        $this->r->status = 'OK';
        $this->r->message = 'Agent calls will follow';
        $this->r->data = $calls;
        $this->_respond();

    }

    // TODO obsolete
    public function get_dashboard_calls($id = false)
    {
        $calls = array();
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $where = array();
        $like = array();

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $where = array(
            'date >'    => $date_gt,
            'date <'    => $date_lt
        );

        $agent_call_restrictions = $this->Config_model->get_item('agent_call_restrictions');

        if ($agent_call_restrictions == 'own') {
            $where['agent_id'] = $id;
        } elseif ($agent_call_restrictions == 'queue') {
            $where['queue_id'] = $this->Agent_model->get_queue_ids($id);
        }

        if ($this->input->post('src')) {
            $like['src'] = $this->input->post('src');
        }

        if ($this->input->post('dst')) {
            $like['dst'] = $this->input->post('dst');
        }

        $calls = $this->Call_model->search($where, $like);

        $this->r->status = 'OK';
        $this->r->message = 'Agent calls will follow';
        $this->r->data = $calls;
        $this->_respond();

    }


    public function get_stats_by_agent($as_admin = false)
    {
        $agents = array();
        $stnega = array(); // eh?

        if ($as_admin) {
            foreach ($this->Agent_model->get_all() as $a) {
                $agents[$a->id] = $a->display_name;
                $stnega[$a->display_name] = $a->id;
            }
        } else {
            foreach ($this->data->user_agents as $a) {
                $agents[$a->id] = $a->display_name;
                $stnega[$a->display_name] = $a->id;
            }
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $track_ringnoanswer = $this->Config_model->get_item('app_track_ringnoanswer');

        $calls = $this->Event_model->get_many_by_complex(
            array(
                'date >' => $date_gt,
                'date <' => $date_lt,
                'queue_id' => $this->data->queue_ids,
            )
        );

        $agent_distribution = array();

        $a = array(
            'calls_answered'    => 0,
            'calls_unanswered'  => 0,
            'calls_outgoing'    => 0,
        );

        foreach ($agents as $aid => $aname) {
            $agent_distribution[$aname] = $a;
        }

        // print_r($agent_distribution);

        foreach ($agent_distribution as $an => $ad) {
            $agent_distribution[$an]['calls_answered'] = $this->Call_model->count_by_complex(
                array(
                    'agent_id' => $stnega[$an],
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'queue_id' => $this->data->queue_ids,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                )
            );
            $agent_distribution[$an]['calls_outgoing'] = $this->Call_model->count_by_complex(
                array(
                    'agent_id' => $stnega[$an],
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'queue_id' => $this->data->queue_ids,
                    'event_type' => array('OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY', 'OUT_FAILED'),
                )
            );

            if ($track_ringnoanswer != 'no') {
                if ($track_ringnoanswer == 'yes') {
                    $agent_distribution[$an]['calls_outgoing'] = $this->Event_model->count_by_complex(
                        array(
                            'agent_id' => $stnega[$an],
                            'date >' => $date_gt,
                            'date <' => $date_lt,
                            'queue_id' => $this->data->queue_ids,
                            'event_type' => array('RINGNOANSWER'),
                            'ringtime >' => 1000
                        )
                    );
                }

                if ($track_ringnoanswer == '10sec') {
                    $agent_distribution[$an]['calls_outgoing'] = $this->Event_model->count_by_complex(
                        array(
                            'agent_id' => $stnega[$an],
                            'date >' => $date_gt,
                            'date <' => $date_lt,
                            'queue_id' => $this->data->queue_ids,
                            'event_type' => array('RINGNOANSWER'),
                            'ringtime >' => 10000
                        )
                    );
                }
            }
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent stats distribution data will follow';
        $this->r->data = $agent_distribution;

        $this->_respond();
    }


    public function get_hourly_stats_for_agents($id)
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $stats = $this->Call_model->get_hourly_stats_for_agent_page($id, $date_range);
        //$stats = $stats[0];
       // $stats->avg_holdtime = ceil(($s->total_holdtime + $s->total_waittime) == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / ($s->calls_answered + $s->calls_unanswered));
       for ($i=0; $i < 24; $i++) 
       {
        $has = false;
        foreach($stats as $s) 
        {
            $s->avg_holdtime = ceil(($s->total_holdtime + $s->total_waittime) == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / ($s->calls_answered + $s->calls_unanswered));

            if(intval($s->hour) == $i)
            {
                $has = true;
            }
        }
        if(!$has)
        {
            $newTime = new stdClass();
            $newTime->hour                      = str_pad($i,2,"0", STR_PAD_LEFT);
            $newTime->calls_answered            = 0;
            $newTime->calls_unanswered          = 0;
            $newTime->incoming_total_calltime   = 0;
            $newTime->calls_outgoing_answered   = 0;
            $newTime->calls_outgoing_unanswered = 0;
            $newTime->outgoing_total_calltime   = 0;
            $newTime->total_holdtime            = 0;
            $newTime->total_waittime            = 0;
            $newTime->avg_holdtime              = 0;

            array_push($stats,$newTime);
        }
       }
       
       usort($stats, function($a, $b) {
        // Define the sorting order
        $sortingOrder = ["10", "11", "12"];
        for ($i = 13; $i <= 23; $i++) {
            $sortingOrder[] = str_pad($i, 2, "0", STR_PAD_LEFT);
        }
        for ($i = 0; $i <= 9; $i++) {
            $sortingOrder[] = "0" . $i;
        }
    
        // Get the position of the hour in the sorting order
        $pos_a = array_search($a->hour, $sortingOrder);
        $pos_b = array_search($b->hour, $sortingOrder);
    
        return $pos_a - $pos_b;
    });
        $this->r->status = 'OK';
        $this->r->message = 'Call distribution data will follow';
        $this->r->data = $stats;

        $this->_respond();
    }
    public function get_daily_stats_for_agents($id)
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $stats = $this->Call_model->get_daily_stats_for_agent_page($id, $date_range);
        
        foreach ($stats as $i) 
        {

            if (($i->calls_answered + $i->calls_outgoing) == 0) 
            {
                $avg_calltime = '00:00:00';
            } 
            else 
            {
                $avg_calltime = sec_to_time($i->total_calltime / ($i->calls_answered + $i->calls_outgoing));
            }

            if ($i->calls_unanswered == 0) 
            {
                $avg_holdtime = '00:00:00';
            } else 
            {
                $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $i->calls_unanswered);
            }

            $daily_stats[] = array(
                'day'                       => $i->date,
                'calls_total'               => $i->calls_answered + $i->calls_outgoing + $i->calls_unanswered,
                'calls_answered'            => $i->calls_answered,
                'calls_missed'              => $i->calls_unanswered,
                'calls_outgoing'            => $i->calls_outgoing,
                'total_calltime'            => sec_to_time($i->total_calltime),
                'total_holdtime'            => sec_to_time($i->total_holdtime),
                'avg_holdtime'              => $avg_holdtime,
                'origposition_avg'          => ceil($i->origposition_avg),
                'calls_outgoing_answered'   => $i->calls_outgoing_answered,
                'calls_outgoing_unanswered' => $i->calls_outgoing_answered,
                'incoming_total_calltime'    => $i->incoming_total_calltime,
                'outgoing_total_calltime'   => $i->outgoing_total_calltime,
            );
        }

        $this->r->data = $daily_stats;
        $this->r->status = 'OK';
        $this->r->message = 'Daily queue stats will follow';
        $this->_respond();
        
      
    }
       

    public function get_stats_by_hour($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $agent = $this->Agent_model->get($id);
        var_dump($agent);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $calls = $this->Event_model->get_many_by_complex(
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
            )
        );

        $call_distribution = array();

        $a = array(
            'calls_answered'    => 0,
            'call_time'         => 0,
            'ring_time'         => 0,
        );

        
        if ($this->data->track_pauses == 'yes') {
            $a['pause_time'] = 0;
        }

        if ($this->data->track_outgoing == 'yes') {
            $a['calls_outgoing'] = 0;
        }

        if ($this->data->track_ringnoanswer != 'no') {
            $a['calls_unanswered'] = 0;
        }

        // Populate empty 24h array
        for ($i=0; $i < 24; $i++) {
            $h = $i < 10 ? '0'.$i : $i;
            $call_distribution[$h] = $a;
        }

        foreach ($calls as $c) {
            $h = date('H', $c->timestamp);
            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                $call_distribution[$h]['calls_answered']++;
                $call_distribution[$h]['call_time'] += $c->calltime;
            };

            if ($c->event_type == 'OUT_ANSWERED' ||
                $c->event_type == 'OUT_NOANSWER' ||
                $c->event_type == 'OUT_BUSY'     ||
                $c->event_type == 'OUT_FAILED'
                ) {
                $call_distribution[$h]['calls_outgoing']++;
                $call_distribution[$h]['call_time'] += $c->calltime;
            };

            $call_distribution[$h]['ring_time'] += $c->ringtime;

            if ($this->data->track_ringnoanswer != 'no') {
                if ($c->event_type == 'RINGNOANSWER') {
                    if ($c->ringtime > 1) {
                        $call_distribution[$h]['calls_unanswered']++;
                    }
                };
            }
            if ($this->data->track_pauses == 'yes') {
                if ($c->event_type == 'STOPPAUSE' && $c->pausetime < '28800') {
                    $call_distribution[$h]['pause_time'] += $c->pausetime;
                }
            }

        }

        $this->r->status = 'OK';
        $this->r->message = 'Call distribution data will follow';
        $this->r->data = $call_distribution;

        $this->_respond();
    }


    public function get_stats_by_day($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $calls = $this->Event_model->get_many_by_complex(
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
            )
        );

        $call_distribution = array();

        $a = array(
            'calls_answered'    => 0,
            'call_time'         => 0,
            'ring_time'         => 0,
        );


        if ($this->data->track_pauses == 'yes') {
            $a['pause_time'] = 0;
        }

        if ($this->data->track_outgoing == 'yes') {
            $a['calls_outgoing'] = 0;
        }

        if ($this->data->track_ringnoanswer != 'no') {
            $a['calls_unanswered'] = 0;
        }

        foreach ($calls as $c) {
            $d = date('Y-m-d', $c->timestamp);
            if (!array_key_exists($d, $call_distribution)) {
                $call_distribution[$d] = $a;
            }

            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                $call_distribution[$d]['calls_answered']++;
                $call_distribution[$d]['call_time'] += $c->calltime;
            };

            if ($this->data->track_outgoing == 'yes') {
                if ($c->event_type == 'OUT_ANSWERED' ||
                    $c->event_type == 'OUT_NOANSWER' ||
                    $c->event_type == 'OUT_BUSY'     ||
                    $c->event_type == 'OUT_FAILED'
                    ) {
                    $call_distribution[$d]['calls_outgoing']++;
                    $call_distribution[$d]['call_time'] += $c->calltime;
                };
            }

            $call_distribution[$d]['ring_time'] += $c->ringtime;

            if ($this->data->track_ringnoanswer != 'no') {
                if (!array_key_exists($d, $call_distribution)) {
                    $call_distribution[$d] = $a;
                }
                if ($c->event_type == 'RINGNOANSWER') {
                    if ($c->ringtime > 1) {
                        $call_distribution[$d]['calls_unanswered']++;
                    }
                };
            }
            if ($this->data->track_pauses == 'yes') {
                if ($c->event_type == 'STOPPAUSE' && $c->pausetime < '28800') {
                    $call_distribution[$d]['pause_time'] += $c->pausetime;
                }
            }

        }

        $this->r->status = 'OK';
        $this->r->message = 'Call distribution data will follow';
        $this->r->data = $call_distribution;

        $this->_respond();
    }


    public function get_stats_by_weekday($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $agent = $this->Agent_model->get($id);
        if (!$agent) {
            $this->r->status = 'FAIL';
            $this->r->message = "Agent does not exist";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $calls = $this->Event_model->get_many_by_complex(
            array(
                'agent_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
            )
        );

        $call_distribution = array();

        $a = array(
            'calls_answered'    => 0,
            'call_time'         => 0,
            'ring_time'         => 0,
        );


        if ($this->data->track_pauses == 'yes') {
            $a['pause_time'] = 0;
        }

        if ($this->data->track_outgoing == 'yes') {
            $a['calls_outgoing'] = 0;
        }

        if ($this->data->track_ringnoanswer != 'no') {
            $a['calls_unanswered'] = 0;
        }

        foreach (array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') as $d) {
            $call_distribution[$d] = $a;
        }

        foreach ($calls as $c) {
            $d = date('D', $c->timestamp);
            if (!array_key_exists($d, $call_distribution)) {
                $call_distribution[$d] = $a;
            }

            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                $call_distribution[$d]['calls_answered']++;
                $call_distribution[$d]['call_time'] += $c->calltime;
            };

            if ($this->data->track_outgoing == 'yes') {
                if ($c->event_type == 'OUT_ANSWERED' ||
                    $c->event_type == 'OUT_NOANSWER' ||
                    $c->event_type == 'OUT_BUSY'     ||
                    $c->event_type == 'OUT_FAILED'
                    ) {
                    $call_distribution[$d]['calls_outgoing']++;
                    $call_distribution[$d]['call_time'] += $c->calltime;
                };
            }

            $call_distribution[$d]['ring_time'] += $c->ringtime;

            if ($this->data->track_ringnoanswer != 'no') {
                if (!array_key_exists($d, $call_distribution)) {
                    $call_distribution[$d] = $a;
                }
                if ($c->event_type == 'RINGNOANSWER') {
                    if ($c->ringtime > 1) {
                        $call_distribution[$d]['calls_unanswered']++;
                    }
                };
            }
            if ($this->data->track_pauses == 'yes') {
                if ($c->event_type == 'STOPPAUSE' && $c->pausetime < '28800') {
                    $call_distribution[$d]['pause_time'] += $c->pausetime;
                }
            }

        }


        $this->r->status = 'OK';
        $this->r->message = 'Call distribution data will follow';
        $this->r->data = $call_distribution;

        $this->_respond();
    }


    public function update($id = false)
    {
        if (!$id || !$this->input->post()) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->Agent_model->update($id, $this->input->post());

        $this->r->status = 'OK';
        $this->r->message = 'Agent configuration updated succesfully';

        $this->_respond();
    }


    public function get_queues($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->data = array();
        $this->load->library('asterisk_manager');
        foreach ($this->Agent_model->get_queues($id) as $q) {
            $this->r->data[$q->id]['data'] = $q;
            $this->r->data[$q->id]['realtime'] = $this->asterisk_manager->queue_status($q->name);
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent queues will follow';

        $this->_respond();
    }


    public function get_settings($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->data = $this->Agent_model->get_settings($id);
        $this->r->status = 'OK';
        $this->r->message = 'Agent settings will follow';

        $this->_respond();
    }


    public function update_settings($id = false, $item = false)
    {
        if (!$id || !$item || !$this->input->post('value')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Agent_model->update_settings($id, $item, $this->input->post('value'));

        $this->r->status = 'OK';
        $this->r->message = 'Agent settings updated succesfully';

        $this->_respond();

    }

    public function get_missed_call_details($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $this->r->data = $this->Event_model->get_many_by_complex(
            array(
                'agent_id'      => $id,
                'date >'        => $date_gt,
                'date <'        => $date_lt,
                'event_type'    => 'RINGNOANSWER',
                'ringtime >'    => 1
            )
        );

        $this->r->status = 'OK';
        $this->r->message = 'Agent missed calls will follow';

        $this->_respond();

    }


    public function update_last_call($extension = false, $uniqueid = false, $src = false)
    {
        // if (!$extension) {
        //     $this->_respond();
        //     exit();
        // }

        log_to_file('DEV', "HERE1");
        $agent = $this->Agent_model->get_by('extension', $extension);
        // if (!$agent) {
        //     $this->_respond();
        //     exit();
        // }
        log_to_file('DEV', "HERE2");
        $this->Agent_model->update_last_call($agent->id, $uniqueid, $src);

        $this->r->status = 'OK';
        $this->r->message = 'Updating agent last call';

        $this->_respond();
    }


    public function get_stats_for_start() {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) {
            array_push($queue_ids, $q->id);
        }

        $agent_call_stats = $this->Call_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_pause_stats = $this->Event_model->get_agent_pause_stats_for_start_page($date_range);


        foreach ($this->data->user_agents as $a) {
            $agent_stats[$a->id] = array(
                'display_name'              => $a->display_name,
                'last_call'                 => $a->last_call,
                'extension'                 => $a->extension,
                'agent_id'                  => $a->id,
                'calls_answered'            => 0,
                'calls_outgoing'            => 0,
                'calls_missed'              => 0,
                'total_calltime'            => 0,
                'total_ringtime'            => 0,
                'total_pausetime'           => 0,
                'avg_calltime'              => 0,
                'avg_ringtime'              => 0,
                'incoming_total_calltime'    => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
            );
        }
        foreach($agent_call_stats as $s) {
            $agent_stats[$s->agent_id]['calls_answered']           = $s->calls_answered;
            $agent_stats[$s->agent_id]['calls_outgoing']           = $s->calls_outgoing;
            $agent_stats[$s->agent_id]['total_calltime']           = $s->total_calltime;
            $agent_stats[$s->agent_id]['total_ringtime']           = $s->total_ringtime;
            $agent_stats[$s->agent_id]['avg_calltime']             = ceil($s->total_calltime == 0 ? 0 : $s->total_calltime / ($s->calls_answered + $s->calls_outgoing));
            $agent_stats[$s->agent_id]['avg_ringtime']             = ceil($s->total_ringtime == 0 ? 0 : $s->total_ringtime / $s->calls_answered);
            $agent_stats[$s->agent_id]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_answered']  = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['outgoing_total_calltime']  = $s->outgoing_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered']= $s->calls_outgoing_unanswered;
        }

        foreach ($agent_event_stats as $s) {
            $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
        }

        foreach ($agent_pause_stats as $s) {
            $agent_stats[$s->agent_id]['total_pausetime'] = $s->total_pausetime;
        }

        $this->r->data = $agent_stats;

        $this->r->status = 'OK';
        $this->r->message = 'Total agent stats will follow';
        $this->_respond();
    }


    public function get_stats_for_agent_stats($agent_id = false)
    {
        if (!$agent_id) {
            $this->_respond();
            exit;
        }

        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $agent_call_stats = $this->Call_model->get_agent_stats_for_agent_stats_page($agent_id, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_agent_stats_page($agent_id, $date_range);
        $agent_call_stats->calls_missed = $agent_event_stats->calls_missed;

        $this->r->status = 'OK'; 
        $this->r->message = 'Total queue stats will follow';
        $this->r->data = $agent_call_stats;
        $this->_respond();
    }


    public function get_stats_by_queue_id($queue_id = false) {
        if (!$queue_id) {
            $this->_respond();
            exit;
        }
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $agent_call_stats = $this->Call_model->get_agent_stats_for_start_page(array($queue_id), $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page(array($queue_id), $date_range);

        foreach ($this->Queue_model->get_agents($queue_id) as $a) {
            $agent_stats[$a->id] = array(
                'display_name'              => $a->display_name,
                'calls_answered'            => 0,
                'incoming_total_calltime'    => 0,
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
            );
        }
        foreach($agent_call_stats as $s) {
            $agent_stats[$s->agent_id]['calls_answered']            = $s->calls_answered;
            $agent_stats[$s->agent_id]['incoming_total_calltime']    = $s->incoming_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;

        }
        foreach ($agent_event_stats as $s) {
            if ($s->agent_id) {
                $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
            }
        }

        $this->r->data = $agent_stats;

        $this->r->status = 'OK';
        $this->r->message = 'Total agent stats will follow';
        // echo "<pre>"; print_r($agent_call_stats); print_r($agent_event_stats); die(print_r($agent_stats));
        $this->_respond();
    }


    public function get_stats_for_all_queues($queue_id = false) {
        foreach ($this->data->user_queues as $q) {
            $queue_ids[] = $q->id;
        }

        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $agent_call_stats = $this->Call_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_ids, $date_range);

        foreach ($this->data->user_agents as $a) {
            $agent_stats[$a->id] = array(
                'display_name' => $a->display_name,
                'calls_answered' => 0,
                'calls_outgoing' => 0,
                'calls_missed' => 0,
                'total_calltime' => 0,
                'total_ringtime' => 0,
                'avg_calltime' => 0,
                'avg_ringtime' => 0,
                'agent_id' => 0,
                'total_data'=> 0,
                'calls_outgoing_answered' => 0,
                'calls_outgoing_unanswered' => 0,
                'incoming_total_calltime' => 0,
                'outgoing_total_calltime' => 0
            );
        }
        foreach($agent_call_stats as $s) {
            $agent_stats[$s->agent_id]['calls_answered'] = $s->calls_answered;
            $agent_stats[$s->agent_id]['calls_outgoing'] = $s->calls_outgoing;
            $agent_stats[$s->agent_id]['total_calltime'] = $s->total_calltime;
            $agent_stats[$s->agent_id]['total_ringtime'] = $s->total_ringtime;
            $agent_stats[$s->agent_id]['avg_calltime'] = ceil($s->total_calltime == 0 ? 0 : $s->total_calltime / ($s->calls_answered + $s->calls_outgoing));
            $agent_stats[$s->agent_id]['avg_ringtime'] = ceil($s->total_ringtime == 0 ? 0 : $s->total_ringtime / $s->calls_answered);
            $agent_stats[$s->agent_id]['total_data'] = $s;
            $agent_stats[$s->agent_id]['calls_outgoing_answered'] = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $agent_stats[$s->agent_id]['incoming_total_calltime'] = $s->incoming_total_calltime;
            $agent_stats[$s->agent_id]['outgoing_total_calltime'] = $s->outgoing_total_calltime;

        }
        foreach ($agent_event_stats as $s) {
            if ($s->agent_id) {
                $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
            }
        }

        $this->r->data = $agent_stats;

        $this->r->status = 'OK';
        $this->r->message = 'Total agent stats will follow';
        // echo "<pre>"; print_r($agent_call_stats); print_r($agent_event_stats); die(print_r($agent_stats));
        $this->_respond();
    }


}
