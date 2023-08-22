<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Crocobet_contact extends MY_Controller {


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


    public function get_by_number($number = false)
    {
        if (!$number) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $contact = $this->Crocobet_contact_model->get_by_number($number);

        if (!$contact) {
            $this->r->status = 'FAIL';
            $this->r->message = "Contact does not exist";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Contact data will follow';
        $this->r->data = $contact;

        $this->_respond();

    }

}