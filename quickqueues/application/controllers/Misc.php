<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Misc extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
    }


    public function license()
    {
        $this->data->page_title = lang('license_info');
        load_views(array('misc/license'), $this->data);
    }



}
