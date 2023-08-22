<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password_tmp_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        $this->_soft_delete = true;
        $this->reset_password_table = 'qq_reset_password_tmp';
        $this->user_table='qq_users';
        parent::__construct();


    }

    public function create_reset_password_record($data){

        $this->db->insert($this->reset_password_table, $data);
    }

    function find_token($md_token){
        $this->db->select('id, uid, md5_token, relased, created_at');
        $this->db->from($this->reset_password_table);
        $this->db->where('md5_token', $md_token);
        $users_data=$this->db->get();

        return $users_data->row_array();
    }

    function set_new_password($md_token, $user_id, $new_password){
        $this->db->where('md5_token', $md_token);
        $this->db->update($this->reset_password_table, array('relased' => '1'));

        $new_password=md5($new_password);

        $this->db->where('id', $user_id);
        $this->db->update($this->user_table, array('password'=> $new_password));
        return true;
    }
}
