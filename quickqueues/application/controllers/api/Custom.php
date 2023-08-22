<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Custom extends CI_Controller {


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


    public function get_recording()
    {
        $this->load->library('user_agent');

        if (!$this->input->post() || !$this->input->post('uniqueid') || !$this->input->post('token')) {
            $this->r->message = 'Invalid request';
            $this->_respond();
            exit();
        }

        $user = $this->User_model->get_by('password', $this->input->post('token'));

        if (!$user) {
            $this->r->message = 'Invalid token';
            $this->_respond();
            exit();
        }

        $this->User_log_model->add_activity($user->id, 'FILE_DOWNLOAD');


        $call = $this->Call_model->get_by('uniqueid', $this->input->post('uniqueid'));
        if (!$call) {
            $this->r->message = 'Invalid uniqueid';
            $this->_respond();
            exit();
        }

        if ($call->archived == 'yes') {
            $this->r->message = 'File moved';
            $this->_respond();
            exit();
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
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();
            readfile($path);
            exit();
        } else {
            $this->_respond();
            exit();
        }
    }

    public function update_last_call($extension = false, $uniqueid = false, $src = false)
    {
        // if (!$extension) {
        //     $this->_respond();
        //     exit();
        // }

        $agent = $this->Agent_model->get_by('extension', $extension);
        // if (!$agent) {
        //     $this->_respond();
        //     exit();
        // }
        $this->Agent_model->update_last_call($agent->id, $uniqueid, $src);

        $this->r->status = 'OK';
        $this->r->message = 'Updating agent last call';

        $this->_respond();
    }


}
