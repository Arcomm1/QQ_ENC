<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Config_model.php - Quickqueues configuration abstraction */


class Config_model extends MY_Model {


    public function __construct()
    {
        $this->_reqquired_fields = array('name', 'default');
        $this->_table = 'qq_config';
        parent::__construct();
    }


    /**
     * Get value for specific item
     *
     * This is convenient shorthand for get_by() function, that returns directly value
     * rather than object
     *
     * @param string $name Setting name
     * @return mixed Setting value of false
     */
    public function get_item($name)
    {
        if (!isset($name)) {
            return false;
        }
        $setting = $this->get_by('name', $name);
        if (!$setting) {
            return false;
        }
        return $setting->value;
    }


    /**
     * Set specific configuration value to its default
     *
     * @param string $name Setting name
     * @return bool TRUE on success, FALSE otherwise
     */
    public function set_default($name)
    {
        if (!isset($name)) {
            return false;
        }
        $setting = $this->get_by('name', $name);
        if (!$setting) {
            return false;
        }
        $res = $this->update($setting->id, array('value' => $setting->default));
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Update specific setting by name
     *
     * @param string $name Setting name
     * @param string $value Setting value
     * @return bool true on success false otherwise
     */
    public function set_item($name = false, $value = false)
    {
        if (!$name) {
            return false;
        }

        if ($value == "") {
            return false;
        }

        return $this->update_by('name', $name, array('value' => $value));
    }


    /**
     * Get available setting categories
     *
     * @return array List of categories
     */
    public function get_categories()
    {
        $categories = array();
        $this->db->select('category');
        $this->db->group_by('category');
        foreach ($this->db->get($this->_table)->result() as $cat) {
            $categories[] = $cat->category;
        }
        return $categories;
    }

    public function use_pagination($param_name){
        if (!isset($param_name)) {
            return false;
        }
        $pagination=$this->db->get_where($this->_table, array('name'=>$param_name));
        return $pagination->row_array();
    }


}
