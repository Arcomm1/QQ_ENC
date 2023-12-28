<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Recordings extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('recordings');

        if ($this->data->config->app_contacts == 'yes') 
        {
            foreach ($this->Contact_model->get_all() as $c) 
            {
                $this->data->contacts[$c->number] = $c->name;
            }
        }
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/recordings/index.js');

        $this->data->interesting_events = array(
            'ANSWERED'       => 'answered',
            'UNANSWERED'     => 'unanswered',
            'COMPLETEAGENT'  => 'COMPLETEAGENT',
            'COMPLETECALLER' => 'COMPLETECALLER',
            'OUTGOING'       => 'outgoing',
            'OUT_ANSWERED'   => 'out_answered',
            'OUT_UNANSWERED' => 'out_unanswered',
        );

        
        $agentId = $this->input->get('agent_id'); //Added agent id
    
        $this->data->called_back_styles = qq_get_called_back_styles();
        
        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'Callback') === false) 
            {
                $this->data->queues[$q->id] = $q->display_name;
                $this->data->queue_ids[]    = $q->id;
            }
        }
        
        foreach ($this->data->user_agents as $a) 
        {
            $this->data->agents[$a->id] = $a->display_name;
        }
        
        $where = array();
        $like = array();
        
        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;
        $ring_no_answer_calls = array(); // Initialize an empty array
        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;
        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;
        if($this->input->get('event_type') != 'RINGNOANSWER')
        {

            $where['agent_id']    = $this->input->get('agent_id');
        }
        $where['called_back'] = $this->input->get('called_back');
        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');
        $where['holdtime >']  = $this->input->get('holdtime_gt');
        $where['holdtime <']  = $this->input->get('holdtime_lt');
        $where['waittime >']  = $this->input->get('waittime_gt');
        $where['waittime <']  = $this->input->get('waittime_lt');

        if ($this->input->get('answered_elsewhere')) {
            $where['answered_elsewhere >'] = 1;
        }

        if ($this->input->get('calls_without_service')) 
        {
            $where['called_back'] = 'no';
            $where['answered_elsewhere'] = 'isnull';
            $where['waittime >='] = $this->data->config->app_ignore_abandon;
            $this->data->calls_without_service = 'yes';
        }
        else 
        {
            $this->data->calls_without_service = false;
        }

        if (strpos($this->input->get('uniqueid'), ',') !== false) 
        {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        }
        else 
        {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') 
        {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        }
        elseif ($this->input->get('event_type') == 'RINGNOANSWER') 
        {
            if ($this->data->config->app_track_ringnoanswer == 'yes') 
            {
                $date_range = array(
                    'date_gt' => $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START,
                    'date_lt' => $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END
                );
                
                $ring_no_answer_calls = $this->Event_model->get_ring_no_answer_calls($agentId, $date_range);

                if (empty($ring_no_answer_calls)) 
                {
                    $unique_Ids[] = 0;
                }
                
                foreach ($ring_no_answer_calls as $call) 
                {
                    $unique_Ids[] = $call->uniqueid;
                }
                $uniqueIds = array_unique($unique_Ids);

                if (!empty($uniqueIds)) 
                {
                    
                    $where['uniqueid'] = $uniqueIds;
                } 
                
            }
        } 
        
        elseif ($this->input->get('event_type') == 'UNANSWERED') 
        {
            if ($this->data->config->app_track_ivrabandon == 'yes') 
            {
                $where['event_type'] = array('ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'IVRABANDON');
            } 
            else 
            {
                $where['event_type'] = array('ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY');
            }
        }
        elseif ($this->input->get('event_type') == 'OUTGOING_INTERNAL')
        {
            $where['event_type']     = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
            $where['LENGTH(dst) <='] = 4;
        }
        elseif ($this->input->get('event_type') == 'OUTGOING_EXTERNAL') 
        {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
            $where['LENGTH(dst) >'] = 4;
        }
        elseif ($this->input->get('event_type') == 'INCOMING') 
        {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } 
        elseif ($this->input->get('event_type') == 'INCOMINGOFFWORK') 
        {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } 
        elseif ($this->input->get('event_type') == 'OUTGOING') 
        {
            $where['event_type'] = array('OUT_ANSWERED', 'OUT_BUSY', 'OUT_FAILED', 'OUT_NOANSWER');
        } 
        elseif ($this->input->get('event_type') == 'OUT_ANSWERED') 
        {
            $where['event_type'] = 'OUT_ANSWERED';
        } 
        elseif ($this->input->get('event_type') == 'OUT_UNANSWERED') 
        {
            $where['event_type'] = array('OUT_BUSY', 'OUT_FAILED', 'OUT_NOANSWER');
        } 
        else 
        {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src']            = $this->input->get('src');
        $like['dst']            = $this->input->get('dst');
        $like['transferdst']    = $this->input->get('transferred_to');
        $like['comment']        = $this->input->get('comment');
        $like['subject_family'] = $this->input->get('subject_search_array');
        //echo $like['subject_family'];
        //die();
        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('recordings/');
        if ($this->input->get('random') == 'true') {
            $this->data->num_calls = 20;
        }
        else
        {
            $this->data->num_calls  = $this->Call_model->count($where, $like);
        }
        // die(print_r($like));
        $config['total_rows']   = $this->data->num_calls;
        $config['per_page']     = 20;
        $config['anchor_class'] = 'follow_link';
        $config['suffix']       = '&action=search';

        foreach ($this->input->get() as $f => $v) 
        {
            if ($f == 'per_page') { continue; }
            $config['suffix'] .= '&'.$f."=".$v;
        }


        $config['first_url'] = '?per_page=1'.$config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('per_page');
        $this->data->page = $page;

        if ($this->input->get('random') == 'true') 
        {
            $this->data->calls = $this->Call_model->search($where, $like, 20, 0, true);
        }
        else
        {
            $this->data->calls = $this->Call_model->search($where, $like, $config["per_page"], $page);
        }

        $this->data->pagination_links  = $this->pagination->create_links();

        if ($this->data->config->app_call_tags == 'yes') 
        {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }
        load_views('recordings/index', $this->data, true);
    }

}
