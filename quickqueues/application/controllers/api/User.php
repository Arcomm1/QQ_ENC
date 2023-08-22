<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User extends MY_Controller {


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
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $user = $this->User_model->get($id);

        if (!$user) {
            $this->r->status = 'FAIL';
            $this->r->message = "User does not exist";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User data will follow';
        $this->r->data = $user;

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

        $this->r->data = $this->User_model->delete_completely($id);

        if ($this->r->data == 0) {
            $this->r->status = 'FAIL';
            $this->r->message = "Could not delete user";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User succesfully deleted';
        $this->_respond();

    }


    public function de_activate($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $a = $this->User_model->activate_or_deactivate($id);

        if (!$a) {
            $this->r->status = 'FAIL';
            $this->r->message = "Could not update user";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User succesfully updated';

        set_flash_notif('success', lang('user_delete_success'));

        $this->_respond();

    }


    public function get_by_name($name = false)
    {
        if (!$name) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $user = $this->User_model->get_by('name', $name);

        if (!$user) {
            $this->r->status = 'FAIL';
            $this->r->message = "User does not exist";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User data will follow';
        $this->r->data = $user;

        $this->_respond();

    }


    public function assign_queue($id = false, $queue_id = false)
    {
        if (!$id || !$queue_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->User_model->assign_queue($id, $queue_id);
        $this->r->status = 'OK';
        $this->r->message = 'Queue assigned succesfully';
        $this->_respond();
    }


    public function unassign_queue($id = false, $queue_id = false)
    {
        if (!$id || !$queue_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->User_model->unassign_queue($id, $queue_id);
        $this->r->status = 'OK';
        $this->r->message = 'Queue unassigned succesfully';
        $this->_respond();
    }


    public function assign_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->User_model->assign_agent($id, $agent_id);
        $this->r->status = 'OK';
        $this->r->message = 'Agent assigned succesfully';
        $this->_respond();
    }


    public function unassign_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->User_model->unassign_agent($id, $agent_id);
        $this->r->status = 'OK';
        $this->r->message = 'Agent unassigned succesfully';
        $this->_respond();
    }


    public function get_queues($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User queues will follow';
        $this->r->data = $this->User_model->get_queues($id);

        $this->_respond();
    }


    public function get_agents($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'User agents will follow';
        $this->r->data = $this->User_model->get_agents($id);

        $this->_respond();
    }


    public function check_password()
    {
        if (!$this->input->post()) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $user = $this->User_model->get($this->input->post('user_id'));

        if (!$user) {
            $this->r->status = 'FAIL';
            $this->r->message = "User does not exist";
            $this->_respond();
            exit();
        }

        if ($user->password != md5($this->input->post('password'))) {
            $this->r->status = 'FAIL';
            $this->r->message = "Password is incorrect";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Password is correct';

        $this->_respond();

    }


    public function update($id = false)
    {
        if (!$id || !$this->input->post()) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $user = $this->User_model->get($id);

        if (!$user) {
            $this->r->status = 'FAIL';
            $this->r->message = "User does not exist";
            $this->_respond();
            exit();
        }

        $p = $this->input->post();

        if (array_key_exists('password', $p)) {
            $p['password'] = md5($p['password']);
        }

        $this->User_model->update($id, $p);

        $this->r->status = 'OK';
        $this->r->message = 'User updated succesfully';

        $this->_respond();

    }

}
