<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Bond_model extends MY_Model {

    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        $this->_agents_table  = $this->_table_prefix.'agents';
        $this->_queue_table = $this->_table_prefix.'queues';
        $this->_user_table = $this->_table_prefix.'users';
    }

    public function countAgents(){
        $result = $this->db->count_all_results($this->_agents_table);
                return $result;
    }

    public function countQueues(){
        $result = $this->db->count_all_results($this->_queue_table);
                return $result;
    }

    public function countUsers(){
        $result = $this->db->count_all_results($this->_user_table, array('enabled' => 'yes'));
                return $result;
    }

}
