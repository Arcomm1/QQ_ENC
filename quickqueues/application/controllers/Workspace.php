<?php
/* Updated At 19.02.2023 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Workspace extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->data->agent = $this->Agent_model->get($this->data->logged_in_user->associated_agent_id);

        $this->data->js_vars = array(
            'agent_id' => $this->data->agent->id,
            'primary_queue_id' => $this->data->agent->primary_queue_id,
            'user_id' => $this->data->logged_in_user->id,
        );
    }


    public function index()
    {
        $this->data->js_include = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include = base_url('assets/js/components/agent_crm/workspace.js');
        load_views('workspace/admin', $this->data, true);
    }




    public function manager()
    {
        if ($this->data->config->app_call_curators == 'yes') {
            $this->data->users = $this->User_model->get_all();
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $this->data->call_categories = $this->Call_category_model->get_all();
        }

        if ($this->data->config->app_call_tags == 'yes') {
            $this->data->call_tags = $this->Call_tag_model->get_all();
        }

        foreach ($this->Event_model->get_unique_fields('did') as $did) {
            $dids[] = $did->did;
        }
        $this->data->js_vars['dids'] = json_encode($dids);

        $this->data->js_include[] = base_url('assets/js/components/agent_crm/common.js');
        $this->data->js_include[] = base_url('assets/js/components/agent_crm/workspace.js');
        load_views('workspace/manager', $this->data);
    }


}
