<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Broadcast_notification_model.php - Quickqueues broadcast abstraction */


class Broadcast_notification_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        $this->_soft_delete = true;
        parent::__construct();
    }


    /**
     * Create new row
     *
     * Replaces parent::create()
     *
     * @param array $params Row data
     * @return int ID of the new row
     */
    function create($params = false)
    {
        if (!$params || !is_array($params) || count($params) == 0) {
            return 0;
        }
        if (count($this->_required_fields) > 0) {
            foreach ($this->_required_fields as $field) {
                if (!array_key_exists($field, $this->_required_fields)) {
                    return 0;
                }
            }
        }

        $user = $this->User_model->get_by('name', $this->session->userdata('user'));

        $params['creation_date'] = date('Y-m-d H:i:s');
        $params['creator_user_id'] = $user->id;
        $this->db->insert($this->_table, $params);
        return $this->db->insert_id();
    }


    /**
     * Get only deleted broadcast notifications
     *
     * This should be temporary solution until we figure out #230 adn #231
     *
     * @return obj CodeIgniter database object
     */
    public function get_deleted()
    {
        $this->db->where($this->_soft_delete_key, 1);
        return $this->db->get($this->_table)->result();
    }


}
