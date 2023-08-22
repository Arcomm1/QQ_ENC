<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Monitoring extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('monitoring');
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/monitoring/index.js');
        load_views('monitoring/index', $this->data, true);
    }


}
