<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Contact_model.php - Quickqueues contacts abstraction */


class Crocobet_contact_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        $this->_table = 'customer_account';
        $this->_db_group = 'crocobet_contacts';
        $this->_pk = 'customer_id';
        
        $this->_database = $this->load->database($this->_db_group, TRUE);
        
        // die($this->_db_group);
    }


    /**
     * Get specific row by primary key
     *
     * @param int $id Primary key
     * @return obj|bool CodeIgniter database object or false
     */
    public function get($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->_database->select('customer_id, first_name, last_name, login_name, mobile, account_status');
        $this->_database->where('customer_id', $id);
        // die($this->db->get_compiled_select());
        return $this->_database->get($this->_table)->row();
    }


    /**
     * Get specific row by number
     *
     * @param int $id Primary key
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_by_number($number = false)
    {
        if (!$number) {
            return false;
        }
        $this->_database->select('customer_id, first_name, last_name, login_name, mobile, account_status');
        $this->_database->like('mobile', $number);
        return $this->_database->get($this->_table)->row();
    }


}