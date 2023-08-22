<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Misc extends MY_Controller {


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


    public function get_roles()
    {
        $this->r->status = 'OK';
        $this->r->message = 'available user roles will follow';
        $this->r->data = get_roles();

        $this->_respond();
    }

    public function get_languages()
    {
        $this->r->status = 'OK';
        $this->r->message = 'Available languages will follow';
        $this->r->data = get_languages();

        $this->_respond();
    }

    public function get_language()
    {
        $this->r->status = 'OK';
        $this->r->message = 'Language data will follow';
        $this->r->data = $this->lang->language;

        $this->_respond();
    }


    public function init_chanspy()
    {
        if (!$this->input->post('channel')) {
            $this->User_log_model->add_activity($this->data->logged_in_user->id, 'REQUEST_CHANSPY');
            $this->r->status = 'FAIL';
            $this->r->message = lang('something_wrong');
            $this->_respond();
            exit();
        }
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'START_CHANSPY', $this->input->post('channel'));
        $this->r->status = 'OK';
        $this->r->message = 'Initiated ChanSPy';
        $this->r->data = $this->input->post('channel');
        $this->_respond();
    }


    public function get_extension_states()
    {
        $this->load->library('Asterisk_manager');
        $this->r->status = 'OK';
        $this->r->message = 'Extension realtime status will follow';
        $this->r->data = $this->asterisk_manager->get_extension_state_list();

        $this->_respond();
    }


    public function get_devices()
    {

        $response = json_decode(file_get_contents('http://localhost/pbx-bridge/devices/get'));

        if ($response->status == 'OK') {
            $this->r->status = 'OK';
            $this->r->message = 'Device list will follow';
            $this->r->data = $response->data;
        }

        $this->_respond();
    }


    public function cdr_lookup($number = false, $hours = 8) {
        if (!$number) {
            $this->_respond();
        }

        // I knwo I know don't access database from controllers, yada yada...
        $this->cdrdb = $this->load->database('cdrdb', true);
        $this->cdrdb->where('dst', $number);
        $this->cdrdb->where('calldate >', 'DATE_SUB(CURDATE(), INTERVAL $hours HOURS)');
        $this->cdrdb->limit(1);
        $this->cdrdb->order_by('calldate', 'DESC');
        $result = $this->cdrdb->get('cdr')->row();
        $this->r->status = 'OK';
        $this->r->message = "CDR lookup data will follow";
        $this->r->data = $result;
        $this->_respond();
    }


    public function generate_call($src = false, $dst = false)
    {
        if (!$src || !$dst) {
            $this->_respond();
            exit();
        }

        $agent = $this->Agent_model->get_by('extension', $src);

        if (!$agent) {
            $this->r->message = "Agent with requested number not found";
            $this->_respond();
            exit();
        }

        if (!$agent->trunk) {
            $this->r->message = "Agent has no trunk associated, please contact system administrator";
            $this->_respond();
            exit();
        }

        $queue = $this->Queue_model->get($agent->primary_queue_id);

        if (!$queue) {
            $this->r->message = "Could not find queue for this agent, please contact ";
            $this->_respond();
            exit();
        }

        $content =  "Channel: SIP/".$src."\n";
        //$content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 60\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-generate-call\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "Set: AGENT=".$agent->name."\n";
        $content .= "Set: QUEUE=".$queue->name."\n";
        $content .= "Set: DST=".$dst."\n";
        $content .= "Set: TRUNK=".$agent->trunk."\n";

        $callfile = time().$src.'-'.$dst.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);

        $this->r->status = 'OK';
        $this->r->message = "Call initiated. ".$src." will receive call and connect to ".$dst;
        $this->_respond();
    }


    public function get_random_help_topic()
    {
        $topics = array();
        foreach ($this->lang->language as $k => $v) {
            if (strpos($k, 'h_') === 0) {
                $topics[] = $k;
            }
        }
        $this->r->status = 'OK';
        $this->r->message = 'Help topic will follow';
        $this->r->data = $topics[array_rand($topics)];

        $this->_respond();
    }


    public function get_customer_info($number = false)
    {
        $this->r->status = 'OK';
        $this->r->message = 'Contact data will follow';
        $this->r->data = $this->Contact_model->get_by('number', $number);

        $this->_respond();
    }


}
