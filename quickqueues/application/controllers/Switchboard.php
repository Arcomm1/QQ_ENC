<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Switchboard extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('monitoring');
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/switchboard/index.js');
		load_views(array('switchboard/index'), $this->data, true);

    }

}
