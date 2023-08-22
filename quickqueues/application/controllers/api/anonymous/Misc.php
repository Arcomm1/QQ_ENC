<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Misc extends CI_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->data = new stdClass();

        $this->data->config = new stdClass();
        foreach ($this->Config_model->get_all() as $item) {
            $this->data->config->{$item->name} = $item->value;
        }

        if ($this->session->language) {
            $this->lang->load(array('main', 'help'), $this->session->language);
        } else {
            if ($this->data->config->app_language) {
                $this->lang->load(array('main', 'help'), $this->data->config->app_language);
            } else {
                $this->lang->load(array('main', 'help'), 'english');
            }
        }

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
            $agent->trunk = 'trunk-2560440';
        }

        $queue = $this->Queue_model->get($agent->primary_queue_id);

        if (!$queue) {
            $this->r->message = "Could not find queue for this agent, please contact ";
            $this->_respond();
            exit();
        }

        $custom_uniqueid = time().".".rand(10000,99999);

        $content =  "Channel: SIP/".$agent->trunk."/".$dst."\n";
        //$content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 60\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-gamma-generate-call\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "CallerID: ".$dst."\n";
        $content .= "Set: QQ_CUSTOMUNIQUEID: ".$custom_uniqueid."\n";
        $content .= "Set: QQ_AGENT=".$agent->name."\n";
        $content .= "Set: QQ_QUEUE=".$queue->name."\n";
        $content .= "Set: QQ_DST=".$dst."\n";
        $content .= "Set: QQ_TRUNK=".$agent->trunk."\n";

        $callfile = time().$src.'-'.$dst.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);

        $this->r->status = 'OK';
        $this->r->message = "Call initiated. ".$src." will receive call and connect to ".$dst;
        $this->r->data = array('UniqueID' => $custom_uniqueid);
        $this->_respond();
    }


}
