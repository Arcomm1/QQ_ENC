<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dnd extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->r = new stdClass();
        $this->r->data = new stdClass();

    }

    private function _respond() {
        header('Content-Type: application/json');
        echo json_encode($this->r, JSON_FORCE_OBJECT);
    }

    public function get_dnd()
    {
        $this->r = $this->Dnd_model->get_dnd();
        $this->_respond();

    }

    public function start_dnd($agent_id)
    {
        $_POST = json_decode(file_get_contents("php://input"),true);

        if ($_POST['dnd_subjects_select']) {
            $dnd_title = $_POST['dnd_subjects_select'];
            $dnd_status = 'on';

            $dnd_data = array(
                'agent_id' => $agent_id,
                'title' => $dnd_title,
                'dnd_status' => $dnd_status
            );

            $this->Dnd_model->start_dnd($dnd_data);

            $agent_dnd_status = $this->Dnd_model->get_agent_dnd_status($agent_id);
            $this->r = $agent_dnd_status;

            //$this->r->dnd_status = 'DND success';

        } else{
            $this->r->status = 'FAIL';
        }

        $this->_respond();
    }

    public function end_dnd()
    {
        $_POST = json_decode(file_get_contents("php://input"),true);

        if ($_POST['dnd_record_id']) {
            $id = $_POST['dnd_record_id'];
            $agent_id = $_POST['agent_id'];
            $this->Dnd_model->end_dnd($id);

            $agent_dnd_status = $this->Dnd_model->get_agent_dnd_status($agent_id);
            $this->r = $agent_dnd_status;
        } else{
            $this->r->status = 'FAIL';
        }

        $this->_respond();
    }

    public function get_all_agents(){
        $all_agents = $this->Dnd_model->get_all_agents();
        $this->r = $all_agents;
        $this->_respond();
    }

    public function get_agent_dnd_status($agent_id){

        $agent_dnd_status = $this->Dnd_model->get_agent_dnd_status($agent_id);
        $this->r = $agent_dnd_status;
        $this->_respond();
    }

    public function get_dnd_by_agent_id($agent_id = null)
    {
        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $this->r = $this->Dnd_model->get_dnd_by_agent_id($agent_id, $date_range);
        $this->_respond();

    }
}
