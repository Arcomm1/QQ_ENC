
<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Queues extends MY_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->data->page_title = lang('queues');
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/queues/index.js');
        load_views(array('queues/index'), $this->data, true);
    }


    public function realtime($id = false)
    {
        if (!$id) {
            redirect(site_url('queues'));
        }

        $this->data->queue = $this->Queue_model->get($id);

        if (!$this->data->queue) {
            redirect(site_url('queues'));
        }

        $this->data->js_vars['queue_id'] = $id;
        $this->data->js_include = base_url('assets/js/components/queues/realtime.js');
        load_views('queues/realtime', $this->data, true);
    }


    public function stats($id = false)
    {
        if (!$id) {
            redirect(site_url('start'));
        }

        if (!$this->Queue_model->exists($id)) {
            set_flash_notif('danger', lang('queue_not_found'));
            redirect(site_url('queues'));
        }

        $this->data->queue = $this->Queue_model->get($id);

        $this->data->js_include = base_url('assets/js/components/queues/stats.js');
        $this->data->js_vars = array('queue_id' => $id);
        $this->data->js_vars['app_round_to_hundredth'] = $this->data->config->app_round_to_hundredth;
        $this->data->queue_id = $id;

        load_views(array('queues/stats'), $this->data, true);

    }


    public function settings($id = false)
    {
        if (!$id) {
            redirect(site_url('start'));
        }

        if (!$this->Queue_model->exists($id)) {
            set_flash_notif('danger', lang('queue_not_found'));
            redirect(site_url('queues'));
        }

        $this->data->js_include = base_url('assets/js/components/queues/settings.js');
        $this->data->js_vars = array('queue_id' => $id);
        $this->data->queue_id = $id;

        load_views(array('queues/settings'), $this->data);
    }



}
