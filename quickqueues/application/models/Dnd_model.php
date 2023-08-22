<?php
defined('BASEPATH') OR exit('No direct script access allowed');




class Dnd_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
        $this->_dnd_subjects  = $this->_table_prefix."dnd_subjects";
        $this->_dnd_records  = $this->_table_prefix."dnd_records";
        $this->_agents  = $this->_table_prefix."agents";

        $this->curr_date_time=date('Y-m-d H:i:s');

    }


    public function get_dnd()
    {
        $dnds = $this->db->get($this->_dnd_subjects);
        return $dnds->result_array();
    }

    public function start_dnd($dnd_data)
    {
         $ins = $this->db->insert($this->_dnd_records, $dnd_data);
         return true;
    }

    public function end_dnd($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->_dnd_records, array('dnd_status'=>'off', 'dnd_ended_at' => $this->curr_date_time));
        return true;
    }

    public function get_agent_dnd_status($agent_id)
    {
        $date_diff='';
// Get last record for agent_id
        $agent_dnd_status = $this->db->order_by('id',"desc")->limit(1)->get_where($this->_dnd_records, array('agent_id' => $agent_id));

            $result = $agent_dnd_status->row_array();

            if(!$result){
                return 'empty';
            }

            $start = new DateTime($result['dnd_started_at']);
            $end = new DateTime($this->curr_date_time);
            $date_diff_string = $start->diff($end);

            $format_string = '%H : %I';
            $date_diff = $date_diff_string->format($format_string);


            $result['dnd_duration'] = $date_diff;
            $dnd_title = $result['title'];

            $dnd_title_result = $this->db->get_where($this->_dnd_subjects, array('id' => $dnd_title))->row_array();
            $result['dnd_subject_title'] = $dnd_title_result['title'];

            return $result;

    }

    public function get_all_agents()
    {
        $all_agents = $this->db->get($this->_agents);
        return $all_agents->result_array();
    }

    public function get_dnd_by_agent_id($agent_id = false, $date_range = array())
    {

        if (!$agent_id || count($date_range) == 0) {
            return false;
        }

        $this->db->select('qq_dnd_records.agent_id, qq_dnd_records.dnd_started_at, qq_dnd_records.dnd_ended_at, qq_dnd_subjects.title ');

        $this->db->where(array('agent_id' => $agent_id));

        $this->db->where('qq_dnd_records.dnd_started_at >', $date_range['date_gt']);
        $this->db->where('qq_dnd_records.dnd_started_at <', $date_range['date_lt']);

        $this->db->from($this->_dnd_records);

        $this->db->join($this->_dnd_subjects, 'qq_dnd_records.title = qq_dnd_subjects.id', 'left');

        $dnd_per_agent = $this->db->get();

        return $dnd_per_agent->result_array();
    }

    /* Get All Agents Breaks For Statistics And Export */
    public function get_all_agents_breaks($date_range = array())
    {

        $this->db->where('qq_dnd_records.dnd_started_at >', $date_range['date_gt']);
        $this->db->where('qq_dnd_records.dnd_started_at <', $date_range['date_lt']);

        $this->db->from($this->_agents);

        $this->db->join($this->_dnd_records, 'qq_agents.id = qq_dnd_records.agent_id', 'left');

        $all_agents_breaks = $this->db->get();

        return $all_agents_breaks->result_array();
    }
}
