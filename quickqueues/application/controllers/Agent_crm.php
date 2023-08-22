<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Agent_crm extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role') != 'agent') {
            redirect(site_url('start/'.$this->session->userdata('role')));
        }

        $this->data->crm_mode = $this->Config_model->get_item('app_crm_mode');

        $this->data->track_called_back = $this->Config_model->get_item('app_track_called_back_calls');
        $this->data->track_pauses = $this->Config_model->get_item('app_track_agent_pause_time');
        $this->data->track_sessions = $this->Config_model->get_item('app_track_agent_session_time');

        $this->data->agent_id = $this->data->user_agents[0]->id;
        $this->data->agent = $this->Agent_model->get($this->data->agent_id);
        $this->data->agent_config = $this->Agent_model->get_settings($this->data->agent_id);

        $this->data->js_vars = array(
            'agent_id' => $this->data->agent_id,
            'primary_queue_id' => $this->data->agent->primary_queue_id,
            'user_id' => $this->data->logged_in_user->id,
        );
    }


    public function overview($queue_id = false)
    {

        if (!$queue_id) {
            $queue_id = $this->data->agent->primary_queue_id;
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }

        $this->data->js_vars['dids'] = json_encode($dids);
        $this->data->js_vars['queue_id'] = $queue_id;

        $this->data->js_include = base_url('assets/js/components/agent_crm/overview.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/overview');
        $this->load->view('common/footer');
    }


    public function overview_vendoo($queue_id = false)
    {
        if (!$queue_id) {
            $queue_id = $this->data->agent->primary_queue_id;
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }

        $this->data->js_vars['dids'] = json_encode($dids);
        $this->data->js_vars['queue_id'] = $queue_id;

        $this->data->js_include = base_url('assets/js/components/agent_crm/overview_vendoo.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/overview_vendoo');
        $this->load->view('common/footer');
    }


    public function overview_thermorum()
    {
        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }

        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include = base_url('assets/js/components/agent_crm_thermorum/overview.js');
        $this->load->view('agent_crm_thermorum/header', $this->data);
        $this->load->view('agent_crm_thermorum/overview');
        $this->load->view('common/footer');
    }


    public function workspace_vendoo()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        if ($this->data->config->app_service_module == 'yes') {
            $this->data->services = $this->Service_model->get_all();
        }
        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace_vendoo.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/workspace_vendoo');
        $this->load->view('common/footer');
    }


    public function workspace()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        if ($this->data->config->app_service_module == 'yes') {
            $this->data->services = $this->Service_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/workspace');
        $this->load->view('common/footer');
    }


    public function workspace_gorgia()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        if ($this->data->config->app_service_module == 'yes') {
            $this->data->services = $this->Service_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace_gorgia.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/workspace_gorgia');
        $this->load->view('common/footer');
    }


    public function workspace_nova()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace_nova.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/workspace_nova');
        $this->load->view('common/footer');
    }


    public function workspace_crocobet()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        if ($this->data->config->app_service_module == 'yes') {
            $this->data->services = $this->Service_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace_crocobet.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/workspace_crocobet');
        $this->load->view('common/footer');
    }


    public function workspace_thermorum()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm_thermorum/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm_thermorum/workspace.js');
        $this->load->view('agent_crm_thermorum/header', $this->data);
        $this->load->view('agent_crm_thermorum/workspace');
        $this->load->view('common/footer');
    }


    public function crm_link()
    {

        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm_thermorum/crm_link.js');
        $this->load->view('agent_crm_thermorum/header', $this->data);
        $this->load->view('agent_crm_thermorum/crm_link');
        $this->load->view('common/footer');
    }


    public function callback_queue()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm_thermorum/callback.js');

        $this->data->queues = array();
        $this->data->queue_ids = array();

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        $this->data->calls = $this->Call_model->get_many_by_complex(
            array(
                'date >' => QQ_TODAY_START,
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                'called_back' => 'no',
                'queue_id' => $this->data->queue_ids,
            )
        );

        $this->data->called_back_styles = qq_get_called_back_styles();

        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/callback');
        $this->load->view('common/footer');
    }


    public function recordings_gorgia()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/recordings.js');

        $this->data->interesting_events = $this->Event_type_model->get_many_by('has_calls', 'yes');

        $this->data->track_called_back = $this->Config_model->get_item('app_track_called_back_calls');
        $this->data->track_transfers = $this->Config_model->get_item('app_track_transfers');
        $this->data->app_call_categories = $this->Config_model->get_item('app_call_categories');
        $this->data->track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');

        $this->data->called_back_styles = qq_get_called_back_styles();

        if ($this->data->config->app_contacts == 'yes') {
            foreach ($this->Contact_model->get_all() as $c) {
                $this->data->contacts[$c->number] = $c->name;
            }
        }

        $this->data->queues = array();
        $this->data->agents = array();
        $this->data->queue_ids = array();
        $this->data->agent_ids = array();

        $this->data->available_agents = array();

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
        }

        foreach ($this->Queue_model->get_all() as $q) {
            $this->data->select_queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->Agent_model->get_all() as $a) {
            $this->data->agents[$a->id] = $a->display_name;
            $this->data->select_agents[$a->id] = $a->extension." - ".$a->display_name;
        }

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->data->user_agents as $a) {
            $this->data->agents[$a->id] = $a->display_name;
        }

        $where = array();
        $like = array();

        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        if (strpos($where['calltime >'], ':')) {
            $t = explode(':', $where['calltime >']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime >'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        if (strpos($where['calltime <'], ':')) {
            $t = explode(':', $where['calltime <']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime <'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        $this->data->js_vars['app_service_module'] = $this->data->config->app_service_module;

        if ($this->data->config->app_service_module == 'yes') {
            $where['service_id'] = $this->input->get('service_id');
            $where['service_product_id'] = $this->input->get('service_product_id');
            $where['service_product_type_id'] = $this->input->get('service_product_type_id');
            $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');

            $this->data->js_vars['service_module_params'] = json_encode(
                array(
                    'service_id' => $this->input->get('service_id'),
                    'service_product_id' => $this->input->get('service_product_id'),
                    'service_product_type_id' => $this->input->get('service_product_type_id'),
                    'service_product_subtype_id' => $this->input->get('service_product_subtype_id'),
                )
            );

            $this->data->services = $this->Service_model->get_all();
        }

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;

        if ($this->data->agent_config['agent_call_restrictions']->value == 'own') {
            $where['agent_id'] = $this->data->agent_id;
        } else {
            $where['agent_id'] = $this->input->get('agent_id') ? $this->input->get('agent_id') : $this->data->agent_ids;
        }

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');

        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');

        if (strpos($this->input->get('uniqueid'), ',') !== false) {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        } else {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } elseif ($this->input->get('event_type') == 'INCOMING') {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        if ($this->input->get('answered_elsewhere')) {
            $where['answered_elsewhere >'] = 1;
        }

        if ($this->input->get('calls_without_service')) {
            $where['called_back'] = 'no';
            $where['answered_elsewhere'] = 'isnull';
            $where['waittime >='] = $this->data->config->app_ignore_abandon;
            $this->data->calls_without_service = 'yes';
        } else {
            $this->data->calls_without_service = false;
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');
        $like['transferdst'] = $this->input->get('transferred_to');

        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('agent_crm/recordings');
        if ($this->input->get('random') == 'true') {
            $this->data->num_calls = 20;
        } else{
            $this->data->num_calls  = $this->Call_model->count($where, $like);
        }
        $config['total_rows']   = $this->data->num_calls;
        $config['per_page']     = 20;
        $config['anchor_class'] = 'follow_link';
        $config['suffix']       = '&action=search';

        foreach ($this->input->get() as $f => $v) {
            if ($f == 'per_page') { continue; }
            $config['suffix'] .= '&'.$f."=".$v;
        }

        $config['first_url'] = '?per_page=1'.$config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('per_page');
        $this->data->page = $page;

        if ($this->input->get('random') == 'true') {
            $this->data->calls = $this->Call_model->search($where, $like, 20, 0, true);
        } else{
            $this->data->calls = $this->Call_model->search($where, $like, $config["per_page"], $page);
        }

        $this->data->pagination_links  = $this->pagination->create_links();


        if ($this->data->app_call_categories == 'yes') {
            $this->load->model('Call_category_model');
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        $this->data->js_vars['active_nav'] = 'nav_recordings';


        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/recordings_gorgia');
        $this->load->view('common/footer');
    }


    public function recordings()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm_thermorum/recordings.js');

        $this->data->interesting_events = $this->Event_type_model->get_many_by('has_calls', 'yes');

        $this->data->track_called_back = $this->Config_model->get_item('app_track_called_back_calls');
        $this->data->track_transfers = $this->Config_model->get_item('app_track_transfers');
        $this->data->app_call_categories = $this->Config_model->get_item('app_call_categories');
        $this->data->track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');

        $this->data->called_back_styles = qq_get_called_back_styles();

        if ($this->data->config->app_contacts == 'yes') {
            foreach ($this->Contact_model->get_all() as $c) {
                $this->data->contacts[$c->number] = $c->name;
            }
        }

        $this->data->queues = array();
        $this->data->agents = array();
        $this->data->queue_ids = array();
        $this->data->agent_ids = array();

        $this->data->available_agents = array();

        if ($this->data->agent_config['agent_call_restrictions'] == 'own') {
            $this->data->agent_ids[] = $this->data->agent_id;
        } else {
            if ($this->data->agent_config['agent_call_restrictions']->value == 'queue') {
                foreach ($this->data->user_queues as $q) {
                    foreach ($this->Queue_model->get_agents($q->id) as $a) {
                        $this->data->agent_ids[] = $a->id;
                        $this->data->available_agents[] = $a;
                        $this->data->agents[$a->id] = $a->display_name;
                    }
                }
            } else {
                foreach ($this->Agent_model->get_all('extension', 'ASC') as $a) {
                    $this->data->agent_ids[] = $a->id;
                    $this->data->available_agents[] = $a;
                    $this->data->agents[$a->id] = $a->display_name;
                }
            }
        }

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->data->user_agents as $a) {
            $this->data->agents[$a->id] = $a->display_name;
        }


        $where = array();
        $like = array();


        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        if (strpos($where['calltime >'], ':')) {
            $t = explode(':', $where['calltime >']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime >'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        if (strpos($where['calltime <'], ':')) {
            $t = explode(':', $where['calltime <']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime <'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;

        if ($this->data->agent_config['agent_call_restrictions']->value == 'own') {
            $where['agent_id'] = $this->data->agent_id;
        }
        // else {
        //     $where['agent_id'] = $this->input->get('agent_id') ? $this->input->get('agent_id') : $this->data->agent_ids;
        // }


        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');

        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');



        if (strpos($this->input->get('uniqueid'), ',') !== false) {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        } else {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } elseif ($this->input->get('event_type') == 'INCOMING') {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');
        $like['transferdst'] = $this->input->get('transferred_to');

        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('agent_crm/recordings');
        if ($this->input->get('random') == 'true') {
            $this->data->num_calls = 20;
        } else{
            $this->data->num_calls  = $this->Call_model->count($where, $like);
        }
        $config['total_rows']   = $this->data->num_calls;
        $config['per_page']     = 20;
        $config['anchor_class'] = 'follow_link';
        $config['suffix']       = '&action=search';


        // $g = $this->input->get();

        foreach ($this->input->get() as $f => $v) {
            if ($f == 'per_page') { continue; }
            $config['suffix'] .= '&'.$f."=".$v;
        }


        $config['first_url'] = '?per_page=1'.$config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('per_page');
        $this->data->page = $page;

        if ($this->input->get('random') == 'true') {
            $this->data->calls = $this->Call_model->search($where, $like, 20, 0, true);
        } else{
            $this->data->calls = $this->Call_model->search($where, $like, $config["per_page"], $page);
        }

        $this->data->pagination_links  = $this->pagination->create_links();


        if ($this->data->app_call_categories == 'yes') {
            $this->load->model('Call_category_model');
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/recordings');
        $this->load->view('common/footer');
    }

    public function recordings_vendoo()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/recordings.js');

        $this->data->interesting_events = $this->Event_type_model->get_many_by('has_calls', 'yes');

        $this->data->track_called_back = $this->Config_model->get_item('app_track_called_back_calls');
        $this->data->track_transfers = $this->Config_model->get_item('app_track_transfers');
        $this->data->app_call_categories = $this->Config_model->get_item('app_call_categories');
        $this->data->track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');

        $this->data->called_back_styles = qq_get_called_back_styles();

        if ($this->data->config->app_contacts == 'yes') {
            foreach ($this->Contact_model->get_all() as $c) {
                $this->data->contacts[$c->number] = $c->name;
            }
        }

        $this->data->queues = array();
        $this->data->agents = array();
        $this->data->queue_ids = array();
        $this->data->agent_ids = array();

        $this->data->available_agents = array();

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
        }

        foreach ($this->Queue_model->get_all() as $q) {
            $this->data->select_queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->Agent_model->get_all() as $a) {
            $this->data->agents[$a->id] = $a->display_name;
            $this->data->select_agents[$a->id] = $a->extension." - ".$a->display_name;
        }

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->data->user_agents as $a) {
            $this->data->agents[$a->id] = $a->display_name;
        }

        $where = array();
        $like = array();

        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        if (strpos($where['calltime >'], ':')) {
            $t = explode(':', $where['calltime >']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime >'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        if (strpos($where['calltime <'], ':')) {
            $t = explode(':', $where['calltime <']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime <'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        $this->data->js_vars['app_service_module'] = $this->data->config->app_service_module;

        if ($this->data->config->app_service_module == 'yes') {
            $where['service_id'] = $this->input->get('service_id');
            $where['service_product_id'] = $this->input->get('service_product_id');
            $where['service_product_type_id'] = $this->input->get('service_product_type_id');
            $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');

            $this->data->js_vars['service_module_params'] = json_encode(
                array(
                    'service_id' => $this->input->get('service_id'),
                    'service_product_id' => $this->input->get('service_product_id'),
                    'service_product_type_id' => $this->input->get('service_product_type_id'),
                    'service_product_subtype_id' => $this->input->get('service_product_subtype_id'),
                )
            );

            $this->data->services = $this->Service_model->get_all();
        }

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;

        if ($this->data->agent_config['agent_call_restrictions']->value == 'own') {
            $where['agent_id'] = $this->data->agent_id;
        } else {
            $where['agent_id'] = $this->input->get('agent_id') ? $this->input->get('agent_id') : $this->data->agent_ids;
        }

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');

        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');

        if (strpos($this->input->get('uniqueid'), ',') !== false) {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        } else {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } elseif ($this->input->get('event_type') == 'INCOMING') {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');
        $like['transferdst'] = $this->input->get('transferred_to');

        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('agent_crm/recordings');
        if ($this->input->get('random') == 'true') {
            $this->data->num_calls = 20;
        } else{
            $this->data->num_calls  = $this->Call_model->count($where, $like);
        }
        $config['total_rows']   = $this->data->num_calls;
        $config['per_page']     = 20;
        $config['anchor_class'] = 'follow_link';
        $config['suffix']       = '&action=search';

        foreach ($this->input->get() as $f => $v) {
            if ($f == 'per_page') { continue; }
            $config['suffix'] .= '&'.$f."=".$v;
        }

        $config['first_url'] = '?per_page=1'.$config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('per_page');
        $this->data->page = $page;

        if ($this->input->get('random') == 'true') {
            $this->data->calls = $this->Call_model->search($where, $like, 20, 0, true);
        } else{
            $this->data->calls = $this->Call_model->search($where, $like, $config["per_page"], $page);
        }

        $this->data->pagination_links  = $this->pagination->create_links();


        if ($this->data->app_call_categories == 'yes') {
            $this->load->model('Call_category_model');
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        $this->data->js_vars['active_nav'] = 'nav_recordings';


        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/recordings_vendoo');
        $this->load->view('common/footer');
    }


    public function service_stats()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/recordings.js');

        $this->data->interesting_events = $this->Event_type_model->get_many_by('has_calls', 'yes');

        $this->data->track_called_back = $this->Config_model->get_item('app_track_called_back_calls');
        $this->data->track_transfers = $this->Config_model->get_item('app_track_transfers');
        $this->data->app_call_categories = $this->Config_model->get_item('app_call_categories');
        $this->data->track_duplicates = $this->Config_model->get_item('app_track_duplicate_calls');

        $this->data->called_back_styles = qq_get_called_back_styles();

        if ($this->data->config->app_contacts == 'yes') {
            foreach ($this->Contact_model->get_all() as $c) {
                $this->data->contacts[$c->number] = $c->name;
            }
        }

        $this->data->queues = array();
        $this->data->agents = array();
        $this->data->queue_ids = array();
        $this->data->agent_ids = array();

        $this->data->available_agents = array();

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
        }

        foreach ($this->Queue_model->get_all() as $q) {
            $this->data->select_queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->Agent_model->get_all() as $a) {
            $this->data->agents[$a->id] = $a->display_name;
            $this->data->select_agents[$a->id] = $a->extension." - ".$a->display_name;
        }

        foreach ($this->data->user_queues as $q) {
            $this->data->queues[$q->id] = $q->display_name;
            $this->data->queue_ids[] = $q->id;
        }

        foreach ($this->data->user_agents as $a) {
            $this->data->agents[$a->id] = $a->display_name;
        }

        foreach ($this->Service_model->get_all() as $s) {
            $this->data->all_services[$s->id] = $s->name;
        }

        foreach ($this->Service_product_model->get_all() as $p) {
            $this->data->all_products[$p->id] = $p->name;
        }

        foreach ($this->Service_product_type_model->get_all() as $t) {
            $this->data->all_product_types[$t->id] = $t->name;
        }

        foreach ($this->Service_product_subtype_model->get_all() as $s) {
            $this->data->all_product_subtypes[$s->id] = $s->name;
        }

        $where = array();
        $like = array();

        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        if (strpos($where['calltime >'], ':')) {
            $t = explode(':', $where['calltime >']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime >'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        if (strpos($where['calltime <'], ':')) {
            $t = explode(':', $where['calltime <']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime <'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        $this->data->js_vars['app_service_module'] = $this->data->config->app_service_module;

        if ($this->data->config->app_service_module == 'yes') {
            $where['service_id'] = $this->input->get('service_id');
            $where['service_product_id'] = $this->input->get('service_product_id');
            $where['service_product_type_id'] = $this->input->get('service_product_type_id');
            $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');

            $this->data->js_vars['service_module_params'] = json_encode(
                array(
                    'service_id' => $this->input->get('service_id'),
                    'service_product_id' => $this->input->get('service_product_id'),
                    'service_product_type_id' => $this->input->get('service_product_type_id'),
                    'service_product_subtype_id' => $this->input->get('service_product_subtype_id'),
                )
            );

            $this->data->services = $this->Service_model->get_all();
        }

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;

        if ($this->data->agent_config['agent_call_restrictions']->value == 'own') {
            $where['agent_id'] = $this->data->agent_id;
        } else {
            $where['agent_id'] = $this->input->get('agent_id') ? $this->input->get('agent_id') : $this->data->agent_ids;
        }

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');

        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');

        if (strpos($this->input->get('uniqueid'), ',') !== false) {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        } else {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } elseif ($this->input->get('event_type') == 'INCOMING') {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');
        $like['transferdst'] = $this->input->get('transferred_to');

        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('agent_crm/service_stats');
        if ($this->input->get('random') == 'true') {
            $this->data->num_calls = 20;
        } else{
            $this->data->num_calls  = $this->Call_model->count($where, $like);
        }
        $config['total_rows']   = $this->data->num_calls;
        $config['per_page']     = 20;
        $config['anchor_class'] = 'follow_link';
        $config['suffix']       = '&action=search';

        foreach ($this->input->get() as $f => $v) {
            if ($f == 'per_page') { continue; }
            $config['suffix'] .= '&'.$f."=".$v;
        }

        $config['first_url'] = '?per_page=1'.$config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('per_page');
        $this->data->page = $page;

        if ($this->input->get('random') == 'true') {
            $this->data->calls = $this->Call_model->search($where, $like, 20, 0, true);
        } else{
            $this->data->calls = $this->Call_model->search($where, $like, $config["per_page"], $page);
        }

        $this->data->pagination_links  = $this->pagination->create_links();


        if ($this->data->app_call_categories == 'yes') {
            $this->load->model('Call_category_model');
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        $this->data->js_vars['active_nav'] = 'nav_service';

        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/service_stats');
        $this->load->view('common/footer');
    }


    public function stats()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/stats.js');
        $this->data->track_outgoing = $this->Config_model->get_item('app_track_outgoing');
        $this->data->track_pauses = $this->Config_model->get_item('app_track_agent_pause_time');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/stats');
        $this->load->view('common/footer');
    }

    public function switchboard()
    {
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/switchboard.js');
        $this->load->view('agent_crm/header', $this->data);
        $this->load->view('agent_crm/switchboard');
        $this->load->view('common/footer');

    }

    public function todo()
    {
        if ($this->data->crm_mode == 'no') {
            redirect(site_url('start'));
        }
        echo "ზუსტად იგივე იქნება როგორიც გადასარეკების გვერდი";
    }


    public function test()
    {
        echo "<pre>";
        die(print_r($this->data));
    }

}
