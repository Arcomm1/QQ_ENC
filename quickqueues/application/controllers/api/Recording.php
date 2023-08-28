<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Recording extends MY_Controller {


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


    public function update($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$this->input->post()) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->Call_model->update($id, $this->input->post());

        if ($this->data->config->app_notifications == 'yes') {
            if ($this->input->post('comment')) {
                $call = $this->Call_model->get($id);
                $users = $this->User_model->get_many_by('associated_agent_id', $call->agent_id);
                foreach ($users as $u) {
                    if ($this->data->logged_in_user->id == $u->id) { continue; }
                    if ($u->role == 'agent') {
                        $url = site_url('agent_crm/recordings?uniqueid='.$call->uniqueid);
                    } else {
                        $url = site_url('recordings?uniqueid='.$call->uniqueid);
                    }
                    $this->Notification_model->create(
                        array(
                            'author_id' => $this->data->logged_in_user->id,
                            'user_id' => $u->id,
                            'content' => 'your_call_has_new_comment',
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        )
                    );
                }
            }
        }

        $this->r->status = 'OK';
        $this->r->message = 'Call information updated succesfully';
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'UPDATE_CALL_DATA');

        $this->_respond();
    }


    public function get_events($uniqueid = false)
    {
        if (!$uniqueid) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $events = $this->Event_model->get_many_by('uniqueid', $uniqueid);
        $this->r->status = 'OK';
        $this->r->message = 'Call related events will follow';
        $this->r->data = $events;
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'GET_CALL_EVENTS');

        $this->_respond();
    }


    public function called_back($id = false, $called_back = false)
    {
        if (!$id || !$called_back) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if ($this->data->config->app_auto_mark_called_back == 0) {

            $this->Call_model->update($id, array('called_back' => $called_back));
            $this->r->status = 'OK';
            $this->r->message = 'Called back status for call updated';
            $this->User_log_model->add_activity($this->data->logged_in_user->id, 'MARK_CALLED_BACK');

            $this->_respond();

        } else {
            $call = $this->Call_model->get($id);
            $mark_from = date('Y-m-d H:i:s', (time() - $this->data->config->app_auto_mark_called_back * 60));

            $calls_to_mark = $this->Call_model->get_many_by_complex(
                array(
                    'src' => $call->src,
                    'date >' => $mark_from,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );

            foreach ($calls_to_mark as $ctm) {
                $this->Call_model->update($ctm->id, array('called_back' => $called_back));
            }

            /**
             * Since auto mark called back value might be less (for example this call was made 3 hours ago and auto mark is set to two)
             * we need to specifically update this call to.
             * Fixes #348
             */
            $this->Call_model->update($id, array('called_back' => $called_back));

            $this->r->status = 'OK';
            $this->r->message = 'Called back status for call updated. Calls affected '.count($calls_to_mark);

            $this->User_log_model->add_activity($this->data->logged_in_user->id, 'MARK_CALLED_BACK');

            $this->_respond();

        }

    }


    public function get($id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Call data will follow';
        $this->r->data = $this->Call_model->get($id);

        $this->_respond();
    }

    public function comment($id = false, $comment = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Call_model->update($id, array('comment' => urldecode($comment)));

        $this->r->status = 'OK';
        $this->r->message = 'Call updated succesfully';

        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'ADD_COMMENT');

        $this->_respond();
    }


    public function comment_on_future($uniqueid = false, $comment = false)
    {
        if (!$uniqueid || !$comment) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->load->library('asterisk_manager');
        $d = urldecode($uniqueid)."|".urldecode($comment);
        if ($this->asterisk_manager->queue_log(false, false, 'ADDCOMMENT', false, $d)) {
            $this->r->status = 'OK';
            $this->r->message = $this->asterisk_manager->status;
        } else {
            $this->r->status = 'FAIL';
            $this->r->message = $this->asterisk_manager->status;
        }
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'ADD_COMMENT');

        $this->_respond();
    }

    public function category_on_future($uniqueid = false, $category_id = false)
    {
        if (!$uniqueid || !$category_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->load->library('asterisk_manager');
        $d = urldecode($uniqueid)."|".urldecode($category_id);
        if ($this->asterisk_manager->queue_log(false, false, 'ADDCATEGORY', false, $d)) {
            $this->r->status = 'OK';
            $this->r->message = $this->asterisk_manager->status;
        } else {
            $this->r->status = 'FAIL';
            $this->r->message = $this->asterisk_manager->status;
        }
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'ADD_CATEGORY');

        $this->_respond();
    }


    public function category($id = false, $category_id = false)
    {
        if (!$id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Call_model->update($id, array('category_id' => $category_id));
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'ADD_CATEGORY');

        $this->r->status = 'OK';
        $this->r->message = 'Call updated succesfully';


        $this->_respond();
    }


    public function get_file($id = false)
    {
        $this->load->library('user_agent');
        if (!$id) {
            set_flash_notif('danger', lang('something_wrong'));
            redirect($this->agent->referrer());
        }
        $this->User_log_model->add_activity($this->data->logged_in_user->id, 'FILE_DOWNLOAD');

        $call = $this->Call_model->get($id);
        if (!$call) {
            set_flash_notif('danger', lang('something_wrong'));
            redirect($this->agent->referrer());
        }

        $path = qq_get_call_recording_path($call);

        $path = (defined('DB_URL') == true ? DB_URL : '') . $path;
        //echo $path; // path is incorrect for localhost!
        
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
            set_flash_notif('danger', lang('something_wrong'));
            redirect($this->agent->referrer());
        }

    }


    public function get_by_number($number = false, $limit = 20)
    {
        if (!$number) {
            set_flash_notif('danger', lang('something_wrong'));
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->r->status = 'OK';
        $this->r->message = 'Calls list will follow';
        $this->r->data = $this->Call_model->get_many_by_number($number, false, false, $limit);

        $this->_respond();
    }


    public function create_future_event()
    {
        if (!$this->input->post() || !$this->input->post('uniqueid')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $params = $this->input->post();

        $params['uniqueid'] = preg_replace("/[^.0-9]/", '', $params['uniqueid']);
        if ($params['comment'] == 'undefined') { unset($params['comment']); }

        if ($this->Future_event_model->exists_by('uniqueid', $params['uniqueid'])) {
            $this->Future_event_model->update_by('uniqueid', $params['uniqueid'], $params);
        } else {
            $this->Future_event_model->create($params);
        }

        if ($this->Call_model->exists_by_complex(
                array(
                    'uniqueid' => $params['uniqueid'],
                    'event_type' => array(
                        'COMPLETECALLER',
                        'COMPLETEAGENT',
                        'OUT_ANSWERED'
                    )
                ))
        ) {
            $this->Call_model->update_by('uniqueid', $params['uniqueid'], $params);
        }

        set_flash_notif('danger', lang('call_update_success'));
        $this->r->status = 'OK';
        $this->r->message = "Future event created succesfully";
        $this->_respond();
    }


    public function add_to_ticket($uniqueid = false, $ticket_id = false)
    {
        if (!$uniqueid || !$ticket_id) {
            $this->_respond();
            exit();
        }

        /**
         * Check if call already exists and update it
         * If not, check if future event exists, and update it
         * If not, create new future event
         */
        $call = $this->Call_model->get_by('uniqueid', $uniqueid);

        if ($call) {
            $this->Call_model->update($call->id, array('ticket_id' => $ticket_id));
        } else {
            $future_event = $this->Future_event_model->get_by('uniqueid', $call_uniqueid);
            if ($future_event) {
                $this->Future_event_model->update($future_event->id, array('ticket_id' => $ticket_id));
            } else {
                $this->Future_event_model->create(
                    array(
                        'ticket_id' => $ticket_id,
                        'uniqueid' => $uniqueid
                    )
                );
            }
        }
        $this->r->status = 'OK';
        $this->r->message = lang('call_addeed_to_ticket_succcess');
        $this->_respond();
    }


    public function remove_from_ticket($id = false)
    {
        if (!$id) {
            $this->_respond();
            exit();
        }

        $this->Call_model->update($id, array('ticket_id' => '0'));
        $this->r->status = 'OK';
        $this->r->message = lang('call_removed_from_ticket_succcess');
        $this->_respond();
    }

}
