<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Event_type_model.php - Quickqueues event type abstraction */


class Event_type_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
    }

}
