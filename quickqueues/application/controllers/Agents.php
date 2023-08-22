<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Agents extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('agents');
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/agents/index.js');
        $dids = array();

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        if ($this->data->logged_in_user->extension) {
            $this->data->js_vars['user_extension'] = $this->data->logged_in_user->extension;
        }

        foreach ($this->data->user_queues as $q) {
            $queue_names[$q->name] = $q->display_name;
        }

        $this->data->js_vars['queue_names'] = json_encode($queue_names);

        load_views(array('agents/index'), $this->data, true);
    }


    public function create()
    {
        $this->data->js_include = base_url('assets/js/components/agents/create.js');

        $this->data->queues = $this->Queue_model->get_all();

        if ($this->input->post()) {
            $data['name'] = $this->input->post('name');
            $data['display_name'] = $this->input->post('name');
            $data['extension'] = $this->input->post('extension');
            $data['primary_queue_id'] = $this->input->post('queue');
            $id = $this->Agent_model->create($data);
            if ($id) {
                set_flash_notif('success', lang('agent_create_success'));
                redirect(site_url('agents'));
            }
        }
        load_views('agents/create', $this->data);
    }

    public function stats($id = false)
    {
        if (!$id) {
            redirect(site_url('start'));
        }

        if (!$this->Agent_model->exists($id)) {
            set_flash_notif('danger', lang('agent_not_found'));
            redirect(site_url('agents'));
        }

        $this->data->agent = $this->Agent_model->get($id);

        $this->data->js_include = base_url('assets/js/components/agents/stats.js');
        $this->data->js_vars = array('agent_id' => $id);
        $this->data->js_vars['app_round_to_hundredth'] = $this->data->config->app_round_to_hundredth;
        load_views(array('agents/stats'), $this->data, true);
    }


    public function settings($id = false)
    {
        if (!$id) {
            redirect(site_url('start'));
        }

        if (!$this->Agent_model->exists($id)) {
            set_flash_notif('danger', lang('agent_not_found'));
            redirect(site_url('agents'));
        }

        $this->data->has_user = $this->Agent_model->has_user($id);

        $this->data->js_include = base_url('assets/js/components/agents/settings.js');
        $this->data->js_vars['agent_id'] = $id;
        $this->data->js_vars['track_pauses'] = $this->Config_model->get_item('app_track_agent_pause_time');

        $this->data->js_vars['track_sessions'] = $this->Config_model->get_item('app_track_agent_session_time');

        $this->data->agent_id = $id;

        load_views(array('agents/settings'), $this->data);
    }


    public function detailed_stats()
    {
        $this->data->js_include = base_url('assets/js/components/agents/compare.js');
        $dids = array();

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        if ($this->data->logged_in_user->extension) {
            $this->data->js_vars['user_extension'] = $this->data->logged_in_user->extension;
        }

        load_views(array('agents/detailed_stats'), $this->data);
    }


    public function timetables()
    {
        $this->data->js_include = base_url('assets/js/components/agents/timetables.js');

        foreach ($this->data->user_agents as $a) {
            $this->data->agents[$a->id] = $a->display_name;
        }

        $where = array();

        if ($this->input->get('agent_id')) {
            $where['agent_id'] = $this->input->get('agent_id');
        }

        $where['event_type'] = array('STARTSESSION', 'STOPSESSION', 'STARTPAUSE', 'STOPPAUSE');
        if ($this->input->get('event_type')) {
            if ($this->input->get('event_type') == 'SESSION') {
                $where['event_type'] = array('STARTSESSION', 'STOPSESSION');
            }
            if ($this->input->get('event_type') == 'PAUSE') {
                $where['event_type'] = array('STARTPAUSE', 'STOPPAUSE');
            }
        }

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $this->config->load('pagination');
        $config                 = $this->config->item('pagination');
        $config['base_url']     = site_url('agents/timetables/');
        $this->data->num_events  = $this->Call_model->count_by_complex($where);
        $config['total_rows']   = $this->data->num_events;
        $config['per_page']     = 50;
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

        $this->data->events = $this->Event_model->get_many_by_complex($where, $config['per_page'], $page);

        load_views(array('agents/timetables'), $this->data);
    }

    public function dndPerAgent($id = false)
    {
        if (!$id) {
            redirect(site_url('start'));
        }

        $this->data->js_include = base_url('assets/js/components/agents/dndperagent.js');
        $this->data->js_vars = array('agent_id' => $id);
        $this->data->js_vars['app_round_to_hundredth'] = $this->data->config->app_round_to_hundredth;

        load_views(array('agents/dndperagent'), $this->data, true);
    }

    public function breaks(){
        $this->data->js_include = base_url('assets/js/components/agents/breaks.js');
        $this->data->js_vars['app_round_to_hundredth'] = $this->data->config->app_round_to_hundredth;

        load_views(array('agents/breaks'), $this->data, true);
    }

}
