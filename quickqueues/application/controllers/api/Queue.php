<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Queue extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->r = new stdClass();

        // Just default to error
        $this->r->status = 'FAIL';
        $this->r->message = 'Internal error';
        $this->r->data = new stdClass();
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

        $queue = $this->Queue_model->get($id);

        if (!$queue) {
            $this->r->status = 'FAIL';
            $this->r->message = "Queue does not exist";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Queue data will follow';
        $this->r->data = $queue;

        $this->_respond();

    }

    public function get_all()
    {
        $this->r->status = 'OK';
        $this->r->message = 'Queue data will follow';
        $this->r->data = $this->data->user_queues;

        $this->_respond();
    }


    public function get_overview($as_admin = false)
    {

        $overview = array();

        $queue_ids = array();

        if ($as_admin) {
            foreach ($this->Queue_model->get_all() as $q) {
                array_push($queue_ids, $q->id);
            }
        } else {
            foreach ($this->data->user_queues as $q) {
                array_push($queue_ids, $q->id);
            }
        }

        $this->load->library('asterisk_manager');
        foreach ($this->data->user_queues as $q) {
            $overview[$q->id]['data'] = $q;
            $overview[$q->id]['current_calls'] = 0;
            $overview[$q->id]['realtime'] = $this->asterisk_manager->queue_status($q->name);
            foreach ($this->Queue_model->get_agents($q->id) as $a) {
                if ($a) {
                    $s = $this->asterisk_manager->get_agent_status($a->extension);
                    if ($s['Status'] == 1) {
                        $overview[$q->id]['current_calls']++;
                    }
                    $overview[$q->id]['agent_status'][$a->extension] = array($s, $a);
                    $overview[$q->id]['agent_calls'][$a->extension] = $this->asterisk_manager->get_agent_call($a->extension);
                }
            }

            $tt = array(
                'COMPLETECALLER', 'COMPLETEAGENT',
                'ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY',
                'OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'
            );

            $track_outgoing = $this->Config_model->get_item('app_track_outgoing');

            $overview[$q->id]['stats']['total'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => $tt,
                )
            );

            $overview[$q->id]['stats']['transfers'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'transferred' => 'yes',
                )
            );

            $overview[$q->id]['stats']['unique'] = $this->Event_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'DID',
                    'queue_id' => $queue_ids,
                )
            );

            $overview[$q->id]['stats']['answered'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                )
            );

            $overview[$q->id]['stats']['completecaller'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'COMPLETECALLER',
                )
            );

            $overview[$q->id]['stats']['completeagent'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'COMPLETEAGENT',
                )
            );

            if ($this->data->config->app_track_ivrabandon == 'yes') {
                $overview[$q->id]['stats']['unanswered'] = $this->Call_model->count_by_complex(
                    array(
                        'queue_id' => $q->id,
                        'date >' => QQ_TODAY_START,
                        'date <' => QQ_TODAY_END,
                        'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'IVRABANDON'),
                    )
                );
            } else {
                $overview[$q->id]['stats']['unanswered'] = $this->Call_model->count_by_complex(
                    array(
                        'queue_id' => $q->id,
                        'date >' => QQ_TODAY_START,
                        'date <' => QQ_TODAY_END,
                        'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                    )
                );
            }

            $overview[$q->id]['stats']['abandon'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'ABANDON',
                )
            );

            if ($this->data->config->app_track_ivrabandon == 'yes') {
                $overview[$q->id]['stats']['ivrabandon'] = $this->Call_model->count_by_complex(
                    array(
                        'queue_id' => $q->id,
                        'date >' => QQ_TODAY_START,
                        'date <' => QQ_TODAY_END,
                        'event_type' => 'IVRABANDON',
                    )
                );
            }

            $overview[$q->id]['stats']['exitempty'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'EXITEMPTY',
                )
            );

            $overview[$q->id]['stats']['exitwithtimeout'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'EXITWITHTIMEOUT',
                )
            );

            $overview[$q->id]['stats']['exitwithkey'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => 'EXITWITHTIMEOUT',
                )
            );

            $overview[$q->id]['stats']['outgoing'] = $this->Event_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => array('OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY', 'OUT_FAILED'),
                )
            );

            $overview[$q->id]['stats']['called_back'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'called_back' => 'yes',
                )
            );


            $overview[$q->id]['stats']['called_back_abandon'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'called_back' => 'yes',
                    'event_type' => 'ABANDON',
                )
            );


            $overview[$q->id]['stats']['called_back_exit_empty'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'called_back' => 'yes',
                    'event_type' => 'EXITEMPTY',
                )
            );

            $overview[$q->id]['stats']['called_back_exitwithtimeout'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'called_back' => 'yes',
                    'event_type' => 'EXITWITHTIMEOUT',
                )
            );

            $overview[$q->id]['stats']['called_back_exitwithkey'] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'called_back' => 'yes',
                    'event_type' => 'EXITWITHKEY',
                )
            );

            $overview[$q->id]['stats']['total_calltime'] = $this->Event_model->sum_by_complex(
                'calltime',
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
                )
            );

            $overview[$q->id]['stats']['total_holdtime'] = $this->Event_model->sum_by_complex(
                'holdtime',
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END
                )
            );
            $overview[$q->id]['stats']['total_ringtime'] = $this->Event_model->sum_by_complex(
                'ringtime',
                array(
                    'queue_id' => $q->id,
                    'event_type' => 'CONNECT',
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END
                )
            );

            $overview[$q->id]['stats']['origposition'] = $this->Event_model->avg_by_complex(
                'origposition',
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END
                )
            );

            $overview[$q->id]['stats']['origposition_max'] = $this->Event_model->max_by_complex(
                'origposition',
                array(
                    'queue_id' => $q->id,
                    'date >' => QQ_TODAY_START,
                    'date <' => QQ_TODAY_END
                )
            );

        }

        $this->r->status = 'OK';
        $this->r->message = 'Queue overview will follow';
        $this->r->data = $overview;

        $this->_respond();

    }


    public function get_stats($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;


        $this->r->data->calls_transferred = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'transferred' => 'yes',
            )
        );


        $tt = array(
            'COMPLETECALLER', 'COMPLETEAGENT',
            'ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY'
        );

        $track_outgoing = $this->Config_model->get_item('app_track_outgoing');
        if ($track_outgoing == 'yes') {
            $tt[] = 'OUT_FAILED';
            $tt[] = 'OUT_BUSY';
            $tt[] = 'OUT_NOANSWER';
            $tt[] = 'OUT_ANSWERED';
        }

        $this->r->data->calls_outgoing_external = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) >' => 4,
            )
        );

        $this->r->data->calls_outgoing_internal = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) <=' => 4,
            )
        );

        $track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');
        if ($track_duplicates > 0) {
            $this->r->data->calls_duplicate = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'duplicate' => 'yes',
                    'event_type' => array(
                        'COMPLETECALLER', 'COMPLETEAGENT',
                        'ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY'
                    )
                )
            );
        }

        $this->r->data->calls_total = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => $tt,
            )
        );

        unset($tt);

        $this->r->data->calls_answered = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
            )
        );

        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $this->r->data->answered_elsewhere = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'ABANDON',
                    'answered_elsewhere >' => 1
                )
            );

            $a_without_service = array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'no',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                'answered_elsewhere' => 'isnull'
            );

            if ($this->data->config->app_ignore_abandon > 0) {
                $a_without_service['waittime >='] = $this->data->config->app_ignore_abandon;
            }

            $this->r->data->calls_without_service = $this->Call_model->count_by_complex($a_without_service);
            unset($a_without_service);
        }

        $this->r->data->answered_within_sla = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                'holdtime <' => $this->data->config->queue_sla_hold_time
            )
        );

        $this->r->data->calls_completecaller = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'COMPLETECALLER',
            )
        );

        $this->r->data->calls_completeagent = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'COMPLETEAGENT',
            )
        );

        if ($this->data->config->app_ignore_abandon > 0) {
            $this->r->data->calls_unanswered_ignored = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'waittime <' => $this->data->config->app_ignore_abandon,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );
        }

        $this->r->data->calls_unanswered = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
            )
        );

        $this->r->data->calls_abandon = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'ABANDON',
            )
        );

        $this->r->data->calls_exitempty = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITEMPTY',
            )
        );

        $this->r->data->calls_exitwithtimeout = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->calls_exitwithkey = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITWITHKEY',
            )
        );

        $this->r->data->calls_unique = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'DID',
            )
        );

        $this->r->data->calls_unique_per_did = array();
        foreach ($this->Event_model->get_unique_fields_by('did', 'queue_id', $id) as $did) {
            $this->r->data->calls_unique_per_did[$did->did] = $this->Event_model->count_by_complex(
                array(
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'DID',
                    'did' => $did->did,
                )
            );
        }

        $this->r->data->calls_outgoing = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY', 'OUT_FAILED'),
            )
        );

        $this->r->data->called_back = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
            )
        );

        $this->r->data->called_back_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'ABANDON',
            )
        );

        $this->r->data->called_back_exitempty = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITEMPTY',
            )
        );

        $this->r->data->called_back_exitwithtimeout = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->called_back_exitwithkey = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITWITHKEY',
            )
        );

        $this->r->data->total_calltime = $this->Event_model->sum_by_complex(
            'calltime',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $this->r->data->max_calltime = $this->Event_model->max_by_complex(
            'calltime',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $this->r->data->total_holdtime = $this->Event_model->sum_by_complex(
            'holdtime',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->max_holdtime = $this->Event_model->max_by_complex(
            'holdtime',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $this->r->data->total_ringtime = $this->Event_model->sum_by_complex(
            'ringtime',
            array(
                'queue_id' => $id,
                'event_type' => 'CONNECT',
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->position = $this->Event_model->avg_by_complex(
            'position',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->origposition = $this->Event_model->avg_by_complex(
            'origposition',
            array(
                'queue_id' => $id,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );


        $this->r->status = 'OK';
        $this->r->message = 'Queue stats will follow';
        $this->_respond();

    }

    public function get_stats_total($as_admin = false)
    {
        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $queue_ids = array();

        if ($as_admin) {
            foreach ($this->Queue_model->get_all() as $q) {
                array_push($queue_ids, $q->id);
            }
        } else {
            foreach ($this->data->user_queues as $q) {
                array_push($queue_ids, $q->id);
            }
        }

        $tt = array(
            'COMPLETECALLER', 'COMPLETEAGENT',
            'ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY'
        );

        $this->r->data->calls_transferred = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'transferred' => 'yes',
            )
        );

        $track_outgoing = $this->Config_model->get_item('app_track_outgoing');
        if ($track_outgoing == 'yes') {
            $tt[] = 'OUT_FAILED';
            $tt[] = 'OUT_BUSY';
            $tt[] = 'OUT_NOANSWER';
            $tt[] = 'OUT_ANSWERED';
        }

        $this->r->data->calls_outgoing_external = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) >' => 4,
            )
        );

        $this->r->data->calls_outgoing_external_answered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'OUT_ANSWERED',
                'LENGTH(dst) >' => 4,
            )
        );

        $this->r->data->calls_outgoing_external_failed = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER'),
                'LENGTH(dst) >' => 4,
            )
        );

        $this->r->data->calls_outgoing_internal = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) <=' => 4,
            )
        );

        $track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');
        if ($track_duplicates > 0) {
            $this->r->data->calls_duplicate = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'duplicate' => 'yes',
                    'event_type' => array(
                        'COMPLETECALLER', 'COMPLETEAGENT',
                        'ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY'
                    )
                )
            );
        }

        $this->r->data->calls_outgoing_per_queue = array();
        foreach ($this->data->user_queues as $q) {
            $this->r->data->calls_outgoing_per_queue[$q->display_name] = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $q->id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'LENGTH(dst) >' => 4,
                    'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                )
            );
        }

        $this->r->data->answered_within_sla = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                'holdtime <' => $this->data->config->queue_sla_hold_time
            )
        );

        unset($tt);

        $this->r->data->calls_answered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
            )
        );

        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $this->r->data->answered_elsewhere = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'ABANDON',
                    'answered_elsewhere >' => 1
                )
            );

            if ($this->data->config->app_track_ivrabandon == 'yes') {
                $a_without_service = array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'called_back' => 'no',
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'IVRABANDON'),
                    'answered_elsewhere' => 'isnull'
                );
            } else {
                $a_without_service = array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'called_back' => 'no',
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                    'answered_elsewhere' => 'isnull'
                );
            }


            if ($this->data->config->app_ignore_abandon > 0) {
                $a_without_service['waittime >='] = $this->data->config->app_ignore_abandon;
            }

            $this->r->data->calls_without_service = $this->Call_model->count_by_complex($a_without_service);
            unset($a_without_service);
        }

        if ($this->data->config->app_track_incomingoffwork == 'yes') {
            $this->r->data->calls_offwork = $this->Call_model->count_by_complex(
                array(
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'INCOMINGOFFWORK',
                )
            );
        }

        $this->r->data->calls_completecaller = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'COMPLETECALLER',
            )
        );

        $this->r->data->calls_completeagent = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'COMPLETEAGENT',
            )
        );

        if ($this->data->config->app_ignore_abandon > 0) {
            $this->r->data->calls_unanswered_ignored = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'waittime <' => $this->data->config->app_ignore_abandon,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );
        }

        if ($this->data->config->app_track_ivrabandon == 'yes') {
            $this->r->data->calls_unanswered = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'IVRABANDON'),
                )
            );
        } else {
            $this->r->data->calls_unanswered = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );
        }


        $this->r->data->calls_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'ABANDON',
            )
        );

        if ($this->data->config->app_track_ivrabandon == 'yes') {
            $this->r->data->calls_ivrabandon = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'IVRABANDON',
                )
            );
        }


        $this->r->data->calls_exitempty = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITEMPTY',
            )
        );

        $this->r->data->calls_exitwithtimeout = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->calls_exitwithkey = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'EXITWITHKEY',
            )
        );

        $this->r->data->calls_unique = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'DID',
            )
        );

        $this->r->data->calls_unique_per_did = array();
        foreach ($this->Event_model->get_unique_fields_by('did', 'queue_id', $queue_ids) as $did) {
            $this->r->data->calls_unique_per_did[$did->did] = $this->Event_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'DID',
                    'did' => $did->did,
                )
            );
        }

        $this->r->data->called_back = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
            )
        );

        $this->r->data->called_back_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'ABANDON',
            )
        );
        $this->r->data->called_back_abandon_nop = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nop',
                'event_type' => 'ABANDON',
            )
        );
        $this->r->data->called_back_abandon_nah = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nah',
                'event_type' => 'ABANDON',
            )
        );

        $this->r->data->called_back_exitempty = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITEMPTY',
            )
        );
        $this->r->data->called_back_exitempty_nop = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nop',
                'event_type' => 'EXITEMPTY',
            )
        );
        $this->r->data->called_back_exitempty_nah = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nah',
                'event_type' => 'EXITEMPTY',
            )
        );

        $this->r->data->called_back_exitwithtimeout = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->called_back_exitwithtimeout_nah = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nah',
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->called_back_exitwithtimeout_nop = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nop',
                'event_type' => 'EXITWITHTIMEOUT',
            )
        );

        $this->r->data->called_back_exitwithkey = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'EXITWITHKEY',
            )
        );

        $this->r->data->called_back_exitwithkey_nah = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nah',
                'event_type' => 'EXITWITHKEY',
            )
        );

        $this->r->data->called_back_exitwithkey_nop = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nop',
                'event_type' => 'EXITWITHKEY',
            )
        );

        if ($this->data->config->app_track_outgoing != 'no') {
            $ct = array('COMPLETECALLER', 'COMPLETEAGENT', 'OUT_ANSWERED');
        } else {
            $ct = array('COMPLETECALLER', 'COMPLETEAGENT');
        }

        $this->r->data->total_calltime = $this->Event_model->sum_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => $ct
            )
        );

        $this->r->data->max_calltime = $this->Event_model->max_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $this->r->data->total_holdtime = $this->Event_model->sum_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->max_holdtime = $this->Event_model->max_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $this->r->data->total_ringtime = $this->Event_model->sum_by_complex(
            'ringtime',
            array(
                'queue_id' => $queue_ids,
                'event_type' => 'CONNECT',
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->origposition_max = $this->Event_model->max_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->origposition = $this->Event_model->avg_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $this->r->data->unique_callers = $this->Call_model->get_unique_fields_by_complex(
            'src',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETEAGENT', 'COMPLETECALLER'),
            )
        );

        if ($this->data->config->app_display_survey_scores == 'yes') {
            $this->r->data->avg_survey_score = $this->Call_model->avg_by_complex(
                'survey_result',
                array(
                    'queue_id'          => $queue_ids,
                    'date >'            => $date_gt,
                    'date <'            => $date_lt,
                    'survey_result >='  => 1
                ),
                'ROUND'
            );
        }

        $this->r->data->unique_callers = count($this->r->data->unique_callers);

        $this->r->data->calls_total = $this->r->data->calls_answered + $this->r->data->calls_unanswered + $this->r->data->calls_outgoing_external;

        $this->r->status = 'OK';
        $this->r->message = 'Total queue stats will follow';
        $this->_respond();

    }


    public function get_stats_by_queue($as_admin = false)
    {
        $queues = array();

        if ($as_admin) {
            foreach ($this->Queue_model->get_all() as $q) {
                $queues[$q->id] = $q->display_name;
            }
        } else {
            foreach ($this->data->user_queues as $q) {
                $queues[$q->id] = $q->display_name;
            }
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $calls = $this->Event_model->get_many_by_complex(
            array(
                'date >' => $date_gt,
                'date <' => $date_lt,
            )
        );

        $queue_distribution = array();

        $a = array(
            'calls_answered'    => 0,
            'calls_unanswered'  => 0,
            'calls_outgoing'    => 0,
            'call_time'         => 0,
            'hold_time'         => 0,
        );

        foreach ($queues as $qid => $qname) {
            $queue_distribution[$qname] = $a;
        }

        foreach ($calls as $c) {

            // $this should fix #323, when we hit error for manager users
            if (!array_key_exists($c->queue_id, $queues)) {
                continue;
            }

            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                $queue_distribution[$queues[$c->queue_id]]['calls_answered']++;
                $queue_distribution[$queues[$c->queue_id]]['call_time'] += $c->calltime;
                $queue_distribution[$queues[$c->queue_id]]['hold_time'] += $c->holdtime;
                $queue_distribution[$queues[$c->queue_id]]['hold_time'] += $c->waittime;
            };

            if ($c->event_type == 'ABANDON'         ||
                $c->event_type == 'EXITWITHTIMEOUT' ||
                $c->event_type == 'EXITWITHKEY'     ||
                $c->event_type == 'EXITEMPTY') {
                    $queue_distribution[$queues[$c->queue_id]]['calls_unanswered']++;
                    $queue_distribution[$queues[$c->queue_id]]['hold_time'] += $c->holdtime;
                    $queue_distribution[$queues[$c->queue_id]]['hold_time'] += $c->waittime;
            };

            if ($c->event_type == 'OUT_FAILED'      ||
                $c->event_type == 'OUT_BUSY'        ||
                $c->event_type == 'OUT_NOANSWER'    ||
                $c->event_type == 'OUT_ANSWERED') {
                    $queue_distribution[$queues[$c->queue_id]]['calls_outgoing']++;
            };


        }

        $this->r->status = 'OK';
        $this->r->message = 'Queue stats distribution data will follow';
        $this->r->data = $queue_distribution;

        $this->_respond();
    }


    public function get_realtime_data($id = false)
    {

        //$id = false;//$id == '_' ? false : $id;

        //if($this->checkOrAddKey($key))
        {
            /*
            $this->r->status  = 'Duplicate';
            $this->r->message = 'Up to date';
            $this->_respond();
            return;
            */
        }

       // $entryMame = 'realtime_data'.($id != false ? '_'.$id : '');
        //$cacheData = $this->get_or_add_cached_data($entryMame);

        // if($cacheData)
        // {
        //     $this->r->status  = 'OK';
        //     $this->r->message = 'Queue realtime data will follow (Cached)';
        //     $this->r->data    = $cacheData;
        //     $this->_respond();
        //     return;
        // }
        
        
        $this->load->library('asterisk_manager');
    
        if (!$id) 
        {
            $queues = array();
            foreach ($this->data->user_queues as $q) 
            {
                $queueStatus = $this->asterisk_manager->queue_status($q->name);
    
                // Check if $queueStatus has the 'data' key before accessing it
                if (isset($queueStatus['data'])) 
                {
                    $queues[] = $queueStatus;
                }
            }
            $this->r->data = $queues;
        } 
        else 
        {
            $queue = $this->Queue_model->get($id);
            $queueStatus = $this->asterisk_manager->queue_status($queue->name);
    
            // Check if $queueStatus has the 'data' key before accessing it
            if (isset($queueStatus['data'])) 
            {
                $this->r->data = $queueStatus;
            }
        }
    
        $queueData = $this->Queue_model->get_queue_entries();
    
        foreach ($this->r->data as &$queueStatus) {
            if (isset($queueStatus['data']['Queue'])) {
                foreach ($queueData as $queueEntry) {
                    if ($queueStatus['data']['Queue'] == $queueEntry['name']) 
                    {
                        // Check if 'data' array exists before adding 'displayName'
                        if (!isset($queueStatus['data'])) {
                            $queueStatus['data'] = array();
                        }
                        $queueStatus['data']['displayName'] = $queueEntry['display_name'];
                    }
                }
            }
        }

       // $this->get_or_add_cached_data($entryMame,$this->r->data);
        
        $this->r->status  = 'OK';
        $this->r->message = 'Queue realtime data will follow';
        $this->_respond();
    }
    


    public function get_agents($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
        } else {
            $this->r->status = 'OK';
            $this->r->message = 'Queue agents will follow';
            $this->r->data = $this->Queue_model->get_agents($id);
            $this->_respond();
        }
    }


    public function get_agent_stats($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Something went wrong";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $agents = $this->Queue_model->get_agents($id);

        foreach ($agents as $agent) {
            if (!$agent) {
                log_to_file('WEB_NOTICE', "api/queue/get_agent_stats($id) got non-existent agent");
                continue;
            }
            $agents[$agent->id]->stats = new stdClass;

            $agents[$agent->id]->stats->answered = $this->Event_model->count_by_complex(
                array(
                    'agent_id' => $agent->id,
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
                )
            );

            $agents[$agent->id]->stats->total_calltime = $this->Call_model->sum_by_complex(
                'calltime',
                array(
                    'agent_id' => $agent->id,
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT', 'OUT_ANSWERED')
                )
            );

            $agents[$agent->id]->stats->total_ringtime = $this->Event_model->sum_by_complex(
                'ringtime',
                array(
                    'agent_id' => $agent->id,
                    'queue_id' => $id,
                    'event_type' => 'CONNECT',
                    'date >' => $date_gt,
                    'date <' => $date_lt
                )
            );

            $agents[$agent->id]->stats->outgoing = $this->Event_model->count_by_complex(
                array(
                    'agent_id' => $agent->id,
                    'queue_id' => $id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => array('OUT_BUSY', 'OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER'),
                )
            );

            $agents[$agent->id]->stats->missed = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $agent->id,
                    'queue_id'      => $id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'event_type'    => 'RINGNOANSWER',
                    'ringtime >'    => 1000,
                )
            );
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent statistics will follow';
        $this->r->data = $agents;

        $this->_respond();

    }


    public function get_agent_realtime_data($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Something went wrong";
            $this->_respond();
            exit();
        }

        $realtime_data = array();

        $this->load->library('asterisk_manager');


        foreach ($this->Queue_model->get_agents($id) as $a) {
            $realtime_data[$a->id]['data'] = $a;
            $realtime_data[$a->id]['status'] = $this->asterisk_manager->get_agent_status($a->extension);
            $realtime_data[$a->id]['current_call'] = $this->asterisk_manager->get_agent_call($a->extension);
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent realtime data will follow';
        $this->r->data = $realtime_data;

        $this->_respond();

    }


    public function get_agent_overview($id = false) {

        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Something went wrong";
            $this->_respond();
            exit();
        }

        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $t_agents = $this->Queue_model->get_agents($id);

        $overview = array();

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
                    'queue_id' => $id,
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

            $overview[$a->id]['calls_missed'] = $this->Event_model->count_by_complex(
                array(
                    'agent_id'      => $a->id,
                    'date >'        => QQ_TODAY_START,
                    'date <'        => QQ_TODAY_END,
                    'event_type'    => 'RINGNOANSWER',
                    'queue_id'      => $id,
                    'ringtime >'    => 1,
                )
            );

            $overview[$a->id]['call_time'] = $this->Event_model->sum_by_complex(
                'calltime',
                array(
                    'agent_id'      => $a->id,
                    'date >'        => QQ_TODAY_START,
                    'date <'        => QQ_TODAY_END,
                    'queue_id'      => $id,
                )
            );

            // if ($this->data->track_pauses == 'yes') {
            //     $overview[$a->id]['pause_time'] = $this->Event_model->sum_by_complex(
            //         'pausetime',
            //         array(
            //             'agent_id'      => $a->id,
            //             'date >'        => QQ_TODAY_START,
            //             'date <'        => QQ_TODAY_END,
            //             'pausetime <'   => '28800', // Ignore large pauses, they are not pauses, rather end of work
            //             'event_type'    => 'STOPPAUSE'
            //         )
            //     );
            // }
        }

        $this->r->status = 'OK';
        $this->r->message = 'Agent overview will follow';
        $this->r->data = $overview;

        $this->_respond();

    }


    public function get_config($id = false, $name = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $config = $this->Queue_model->get_config($id, $name);

        $this->r->status = 'OK';
        $this->r->message = 'Queue configuration will follow';
        $this->r->data = $config;

        $this->_respond();

    }


    public function set_config($id = false, $name = false)
    {
        if (!$id || !$name || !$this->input->post('value')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Queue_model->set_config($id, $name, $this->input->post('value'));

        $this->r->status = 'OK';
        $this->r->message = 'Queue configuration updated succesfully';

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
        $this->Queue_model->update($id, $this->input->post());

        $this->r->status = 'OK';
        $this->r->message = 'Queue configuration updated succesfully';

        $this->_respond();
    }


    public function get_total_stats_for_start()
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) 
        {  
            array_push($queue_ids, $q->id);
        }

		$stats_for_start = $this->Call_model->get_stats_for_start($queue_ids, $date_range);
		$local_calls_for_start = $this->Call_model->get_local_calls_for_start($date_range, false);
		
		$stats_for_start->calls_total_local = $local_calls_for_start->calls_total_local;
		
		if (!isset($local_calls_for_start->calls_total_local)) {
			$stats_for_start->calls_total_local = 0; // Set default value if the property does not exist
		}else {
			$stats_for_start->calls_total_local = $local_calls_for_start->calls_total_local;
		}

		$this->r->data = $stats_for_start;
		
        $this->r->status = 'OK';
        $this->r->message = 'Total queue stats will follow';
        $this->_respond();
    }

    public function get_total_stats_for_all_queues()
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) 
        {
            array_push($queue_ids, $q->id);

        }

        $this->r->data    = $this->Call_model->get_stats_for_start($queue_ids, $date_range);
        $this->r->status  = 'OK';
        $this->r->message = 'Total queue stats will follow';
        $this->_respond();
    }


    public function get_stats_for_queue_stats($queue_id = false)
    {
        if (!$queue_id) {
            $this->_respond();
            exit;
        }

        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $this->r->data = $this->Call_model->get_stats_for_start($queue_id, $date_range);

        $this->r->status = 'OK';
        $this->r->message = 'Total queue stats will follow';
        $this->_respond();
    }


    public function get_stats_for_start()
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'Callback') !== false || stripos($q->display_name, 'callback') !== false) 
            {
                continue;
            }
            array_push($queue_ids, $q->id);
        }

        $queue_call_stats = $this->Call_model->get_queue_stats_for_start_page($queue_ids, $date_range);
        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'Callback') !== false || stripos($q->display_name, 'callback') !== false) 
            {
                continue;
            }

            $queue_stats[$q->id] = array(
                'display_name'             => $q->display_name,
                'calls_total'              => 0,
                'calls_answered'           => 0,
                'calls_outgoing'           => 0,
                'calls_missed'             => 0,
                'total_calltime'           => 0,
                'total_holdtime'           => 0,
                'avg_calltime'             => 0,
                'avg_holdtime'             => 0,
                'origposition_avg'         => 0,
                'calls_outgoing_answered'  => 0,
                'calls_outgoing_unanswered'=> 0,
                'calls_missed'             => 0,
            );
        }

        foreach($queue_call_stats as $s) {
            $queue_stats[$s->queue_id]['calls_answered']           = $s->calls_answered;
            $queue_stats[$s->queue_id]['calls_outgoing']           = $s->calls_outgoing;
            $queue_stats[$s->queue_id]['calls_missed']             = $s->calls_unanswered;
            $queue_stats[$s->queue_id]['total_calltime']           = $s->total_calltime;
            $queue_stats[$s->queue_id]['total_holdtime']           = $s->total_holdtime;
            $queue_stats[$s->queue_id]['avg_calltime']             = ceil($s->total_calltime == 0 || ($s->calls_answered + $s->calls_outgoing) == 0 ? 0 : $s->total_calltime / ($s->calls_answered + $s->calls_outgoing));
            $queue_stats[$s->queue_id]['avg_holdtime']             = ceil(($s->total_holdtime + $s->total_waittime) == 0 || ($s->calls_answered + $s->calls_unanswered) == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / ($s->calls_answered + $s->calls_unanswered));
            $queue_stats[$s->queue_id]['origposition_avg']         = ceil($s->origposition_avg);
            $queue_stats[$s->queue_id]['calls_outgoing_answered']  = $s->calls_outgoing_answered;
            $queue_stats[$s->queue_id]['calls_outgoing_unanswered']= $s->calls_outgoing_unanswered;
            $queue_stats[$s->queue_id]['incoming_total_calltime']  = $s->incoming_total_calltime;
            $queue_stats[$s->queue_id]['outgoing_total_calltime']  = $s->outgoing_total_calltime;
        }

        $this->r->data = $queue_stats;
        $this->r->status = 'OK';
        $this->r->message = 'Total queue stats will follow';
        $this->_respond();
    }


    public function get_hourly_stats_for_start()
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
                array_push($queue_ids, $q->id);
            }
        }

        $hourly_call_stats = $this->Call_model->get_hourly_stats_for_start_page($queue_ids, $date_range);
        for ($i=0; $i < 24; $i++) {
            $h = $i < 10 ? '0'.$i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'           => 0,
                'calls_missed'             => 0,
                'calls_outgoing'           => 0,
                'total_calltime'           => 0,
                'total_holdtime'           => 0,
                'avg_calltime'             => 0,
                'avg_holdtime'             => 0,
                'origposition_avg'         => 0,
                'hour'                     => $h,
                'incoming_total_calltime'   => 0,
                'calls_outgoing_answered'  => 0,
                'calls_outgoing_unanswered'=> 0,
                'outgoing_total_calltime'  => 0,
            );
        }
        foreach($hourly_call_stats as $s) {
            $hourly_stats[$s->hour]['calls_answered']            = $s->calls_answered;
            $hourly_stats[$s->hour]['calls_outgoing']            = $s->calls_outgoing;
            $hourly_stats[$s->hour]['calls_missed']              = $s->calls_unanswered;
            $hourly_stats[$s->hour]['total_calltime']            = $s->total_calltime;
            $hourly_stats[$s->hour]['total_holdtime']            = $s->total_holdtime;
            $hourly_stats[$s->hour]['avg_calltime']              = ceil($s->total_calltime == 0 ? 0 : $s->total_calltime / ($s->calls_answered + $s->calls_outgoing));
            $hourly_stats[$s->hour]['avg_holdtime']              = ceil(($s->total_holdtime + $s->total_waittime) == 0 || $s->calls_unanswered == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / $s->calls_unanswered);
            $hourly_stats[$s->hour]['origposition_avg']          = ceil($s->origposition_avg);
            $hourly_stats[$s->hour]['incoming_total_calltime']    = $s->incoming_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $hourly_stats[$s->hour]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $hourly_stats[$s->hour]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
        }

        $this->r->data = $hourly_stats;

        $this->r->status = 'OK';
        $this->r->message = 'Hourly queue stats will follow';
        $this->_respond();
    }

    public function get_hourly_stats_for_queue_stats($queue_id = false)
    {
        if (!$queue_id) {
            $this->_respond();
            exit();
        }
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $hourly_call_stats = $this->Call_model->get_hourly_stats_for_start_page(array($queue_id), $date_range);
        for ($i=0; $i < 24; $i++) {
            $h = $i < 10 ? '0'.$i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'           => 0,
                'calls_missed'             => 0,
                'incoming_total_calltime'   => 0,
                'calls_outgoing_answered'  => 0,
                'calls_outgoing_unanswered'=> 0,
                'outgoing_total_calltime'  => 0,
                'avg_holdtime'             => 0,
                'hour'                     => $h
            );
        }
        foreach($hourly_call_stats as $s) {
            $hourly_stats[$s->hour]['calls_answered']            = $s->calls_answered;
            $hourly_stats[$s->hour]['calls_missed']              = $s->calls_unanswered;
            $hourly_stats[$s->hour]['incoming_total_calltime']    = $s->incoming_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $hourly_stats[$s->hour]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $hourly_stats[$s->hour]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $hourly_stats[$s->hour]['avg_holdtime']              = ceil(($s->total_holdtime + $s->total_waittime) == 0 || $s->calls_unanswered == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / $s->calls_unanswered);
        }

        $this->r->data = $hourly_stats;

        $this->r->status = 'OK';
        $this->r->message = 'Hourly queue stats will follow';
        $this->_respond();
    }


    public function get_daily_stats_for_start()
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;
        $queue_ids = array();

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
                array_push($queue_ids, $q->id);
            }
        }

        // Generate the list of dates within the specified date range
        $start_date = new DateTime($date_range['date_gt']);
        $end_date = new DateTime($date_range['date_lt']);
        $interval = new DateInterval('P1D'); // 1 day interval
        $date_range_list = new DatePeriod($start_date, $interval, $end_date);
        $dates = [];
        foreach ($date_range_list as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $daily_call_stats = $this->Call_model->get_daily_stats_for_start_page($queue_ids, $date_range);

        $daily_stats = array(); // Initialize the array here

        // Fill in missing dates with default values
        foreach ($dates as $date) {
            $found = false;
            foreach ($daily_call_stats as $i) {
                if ($i->date == $date) {
                    $found = true;
                    // Calculate values as before
                    if (($i->calls_answered + $i->calls_outgoing) == 0) {
                        $avg_calltime = '00:00:00';
                    } else {
                        $avg_calltime = sec_to_time($i->total_calltime / ($i->calls_answered + $i->calls_outgoing));
                    }

                    if ($i->calls_unanswered == 0) {
                        $avg_holdtime = '00:00:00';
                    } else {
                        $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $i->calls_unanswered);
                    }

                    $daily_stats[] = array(
                        'day'                       => $i->date,
                        'calls_total'               => $i->calls_answered + $i->calls_outgoing + $i->calls_unanswered,
                        'calls_answered'            => $i->calls_answered,
                        'calls_missed'              => $i->calls_unanswered,
                        'calls_outgoing'            => $i->calls_outgoing,
                        'total_calltime'            => sec_to_time($i->total_calltime),
                        'avg_calltime'              => $avg_calltime,
                        'total_holdtime'            => sec_to_time($i->total_holdtime),
                        'avg_holdtime'              => $avg_holdtime,
                        'origposition_avg'          => ceil($i->origposition_avg),
                        'calls_outgoing_answered'   => $i->calls_outgoing_answered,
                        'calls_outgoing_unanswered' => $i->calls_outgoing_unanswered,
                        'incoming_total_calltime'    => $i->incoming_total_calltime,
                        'outgoing_total_calltime'   => $i->outgoing_total_calltime,
                    );
                    break;
                }
            }
            if (!$found) {
                // If the date is not found in $daily_call_stats, set all parameters to 0
                $daily_stats[] = array(
                    'day'                       => $date,
                    'calls_total'               => 0,
                    'calls_answered'            => 0,
                    'calls_missed'              => 0,
                    'calls_outgoing'            => 0,
                    'total_calltime'            => '00:00:00',
                    'avg_calltime'              => '00:00:00',
                    'total_holdtime'            => '00:00:00',
                    'avg_holdtime'              => '00:00:00',
                    'origposition_avg'          => 0,
                    'calls_outgoing_answered'   => 0,
                    'calls_outgoing_unanswered' => 0,
                    'incoming_total_calltime'   => 0,
                    'outgoing_total_calltime'   => 0,
                );
            }
        }

        $this->r->data = $daily_stats;
        $this->r->status = 'OK';
        $this->r->message = 'Daily queue stats will follow';
        $this->_respond();
    }



    public function get_daily_stats_for_queue_stats($queue_id = false)
    {
        if (!$queue_id) {
            $this->_respond();
            exit();
        }

        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        // Generate the list of dates within the specified date range
        $start_date = new DateTime($date_range['date_gt']);
        $end_date = new DateTime($date_range['date_lt']);
        $interval = new DateInterval('P1D'); // 1 day interval
        $date_range_list = new DatePeriod($start_date, $interval, $end_date);
        $dates = [];
        foreach ($date_range_list as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $daily_call_stats = $this->Call_model->get_daily_stats_for_start_page(array($queue_id), $date_range);

        $daily_stats = array(); // Initialize the array here

        // Fill in missing dates with default values
        foreach ($dates as $date) {
            $found = false;
            foreach ($daily_call_stats as $i) {
                if ($i->date == $date) {
                    $found = true;
                    // Calculate values as before
                    if (($i->calls_answered + $i->calls_outgoing) == 0) {
                        $avg_calltime = '00:00:00';
                    } else {
                        $avg_calltime = sec_to_time($i->total_calltime / ($i->calls_answered + $i->calls_outgoing));
                    }

                    if ($i->calls_unanswered == 0) {
                        $avg_holdtime = '00:00:00';
                    } else {
                        $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $i->calls_unanswered);
                    }

                    $daily_stats[] = array(
                        'day'                       => $i->date,
                        'calls_answered'            => $i->calls_answered,
                        'incoming_total_calltime'    => $i->incoming_total_calltime,
                        'calls_missed'              => $i->calls_unanswered,
                        'calls_outgoing_answered'   => $i->calls_outgoing_answered,
                        'outgoing_total_calltime'   => $i->outgoing_total_calltime,
                        'calls_outgoing_unanswered' => $i->calls_outgoing_unanswered,
                        'avg_holdtime'              => $avg_holdtime,
                    );
                    break;
                }
            }
            if (!$found) {
                // If the date is not found in $daily_call_stats, set all parameters to 0
                $daily_stats[] = array(
                    'day'                       => $date,
                    'calls_answered'            => 0,
                    'incoming_total_calltime'    => 0,
                    'calls_missed'              => 0,
                    'calls_outgoing_answered'   => 0,
                    'outgoing_total_calltime'   => 0,
                    'calls_outgoing_unanswered' => 0,
                    'avg_holdtime'              => '00:00:00',
                );
            }
        }

        $this->r->data = $daily_stats;
        $this->r->status = 'OK';
        $this->r->message = 'Daily queue stats will follow';
        $this->_respond();
    }


    public function get_basic_stats_for_today($id = false)
    {
        if (!$id) 
        {
            foreach ($this->data->user_queues as $q) 
            {
                if (stripos($q->display_name, 'Callback') === false) 
                {
                    $id[] = $q->id;
                }
            }
        }
		
		/*
        $this->r->data->calls_answered = $this->Call_model->count_by_complex(
            array(
                'date >' => QQ_TODAY_START,
                'queue_id' => $id,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );
		
        $this->r->data->calls_unanswered = $this->Call_model->count_by_complex(
            array(
                'date >' => QQ_TODAY_START,
                'queue_id' => $id,
                'event_type' => array('ABANDON','EXITEMPTY', 'EXITWITHTIMEOUT')
            )
        );

		
        $this->r->data->calls_without_service = $this->Call_model->count_by_complex(
            array(
                'date >' => QQ_TODAY_START,
                'queue_id' => $id,
                'called_back' => 'no',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                'answered_elsewhere' => 'isnull',
                'waittime >=' => 5
            )
        );
		*/
		// New count_by_complex_with_exclusion to support additional exclusions
        $this->r->data->calls_answered = $this->Call_model->count_by_complex_with_exclusion(
            array(
                'date >' => QQ_TODAY_START,
                'queue_id' => $id,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            ),
			function($db) {
				$db->group_start()
				   ->where('src NOT IN (SELECT extension FROM users)')
				   ->or_where('dst NOT IN (SELECT extension FROM users)')
				   ->group_end();
			}
        );		
		
        $this->r->data->calls_unanswered = $this->Call_model->count_by_complex_with_exclusion(
            array(
                'date >' => QQ_TODAY_START,
                'queue_id' => $id,
                'event_type' => array('ABANDON','EXITEMPTY', 'EXITWITHTIMEOUT')
            ),
			"NOT (`src` IN (SELECT extension FROM users) AND `dst` = '' AND event_type = 'ABANDON' AND agent_id = 0)"
        );		
		
		$this->r->data->calls_without_service = $this->Call_model->count_by_complex_with_exclusion(
			array(
				'date >' => QQ_TODAY_START,
				'queue_id' => $id,
				'called_back' => 'no',
				'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
				'answered_elsewhere' => 'isnull',
				'waittime >=' => 5
			),
			"NOT (`src` IN (SELECT extension FROM users) AND `dst` = '' AND event_type = 'ABANDON' AND agent_id = 0)"
		);
		
        $this->r->status = 'OK';
        $this->r->message = 'Queue basic stats will follow';

        $this->_respond();
    }


    public function get_freepbx_agents($id = false)
    {
        foreach ($this->data->user_agents as $a) {
            $agent_ids[] = $agent_id = $a->id;
        }
        if (!$id) {
            $freepbx_agents = $this->Queue_model->get_all_freepbx_agents();
        } else {
            $freepbx_agents = $this->Queue_model->get_freepbx_agents($id);
        }

        foreach ($freepbx_agents as $fa) {
            if (in_array($fa->id, $agent_ids)) {
                $agents[] = $fa;
            }
        }

        $this->r->status = 'OK';
        $this->r->message = 'FreePBX agents will follow';
        $this->r->data = $agents;

        $this->_respond();
    }

}
