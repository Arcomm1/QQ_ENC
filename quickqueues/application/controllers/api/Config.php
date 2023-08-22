<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Config extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->r = new stdClass();
        // Just default to error
        $this->r->status = 'FAIL';
        $this->r->message = 'Internal error';
        $this->r->data = new stdClass();
    }


    private function _respond() {
        header('Content-Type: application/json');
        echo json_encode($this->r, JSON_FORCE_OBJECT);
    }


    public function get_items_by_category($category = false)
    {
        if (!$category) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $t = $this->Config_model->get_many_by('category', $category);
        $items = array();
        foreach ($t as $i) {
            $items[$i->name] = $i;
        }

        $this->r->status = 'OK';
        $this->r->message = 'Configuration items for category '.$category.' will follow';
        $this->r->data = $items;

        $this->_respond();
    }


    public function set_item($item = false)
    {
        if (!$item) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        if ($this->input->post('value') == "") {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->r->data = $this->Config_model->set_item($item, $this->input->post('value'));
        $this->r->status = 'OK';
        $this->r->message = 'Number of affected configutation items will follow';

        $this->_respond();

    }


    public function get_service_products($service_id = false)
    {
        $this->r->data = $this->Service_product_model->get_many_by('service_id', $service_id);
        $this->r->status = 'OK';
        $this->r->message = 'Service products will follow';

        $this->_respond();
    }


    public function get_service_product_types($service_product_id = false)
    {
        $this->r->data = $this->Service_product_type_model->get_many_by('service_product_id', $service_product_id);
        $this->r->status = 'OK';
        $this->r->message = 'Service product types will follow';

        $this->_respond();
    }


    public function get_service_product_subtypes($service_product_type_id = false)
    {
        $this->r->data = $this->Service_product_subtype_model->get_many_by('service_product_type_id', $service_product_type_id);
        $this->r->status = 'OK';
        $this->r->message = 'Service product subtypes will follow';

        $this->_respond();
    }


    public function create_call_subcategory($category_id = false)
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$category_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Call_subcategory_model->create(array('name' => $this->input->post('name'), 'category_id' => $category_id));
        $this->r->status = 'OK';
        $this->r->message = lang('subcat_create_success');
        $this->_respond();

    }


    public function delete_call_subcategory($call_subcategory_id = false)
    {
        if (!$call_subcategory_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $s = $this->Call_subcategory_model->get($call_subcategory_id);

        $this->Call_subcategory_model->delete($call_subcategory_id);

        $calls = $this->Call_model->update_by('subcategory_id', $call_subcategory_id, array('subcategory_id' => null));

        set_flash_notif('success', lang('cat_delete_success'));
        redirect(site_url('config/call_subcategories/'.$s->category_id));
    }


    public function get_blacklist()
    {
        $this->r->status = 'OK';
        $this->r->message = 'Blacklisted numbers will follow';
        $this->load->library('Asterisk_manager');
        $blacklist = array();
        $result = $this->asterisk_manager->database_get('blacklist');
        foreach ($result as $b) {
            $blacklist[] = array(str_replace('/blacklist/', '', $b[0]), $b[1]);
        }
        $this->r->data = $blacklist;
        $this->_respond();
    }


    public function add_blacklist()
    {
        if (!$this->input->post('number')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->load->library('Asterisk_manager');

        $this->asterisk_manager->database_put('blacklist', $this->input->post('number'), $this->input->post('number'));

        $this->r->status = 'OK';
        $this->r->message = lang('blacklist_add_success');
        $this->_respond();
    }


    public function delete_blacklist($number = false)
    {
        if (!$number) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }
        $this->load->library('Asterisk_manager');

        $this->asterisk_manager->database_del('blacklist', $number);

        set_flash_notif('success', lang('blacklist_del_success'));
        redirect(site_url('config/blacklist'));
    }


    public function get_call_tags()
    {
        $this->r->data = $this->Call_tag_model->get_all();
        $this->r->status = 'OK';
        $this->r->message = 'Call tags will follow';

        $this->_respond();
    }


    public function create_call_tag()
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Call_tag_model->create(array('name' => $this->input->post('name')));
        $this->r->status = 'OK';
        $this->r->message = lang('tag_create_success');
        $this->_respond();

    }


    public function delete_call_tag($call_tag_id = false)
    {
        if (!$call_tag_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Call_tag_model->delete($call_tag_id);

        $calls = $this->Call_model->update_by('tag_id', $call_tag_id, array('tag_id' => null));

        set_flash_notif('success', lang('tag_delete_success'));
        redirect(site_url('config/call_tags'));
    }


    public function get_ticket_departments()
    {
        $this->r->data = $this->Ticket_department_model->get_all();
        $this->r->status = 'OK';
        $this->r->message = 'Ticket departments will follow';

        $this->_respond();
    }


    public function create_ticket_department()
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Ticket_department_model->create(array('name' => $this->input->post('name')));
        $this->r->status = 'OK';
        $this->r->message = lang('cat_create_success');
        $this->_respond();

    }


    public function delete_ticket_department($ticket_department_id = false)
    {
        if (!$ticket_department_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Ticket_department_model->delete($ticket_department_id);

        $categories = $this->Ticket_category_model->get_many_by('department_id', $ticket_department_id);

        $this->Ticket_category_model->delete_by('department_id', $ticket_department_id);

        // $this->Call_model->update_by('category_id', $call_category_id, array('category_id' => null));
        // foreach ($subcategories as $s) {
        //     $this->Call_model->update_by('subcategory_id', $s->id, array('subcategory_id' => null));
        // }

        set_flash_notif('success', lang('cat_delete_success'));
        redirect(site_url('config/ticket_departments'));
    }


    public function get_ticket_categories($department_id = false)
    {
        $this->r->data = $this->Ticket_category_model->get_many_by('department_id', $department_id);
        $this->r->status = 'OK';
        $this->r->message = 'Ticket departments will follow';

        $this->_respond();
    }


    public function create_ticket_category($department_id = false)
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$department_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Ticket_category_model->create(array('name' => $this->input->post('name'), 'department_id' => $department_id));
        $this->r->status = 'OK';
        $this->r->message = lang('cat_create_success');
        $this->_respond();

    }


    public function delete_ticket_category($ticket_category_id = false)
    {
        if (!$ticket_category_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $s = $this->Ticket_category_model->get($ticket_category_id);

        $this->Ticket_category_model->delete($ticket_category_id);

        // $calls = $this->Call_model->update_by('ticket_category_id', $ticket_category_id, array('ticket_category_id' => null));

        set_flash_notif('success', lang('cat_delete_success'));
        redirect(site_url('config/ticket_categories/'.$s->department_id));
    }


    public function get_ticket_subcategories($category_id = false)
    {
        $this->r->data = $this->Ticket_subcategory_model->get_many_by('category_id', $category_id);
        $this->r->status = 'OK';
        $this->r->message = 'Ticket departments will follow';

        $this->_respond();
    }


    public function create_ticket_subcategory($category_id = false)
    {
        if (!$this->input->post('name')) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        if (!$category_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $this->Ticket_subcategory_model->create(array('name' => $this->input->post('name'), 'category_id' => $category_id));
        $this->r->status = 'OK';
        $this->r->message = lang('cat_create_success');
        $this->_respond();

    }


    public function delete_ticket_subcategory($ticket_subcategory_id = false)
    {
        if (!$ticket_subcategory_id) {
            $this->r->status = 'FAIL';
            $this->r->message = "Invalid request";
            $this->_respond();
            exit();
        }

        $s = $this->Ticket_subcategory_model->get($ticket_subcategory_id);

        $this->Ticket_subcategory_model->delete($ticket_subcategory_id);

        // $calls = $this->Call_model->update_by('ticket_category_id', $ticket_category_id, array('ticket_category_id' => null));

        set_flash_notif('success', lang('cat_delete_success'));
        redirect(site_url('config/ticket_subcategories/'.$s->category_id));
    }

}
