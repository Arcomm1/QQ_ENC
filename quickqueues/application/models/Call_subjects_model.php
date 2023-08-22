<?php
//(zura) Created At 14.12.2021
//		 Updated At 10.07.2022
// task categories model
// used to define task categories

class Call_subjects_model extends CI_Model {

	var $parent_tbl = 'qq_call_subjects_parent';
	var $child_1_tbl = 'qq_call_subjects_child_1';
	var $child_2_tbl = 'qq_call_subjects_child_2';
	var $child_3_tbl = 'qq_call_subjects_child_3';
	var $call_table = 'qq_calls';

	function __construct() {
		parent::__construct();
		$this->load->database();
	}


    /* Add New Parent Subject (category ID=0) */
	function save_parent_call_subject($data)
	{
		$this->db->insert($this->parent_tbl,$data);
        return true;
	}

    /* Get Main Subjects Filtered By Visibility */
	function get_visible_subjects() {
		$this->db->order_by('id','ASC');
		$result = $this->db->get_where($this->parent_tbl,array('visible'=>'1'));
		return $result->result_array();
	}

    /* Get All Main Subjects*/
	function get_main_subjects() {
		$this->db->order_by('id','ASC');
		$result = $this->db->get($this->parent_tbl);
		return $result->result_array();
	}

    /* Get All Child 1 Subjects */
    function get_child_1_subjects() {
        $this->db->order_by('id','ASC');
        $result = $this->db->get($this->child_1_tbl);
        return $result->result_array();
    }

    /* Get All Child 2 Subjects */
    function get_child_2_subjects() {
        $this->db->order_by('id','ASC');
        $result = $this->db->get($this->child_2_tbl);
        return $result->result_array();
    }

    /* Get All Child 3 Subjects */
    function get_child_3_subjects() {
        $this->db->order_by('id','ASC');
        $result = $this->db->get($this->child_3_tbl);
        return $result->result_array();
    }

    /* Get Child 1 Subjcets by Parent */
	function get_child_1_subject_all($parent_id) {
		if (!isset($parent_id) or $parent_id == '') {
			return array();
		}
		$this->db->order_by('id','ASC');
		$result = $this->db->get_where($this->child_1_tbl, $parent_id);
		return $result->result_array();
	}
	
	function get_child_1_subject($parent_id) {
		if (!isset($parent_id) or $parent_id == '') {
			return array();
		}
		$this->db->order_by('id','ASC');
		$result = $this->db->get_where($this->child_1_tbl, $parent_id);
		return $result->result_array();
	}


    /* Add New Child 1 Subject */
	function save_child_1_subject($data)
	{

		$this->db->insert($this->child_1_tbl, $data);
		return true;
	}

    /* Get Child 2 Subjcets by Parent */
	function get_child_2_subject($parent_id) {
		if (!isset($parent_id) or $parent_id == '') {
			return array();
		}
		$this->db->order_by('id','ASC');
		$result = $this->db->get_where($this->child_2_tbl, $parent_id);
		return $result->result_array();
	}

    /* Add New Child 2 Subject */
	function save_child_2_subject($data)
	{
		$this->db->insert($this->child_2_tbl, $data);
		return true;
	}

    /* Get Child 3 Subjcets by Parent */
	function get_child_3_subject($parent_id) {
		if (!isset($parent_id) or $parent_id == '') {
			return array();
		}
		$this->db->order_by('id','ASC');
		$result = $this->db->get_where($this->child_3_tbl, $parent_id);
		return $result->result_array();
	}

    /* Add New Child 2 Subject */
	function save_child_3_subject($data)
	{
		$this->db->insert($this->child_3_tbl, $data);
		return true;
	}

    /* ------------------- Edit Main Subject ------------------- */

    /* Get Main Subjcets by ID */
	function get_by_id_main_subject($id) {
		if (!isset($id) or $id == '') {
            return array();
        }
        $result = $this->db->get_where($this->parent_tbl, array('id' => $id));
        return $result->row_array();
	}

    /* Save Main Subject */
	function update_main_subject($id,$params) {
		$this->db->where('id',$id);
		$this->db->update($this->parent_tbl,$params);
		return TRUE;
	}
    /* ---- End Of Update Main Subject--- */

