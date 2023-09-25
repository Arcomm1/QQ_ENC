<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Monitoring extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('monitoring');
        $this->load->model('./../models/Settings_model', 'globalSettings');
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/monitoring/index.js');
        $this->data->global_settings = $this->globalSettings->getSettings();
        load_views('monitoring/index', $this->data, true);
    }


}
