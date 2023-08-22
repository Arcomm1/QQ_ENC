<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Ticket extends MY_Controller {


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


    public function get($id = false)
    {
        if (!$id) {
            $this->_respond();
            exit();
        }
        $this->r->status = 'OK';
        $this->r->message = 'Ticket information will follow';
        $this->r->data = $this->Ticket_model->get($id);
        $this->_respond();
    }


    public function get_categories($department_id = false)
    {
        $this->r->status = 'OK';
        $this->r->message = "Categories list will follow";
        if (!$department_id) {
            $this->r->data = $this->Ticket_category_model->get_all();
        } else {
            $this->r->data = $this->Ticket_category_model->get_many_by('department_id', $department_id);

        }
        $this->_respond();
    }

    public function get_subcategories($category_id = false)
    {
        $this->r->status = 'OK';
        $this->r->message = "Categories list will follow";
        if (!$category_id) {
            $this->r->data = $this->Ticket_subcategory_model->get_all();
        } else {
            $this->r->data = $this->Ticket_subcategory_model->get_many_by('category_id', $category_id);

        }
        $this->_respond();
    }


    public function get_comments($id = false)
    {
        if (!$id) {
            $this->_respond();
            exit();
        }
        $this->r->status = 'OK';
        $this->r->message = 'Ticket comments will follow';
        $this->r->data = $this->Ticket_comment_model->get_many_by('ticket_id', $id);
        $this->_respond();
    }


    public function add_comment($id = false)
    {
        if (!$id) {
            $this->_respond();
            exit();
        }
        if (!$this->input->post()) {
            $this->_respond();
            exit();
        }
        $data = $this->input->post();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['ticket_id'] = $id;
        $this->Ticket_comment_model->create($data);
        $this->r->status = 'OK';
        $this->r->message = 'Ticket comment added  succesfully';
        $this->_respond();
    }


    public function get_by_number($number = false, $status = false, $limit = 20)
    {
        $this->r->status = 'OK';
        $this->r->message = "Ticket list will follow";
        $this->r->data = $this->Ticket_model->get_many_by_complex(
            array(
                'number' => $number,
                'status' => $status,
            )
        );
        $this->_respond();
    }


}
