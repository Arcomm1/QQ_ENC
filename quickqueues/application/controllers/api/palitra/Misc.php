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


    public function update_last_call($extension = false, $uniqueid = false, $src = false)
    {
        if (!$extension) {
            $this->_respond();
            exit();
        }

        $agent = $this->Agent_model->get_by('extension', $extension);
        if (!$agent) {
            $this->_respond();
            exit();
        }

        $this->Agent_model->update_last_call($agent->id, $uniqueid, $src);

        $this->r->status = 'OK';
        $this->r->message = 'Updating agent last call';

        $this->_respond();
    }


    public function get_contact_name($number = false)
    {
        if (!$number) {
            $this->_respond();
            exit();
        }
        $contact = $this->Contact_model->get_by('number', $number);
        $this->r->status = 'OK';
        $this->r->message = 'Contact information will follow';
        $this->r->data = $contact;
        $this->_respond();
    }


    public function set_contact_name($number = false, $name = false)
    {
        if (!$number || !$name) {
            $this->_respond();
            exit();
        }
        $contact = $this->Contact_model->get_by('number', $number);
        if ($contact) {
            $this->Contact_model->update_by('number', $number, array('name' => urldecode($name)));
        } else {
            $this->Contact_model->create(array('name' => urlencode($name), 'number' => $number));
        }
        $this->r->status = 'OK';
        $this->r->message = 'Contact information updated';
        $this->_respond();
    }


}
