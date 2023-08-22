<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Contact_model.php - Quickqueues contacts abstraction */


class Contact_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
    }


}