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


    public function generate_call($src = false, $dst = false)
    {
        if (!$src || !$dst) {
            $this->_respond();
            exit();
        }

        $custom_uniqueid = time().".".rand(10000,99999);

        if (strlen($dst) < 12) {
            $this->Event_model->create(
                array(
                    'uniqueid'  => $custom_uniqueid,
                    'custom_uniqueid' => $custom_uniqueid,
                    'timestamp' => time(),
                    'date' => date('Y-m-d H:i:s'),
                    'event_type' => 'DIALOUTATTEMPT',
                )
            );

            $this->Event_model->create(
                array(
                    'uniqueid'  => $custom_uniqueid,
                    'custom_uniqueid' => $custom_uniqueid,
                    'timestamp' => time(),
                    'date' => date('Y-m-d H:i:s'),
                    'event_type' => 'DIALOUTFAILED',
                    'dialout_fail_reason' => 8
                )
            );

            $this->r->status = 'OK';
            $this->r->message = "Call initiated. ".$src." will receive call and connect to ".$dst;
            $this->r->data = array('UniqueID' => $custom_uniqueid);
            $this->_respond();
            exit();
        }

        if (substr($dst, 0, 4) != '9953' &&
            substr($dst, 0, 4) != '9954' &&
            substr($dst, 0, 4) != '9955' &&
            substr($dst, 0, 4) != '9957') {
            $this->Event_model->create(
                array(
                    'uniqueid'  => $custom_uniqueid,
                    'custom_uniqueid' => $custom_uniqueid,
                    'timestamp' => time(),
                    'date' => date('Y-m-d H:i:s'),
                    'event_type' => 'DIALOUTATTEMPT',
                )
            );

            $this->Event_model->create(
                array(
                    'uniqueid'  => $custom_uniqueid,
                    'custom_uniqueid' => $custom_uniqueid,
                    'timestamp' => time(),
                    'date' => date('Y-m-d H:i:s'),
                    'event_type' => 'DIALOUTFAILED',
                    'dialout_fail_reason' => 8
                )
            );

            $this->r->status = 'OK';
            $this->r->message = "Call initiated. ".$src." will receive call and connect to ".$dst;
            $this->r->data = array('UniqueID' => $custom_uniqueid);
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

        $content =  "Channel: SIP/".$agent->trunk."/".$dst."\n";
        //$content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 60\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-gamma-generate-call\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "CallerID: ".$dst."\n";
        $content .= "Set: QQ_CUSTOMUNIQUEID=".$custom_uniqueid."\n";
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


    public function get_call($custom_uniqueid = false)
    {
        if (!$custom_uniqueid) {
            $this->r->message = 'Please provide call ID';
            $this->_respond();
            exit;
        }

        $src_event = $this->Event_model->get_one_by_complex(
            array(
                'custom_uniqueid' => $custom_uniqueid,
                'event_type' => 'DIALOUTFAILED'
            )
        );

        if ($src_event) {
            if ($src_event->dialout_fail_reason == 1) {
                $this->r->status = 'FAILED';
                $this->r->message = 'Number does not exist or is unreachable';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 3) {
                $this->r->status = 'NO_ANSWER';
                $this->r->message = 'Number did not answer the call';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 5) {
                $this->r->status = 'BUSY';
                $this->r->message = 'Number is busy or declined the call';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 8) {
                $this->r->status = 'FAILED';
                $this->r->message = 'Number does not exist or is unreachable';
                $this->_respond();
                exit;
            }
        }

        $src_event = $this->Event_model->get_one_by_complex(
            array(
                'custom_uniqueid' => $custom_uniqueid,
                'event_type' => 'DIALOUTATTEMPT'
            )
        );

        if (!$src_event) {
            $this->r->message = 'Event not found. Please check if provided ID is correct';
            $this->_respond();
            exit;
        }

        $call = $this->Call_model->get_by('uniqueid', $src_event->uniqueid);

        if (!$call) {
            $this->r->message = 'Call not found. Please check if provided ID is correct';
            $this->_respond();
            exit;
        }

        $this->r->status = 'OK';
        $this->r->message = 'Call data will follow';
        $this->r->data = $call;

        $this->_respond();

    }


    public function get_file($custom_uniqueid = false)
    {
        $this->load->library('user_agent');
        if (!$custom_uniqueid) {
            $this->r->message = 'Please provide call ID';
            $this->_respond();
            exit;
        }

        $src_event = $this->Event_model->get_one_by_complex(
            array(
                'custom_uniqueid' => $custom_uniqueid,
                'event_type' => 'DIALOUTFAILED'
            )
        );

        if ($src_event) {
            if ($src_event->dialout_fail_reason == 1) {
                $this->r->status = 'FAILED';
                $this->r->message = 'Number does not exist or is unreachable';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 3) {
                $this->r->status = 'NO_ANSWER';
                $this->r->message = 'Number did not answer the call';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 5) {
                $this->r->status = 'BUSY';
                $this->r->message = 'Number is busy or declined the call';
                $this->_respond();
                exit;
            }
            if ($src_event->dialout_fail_reason == 8) {
                $this->r->status = 'FAILED';
                $this->r->message = 'Number does not exist or is unreachable';
                $this->_respond();
                exit;
            }
        }

        $src_event = $this->Event_model->get_one_by_complex(
            array(
                'custom_uniqueid' => $custom_uniqueid,
                'event_type' => 'DIALOUTATTEMPT'
            )
        );

        if (!$src_event) {
            $this->r->message = 'Event not found. Please check if provided ID is correct';
            $this->_respond();
            exit;
        }

        $call = $this->Call_model->get_by('uniqueid', $src_event->uniqueid);

        if (!$call) {
            $this->r->message = 'Call not found. Please check if provided ID is correct';
            $this->_respond();
            exit;
        }

        if ($call->event_type == 'ABANDON') {
            $this->r->message = 'Call was not answered by agent';
            $this->_respond();
            exit;
        }

        $path = qq_get_call_recording_path($call);
        // die($path);
        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Accept-Ranges: bytes');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();
            readfile($path);
            exit();
        } else {
            $this->r->message = 'Something went wrong';
            $this->_respond();
            exit;
        }

    }

}
