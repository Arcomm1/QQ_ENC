<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Misc extends CI_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->r = new stdClass();
        // Just default to error
        $this->r->status = 'FAIL';
        $this->r->message = 'Internal error';
        $this->r->data = new stdClass();
    }


    private function _respond()
    {
        header('Content-Type: application/json');
        echo json_encode($this->r, JSON_FORCE_OBJECT);
    }


    public function add_contact()
    {
        if (!$this->input->post()) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact data';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('id')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact ID';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('number')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact number';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact name';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('order_id')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact order ID';
            $this->_respond();
            exit();
        }

        $contact = $this->Contact_model->get($this->input->post('id'));

        if (!$contact) {
            $this->r->status = 'OK';
            $this->r->message = 'Contact created succesfully, contact data will follow';
            $contact_id = $this->Contact_model->create($this->input->post());
            $this->r->data = $this->Contact_model->get($contact_id);
            $this->_respond();
            exit();
        } else {
            $this->r->status = 'OK';
            $this->r->message = 'Contact updated succesfully, contact data will follow';
            $this->Contact_model->update($this->input->post('id'), $this->input->post());
            $this->r->data = $this->Contact_model->get($this->input->post('id'));
            $this->_respond();
        }

    }


    public function contact_update($od = false)
    {
        if (!$id) {
            $this->r->message = 'Please specify contact id';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('number')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact number';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact name';
            $this->_respond();
            exit();
        }
        if (!$this->input->post('order_id')) {
            $this->r->status = 'FAIL';
            $this->r->message = 'Please provide contact order ID';
            $this->_respond();
            exit();
        }
        $this->r->status = 'OK';
        $this->r->message = 'Contact updated succesfully';
        $this->_respond();
        exit();
    }


    public function generate_call($dst = false)
    {
        if (!$dst) {
            $this->r->status = 'FAIL';
            $this->r->message = "Please provide number";
            $this->_respond();
            exit();
        }

        // $content =  "Channel: SIP/trunk-2194488/".$dst."\n";
        // $content .= "WaitTime: 30\n";
        // $content .= "Context: qq-hotsale-generate-queue-call\n";
        // $content .= "Extension: s\n";
        // $content .= "Priority: 1\n";
        // $content .= "Set: QQ_QUEUE=7000\n";
        // $content .= "Set: QQ_DST=".$dst."\n";

        // $callfile = time().'-hotsale-'.$dst.'.call';

        // file_put_contents('/var/www/html/'.$callfile, $content);
        // rename('/var/www/html/'.$callfile, '/var/spool/asterisk/outgoing/'.$callfile);

        $this->r->status = 'OK';
        $this->r->message = "Call initiated. Calling ".$dst;
        $this->_respond();
    }


    public function get_agent_status()
    {
        $this->_respond();
    }



}
