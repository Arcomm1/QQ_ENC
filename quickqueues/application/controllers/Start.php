<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Start extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/start/admin_new.js');
        $this->data->js_vars['app_call_categories'] = $this->data->config->app_call_categories;
        $this->data->js_vars['app_round_to_hundredth'] = $this->data->config->app_round_to_hundredth;

        load_views(array('start/admin_new'), $this->data, true);
    }


}
