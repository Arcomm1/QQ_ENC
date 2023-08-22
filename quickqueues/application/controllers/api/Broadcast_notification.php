<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Broadcast_notification extends MY_Controller {


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


    public function get_all()
    {
        $this->r->data = $this->Broadcast_notification_model->get_all('id', 'DESC');
        $this->r->status = 'OK';
        $this->r->message = 'All broadcasts will follow';

        $this->_respond();
    }


    public function get_deleted()
    {
        $this->r->data = $this->Broadcast_notification_model->get_deleted();
        $this->r->status = 'OK';
        $this->r->message = 'All broadcasts will follow';

        $this->_respond();
    }


    public function create()
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$this->input->post('description')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Broadcast_notification_model->create(
            array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'color' => $this->input->post('color'),

            )
        );
        $this->r->status = 'OK';
        $this->r->message = lang('bcast_create_success');
        $this->_respond();

    }


    public function update($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$this->input->post('description')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Broadcast_notification_model->update($id,
            array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),

            )
        );
        $this->r->status = 'OK';
        $this->r->message = lang('bcast_edit_success');
        $this->_respond();

    }


    public function get($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->data = $this->Broadcast_notification_model->get($id);
        $this->r->status = 'OK';
        $this->r->message = 'Broadcast message will follow';

        $this->_respond();
    }


    public function delete($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Broadcast_notification_model->delete($id);

        set_flash_notif('success', lang('bcast_delete_success'));
        redirect(site_url('broadcast_notifications'));
    }


    public function restore($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Broadcast_notification_model->restore($id);

        set_flash_notif('success', lang('bcast_restore_success'));
        redirect(site_url('broadcast_notifications'));
    }


}
