<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* User_log_model.php - Quickqueues user logging */


class User_log_model extends MY_Model {

    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
    }


    public function add_activity($user_id = false, $action = false, $data = false)
    {
        if (!$user_id || !$action) {
            return false;
        }

        $this->create(array(
            'user_id'       => $user_id,
            'action'        => $action,
            'data'          => $data,
            'url'           => uri_string(),
            'date'          => date('Y-m-d H:i:s'),
            'ip_address'    => $this->input->ip_address(),
        ));

    }


}