    //  Hide & Show Main Subject
	function hide_show_main_subject($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->parent_tbl, $data);
		return TRUE;
	}


    /* ------------------- Edit Child 1 ------------------- */

    /* Get Child 1 by ID */
	function get_by_id_child_1($id) {
		if (!isset($id) or $id == '') {
            return array();
        }
        $result = $this->db->get_where($this->child_1_tbl, array('id' => $id));
        return $result->row_array();
	}

    /* Save Child 1 Subject */
	function update_child_1($id,$params) {
		$this->db->where('id',$id);
		$this->db->update($this->child_1_tbl,$params);
		return TRUE;
	}
    /* ---- End Of Update Child 1--- */

    //  Hide & Show Child 1
	function hide_show_child_1($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->child_1_tbl, $data);
		return TRUE;
	}

    /* ------ Select Distinct Subject Family for child 1 ------*/
    /*function get_child_1_distinct(){
        $this->db->select('DISTINCT(Category), BookName');
        $this->db->group_by('Category');
        $query = $this->db->get('Books');
    }*/

    /* ------------------- Edit Child 2 ------------------- */

    /* Get Child 2 by ID */
	function get_by_id_child_2($id) {
		if (!isset($id) or $id == '') {
			return array();
		}
		$result = $this->db->get_where($this->child_2_tbl, array('id' => $id));
		return $result->row_array();
	}

    /* Save Child 2 Subject */
	function update_child_2($id,$params) {
		$this->db->where('id',$id);
		$this->db->update($this->child_2_tbl,$params);
		return TRUE;
	}
    /* ---- End Of Update Child 2--- */

    //  Hide & Show Child 2
	function hide_show_child_2($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->child_2_tbl, $data);
		return TRUE;
	}

    /* ------------------- Edit Child 3------------------- */

    /* Get Child 3 by ID */
	function get_by_id_child_3($id) {
		if (!isset($id) or $id == '') {
			return array();
		}
		$result = $this->db->get_where($this->child_3_tbl, array('id' => $id));
		return $result->row_array();
	}

    /* Save Child 3 Subject */
	function update_child_3($id,$params) {
		$this->db->where('id',$id);
		$this->db->update($this->child_3_tbl,$params);
		return TRUE;
	}
        /* ---- End Of Update Child 2--- */

    //  Hide & Show Child 3
	function hide_show_child_3($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->child_3_tbl, $data);
		return TRUE;
	}

	function add_subject_comments($id, $data){
		$this->db->where('id', $id);
		$this->db->update($this->call_table, $data);
		return TRUE;
	}

	function get_call_params($id){
		$result = $this->db->get_where($this->call_table, array('id' => $id));
		
		return $result->row_array();
	}

/* Get Child And SubChild Subjects */
	function get_child_sub_childs($id) {
		if (!isset($id) or $id == '') {
			return array();
		}
		$result = $this->db->get_where($this->child_1_tbl, array('parent_id' => $id));
		return $result->result_array();
	}
	

/* Get Parents And Childs Subjects */
	function get_parents_childs($id, $table_name) {
		if (!isset($id) or $id == '') {
			return array();
		}

		$child = $this->db->get_where($table_name, array('parent_id' => $id));
		return $child->result_array();
	}

/* ------------------------ Subject Category Statistics (Filters by Category SubCategory) ---------------------- */	

/* Get All Main Subjects*/
    /* Using for Category Export */
	function get_stat_parent_subjects($subject_id, $date_gt=false, $date_lt=false) {
		$this->db->select('*');
		$this->db->from($this->call_table);
        $this->db->where('date >=', $date_gt);
        $this->db->where('date <', $date_lt);
		$this->db->like('subject_family', $subject_id, 'after');
		$result = $this->db->get();
		return $result->result_array();
	}
    /*----------------------------*/

	function get_stat_childs($subject_family, $date_gt=false, $date_lt=false) {
		$this->db->select('*');
		$this->db->from($this->call_table);
        $this->db->where('date >=', $date_gt);
        $this->db->where('date <', $date_lt);
		$this->db->like('subject_family', $subject_family, 'after');
		$result = $this->db->get();
		return $result->result_array();
	}

    /*function get_all_subjects_by_id($table_name, $id){
        if (!isset($id) or $id == '') {
            return array();
        }
        if (!isset($table_name) or $table_name == '') {
            return array();
        }
        $result = $this->db->get_where($table_name, array('id' => $id));
        return $result->row_array();
    }*/

}
