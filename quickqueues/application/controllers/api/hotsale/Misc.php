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


    public function generate_queue_call($dst = false)
    {
        if (!$dst) {
            $this->r->status = 'FAIL';
            $this->r->message = "Please provide number";
            $this->_respond();
            exit();
        }

        $content =  "Channel: SIP/trunk-2194488/".$dst."\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-hotsale-generate-queue-call\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "Set: QQ_QUEUE=7000\n";
        $content .= "Set: QQ_DST=".$dst."\n";

        $callfile = time().'-hotsale-'.$dst.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);

        $this->r->status = 'OK';
        $this->r->message = "Call initiated. Calling ".$dst;
        $this->_respond();
    }


    public function generate_ivr_call($dst = false, $file = false)
    {
        if (!$dst) {
            $this->r->status = 'FAIL';
            $this->r->message = "Please provide number";
            $this->_respond();
            exit();
        }

        if (!$file) {
            $this->r->status = 'FAIL';
            $this->r->message = "Please provide file ID";
            $this->_respond();
            exit();
        }

        $content =  "Channel: SIP/trunk-2194488/".$dst."\n";
	    $content .= "MaxRetries: 1\n";
        $content .= "RetryTime: 120\n";
        $content .= "WaitTime: 30\n";
        $content .= "Context: qq-hotsale-generate-ivr-call\n";
        $content .= "Extension: s\n";
        $content .= "Priority: 1\n";
        $content .= "Set: QQ_DST=".$dst."\n";
        $content .= "Set: QQ_FILE=".$file."\n";

        $callfile = time().'-hotsale-'.$dst.'.call';

        file_put_contents('/var/www/html/'.$callfile, $content);
        rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);

        $this->r->status = 'OK';
        $this->r->message = "Call initiated. Calling ".$dst;
        $this->_respond();
    }

}
