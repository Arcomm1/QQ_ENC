<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Future_model_event.php - for events that are not parsed yet */


class Future_event_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        parent::__construct();
    }

}
