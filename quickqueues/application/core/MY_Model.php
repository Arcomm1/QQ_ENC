<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* MY_Model.php - base CodeIgniter model providing core functionality */


class MY_model extends CI_Model
{
    /**
     * Table name for model.
     *
     * Unless otherwise specified, table name is automatically guessed to be
     * plural and lowercase of class name, including optional prefix stripping _model suffix.
     */
    protected $_table = "";


    /**
     * Table name prefix.
     */
    protected $_table_prefix = "";


    /**
     * List of required fields for model.
     *
     * If set, required fields will be checked for presence prior to inserting new row.
     */
    protected $_required_fields = array();


    /**
     * Primary key of the table.
     */
    protected $_pk = "id";


    /**
     * Soft deletion support.
     */
    protected $_soft_delete = false;


    /**
     * Soft delete key, expected to be TINYINT or INT.
     */
    protected $_soft_delete_key = 'deleted';


    /**
     * If soft delete is set, whether or not to return deleted rows in results.
     */
    protected $_with_deleted = false;


    /**
     * If soft delete is set, whether or not to return only deleted rows in results.
     */
    protected $_only_deleted = false;


    /**
     * Database group name, default is default
     */
    protected $_db_group = 'default';


    /******************** PUBLIC METHODS **************************************/


    public function __construct()
    {
        parent::__construct();
        $this->load->helper('inflector');
        if (!$this->_table) {
            $this->_table = $this->_table_prefix.str_replace('_model', '', strtolower(plural(get_class($this))));
        } else {
            $this->_table = $this->_table_prefix.$this->_table;
        }
        $this->load->database($this->_db_group);
    }


    /**
     * Get all rows, optionally ordering them
     *
     * @param string $by Column by which to order
     * @param string $order Order of columns, either ASC, DESC or RAND()
     *
     * @return obj CodeIgniter database object
     */
    public function get_all($by = false, $order = 'DESC')
    {
        $this->_set_soft_delete_where();
        if ($by) {
            $this->db->order_by($by, $order);
        }
        return $this->db->get($this->_table)->result();
    }


    /**
     * Get specific row by primary key
     *
     * @param int $id Primary key
     * @return obj|bool CodeIgniter database object or false
     */
    public function get($id = false)
    {
        if (!$id) {
            return false;
        }
        $this->db->where($this->_pk, $id);
        return $this->db->get($this->_table)->row();
    }


    /**
     * Get specific row matching simple WHERE clause
     *
     * @param string $field Column name
     * @param string $value Column value
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_by($field = false, $value = false)
    {
        if (!$field || !$value) {
            return false;
        }
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        return $this->db->get($this->_table)->row();
    }


    /**
     * Get specific row matching complex WHERE statement
     *
     * @param array $where Multidimensional array of column names and values
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_one_by_complex($where = false)
    {
        if (!$where || !is_array($where) || count($where) == 0) {
            return false;
        }
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {
            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }

        return $this->db->get($this->_table)->row();
    }


    /**
     * Get multiple rows matching simple WHERE clause
     *
     * @param string $column Column name
     * @param string $value Column value
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_many_by($field = false, $value = false)
    {
        if (!$field || !$value) {
            return false;
        }
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        return $this->db->get($this->_table)->result();
    }


    /**
     * Get multiple rows matching complex WHERE statement
     *
     * @param array $where Multidimensional array of column names and values
     * @return obj|bool CodeIgniter database object or false
     */
    public function get_many_by_complex($where = false)
    {
        if (!$where || !is_array($where) || count($where) == 0) {
            return false;
        }
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {

            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }
        return $this->db->get($this->_table)->result();
    }


    /**
     * Create new row
     *
     * @param array $params Row data
     * @return int ID of the new row
     */
    function create($params = false)
    {
        if (!$params || !is_array($params) || count($params) == 0) {
            return 0;
        }
        if (count($this->_required_fields) > 0) {
            foreach ($this->_required_fields as $field) {
                if (!array_key_exists($field, $this->_required_fields)) {
                    return 0;
                }
            }
        }
        $this->db->insert($this->_table, $params);
        return $this->db->insert_id();
    }


    /**
     * Create new rows
     *
     * @param array $params Array of arrays of Row data
     * @return array Array of inserted row IDs
     */
    function create_many($rows = false)
    {
        $ids = array();
        if (!$rows || !is_array($rows) || count($rows) == 0) {
            return $ids;
        }
        foreach ($rows as $row) {
            $id = $this->create($row);
            if ($id > 0) {
                array_push($ids, $id);
            }
        }
        return $ids;
    }


    /**
     * Update existing row by primary key
     *
     * @param int $id Row ID
     * @param array $params row data
     * @return int Number of affected rows
     */
    function update($id = false, $params = false)
    {
        if (!$id || !$params) {
            return 0;
        }
        if (!is_array($params) || count($params) == 0) {
            return 0;
        }
        $this->db->where($this->_pk, $id);
        $this->db->update($this->_table, $params);
        return $this->db->affected_rows();
    }


    /**
     * Update row(s) matching simple WHERE clause
     *
     * @param string $field Column name
     * @param string $value Column value
     * @param array $params Row data
     * @return int Number of updated rows
     */
    function update_by($field = false, $value = false, $params = false)
    {
        if (!$field || !$value || !$params) {
            return 0;
        }
        if (!is_array($params) || count($params) == 0) {
            return 0;
        }
        // print_r($params);
        $this->db->where($field, $value);
        $this->db->update($this->_table, $params);
        return $this->db->affected_rows();
    }


    /**
     * Update row(s) matching complex WHERE clause
     *
     * @param string $where Multidimensional array of column names and values
     * @param array $params Row data
     * @return int Number of updated rows
     */
    function update_by_complex($where = false, $params = false)
    {
        if (!$where || !$params) {
            return 0;
        }
        if (!is_array($where) || count($where) == 0 || !is_array($params) || count($params) == 0) {
            return 0;
        }
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                $this->db->where_in($field, $value);
            } else {
                $this->db->where($field, $value);
            }

        }
        $this->db->update($this->_table, $params);
        return $this->db->affected_rows();
    }


    /**
     * Delete specific row by primary key
     *
     * @param int $id Row ID
     * @return int Number of deleted rows
     */
    public function delete($id = false)
    {
        if (!$id) {
            return 0;
        }
        $this->db->where($this->_pk, $id);
        if ($this->_soft_delete) {
            $this->db->update($this->_table, array($this->_soft_delete_key => 1));
        } else {
            $this->db->delete($this->_table);
        }
        return $this->db->affected_rows();
    }


    /**
     * Restore specific row by primary key
     *
     * @param int $id Row ID
     * @return int Number of restored rows
     */
    public function restore($id = false)
    {
        if (!$id) {
            return 0;
        }
        $this->db->where($this->_pk, $id);
        $this->db->update($this->_table, array($this->_soft_delete_key => 0));
        return $this->db->affected_rows();
    }


    /**
     * Delete row(s) matching simple WHERE clause
     *
     * @param string $field Column name
     * @param string $value Column data
     * @return int Number of deleted rows
     */
    function delete_by($field = false, $value = false)
    {
        if (!$field || !$value) {
            return 0;
        }
        $this->db->where($field, $value);
        if ($this->_soft_delete === true) {
            $this->db->update($this->_table, array($this->_soft_delete_key => 1));
        } else {
            $this->db->delete($this->_table);
        }
        return $this->db->affected_rows();
    }


    /**
     * Delet row(s) matching more complex WHERE clause
     *
     * @param array $where Multidimensional array of column names and values
     * @return int Number of deleted rows
     */
    function delete_by_complex($where = false)
    {
        if (!$where || !is_array($where) || count($where)) {
            return 0;
        }
        foreach ($where as $field => $value) {
            $this->db->where($field, $value);
        }
        if ($this->_soft_delete === true) {
            $this->db->update($this->_table, array($this->_soft_delete_key => 1));
        } else {
            $this->db->delete($this->_table);
        }
        return $this->db->affected_rows();
    }


    /**
     * Return count of all rows
     *
     * @param void
     * @return int Number of rows
     */
    function count_all()
    {
        return $this->db->count_all($this->_table);
    }


    /**
     * Count rows matching simple WHERE clause
     *
     * @param string $field Column name
     * @param string $value Column value
     * @return int Number of rows
     */
    function count_by($field = false, $value = false)
    {
        if (!$field || !$value) {
            return 0;
        }
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        return $this->db->count_all_results($this->_table);
    }


    /**
     * Count rows matching complex WHERE clause
     *
     * @param array $where Multidimensional array of column names and values
     * @return int Number of rows
     */
    function count_by_complex($where = false)
    {
        if (!$where || !is_array($where) || !count($where)) {
            return 0;
        }
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {
            if ($value == 'isnull') {
                $this->db->where("$field IS NULL");
                continue;
            }
            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }
        return $this->db->count_all_results($this->_table);
    }


    /**
     * Get sum of of specific column, matching simple WHERE clause
     *
     * @param string $sum Column name, which needs to be summed up
     * @param string $field Column name to chich match
     * @param string $value Value to be matched
     * @return int Sum of specified column
     */
    public function sum_by($sum = false, $field = false, $value = false)
    {
        if (!$sum || $field || $value) {
            return 0;
        }
        $this->db->select_sum($sum);
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        $r = $this->db->get($this->_table)->row();
        return $r->$sum;
    }


    /**
     * Get sum of of specific column, matching simple WHERE clause
     *
     * @param string $sum Column name, which needs to be summed up
     * @param string $where Multidimensional array of column names and values
     * @return int Sum of specified column
     */
    public function sum_by_complex($sum = false, $where = false)
    {

        if (!$sum || !$where) {
            return 0;
        }
        $this->db->select_sum($sum);
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {
            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }
        $r = $this->db->get($this->_table)->row();
        return $r->$sum;
    }


    /**
     * Get avg of of specific column, matching simple WHERE clause
     *
     * @param string $avg Column name, which needs to be averaged
     * @param string $field Column name to chich match
     * @param string $value Value to be matched
     * @param string|bool $round Whether to round to FLOOR, CEIL, or to not round at all
     * @return int Average of specified column
     */
    public function avg_by($avg = false, $field = false, $value = false, $round = 'FLOOR')
    {
        if (!$avg || !$field || $value) {
            return 0;
        }
        $this->db->select_avg($avg);
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        $r = $this->db->get($this->_table)->row();

        if ($round == 'FLOOR')  { return floor($r->$avg); }
        if ($round == 'CEIL')   { return ceil($r->$avg); }
        return $r->$avg;
    }


    /**
     * Get avg of of specific column, matching simple WHERE clause
     *
     * @param string $avg Column name, which needs to be averaged
     * @param string $where Multidimensional array of column names and values
     * @param string|bool $round Whether to round to FLOOR, CEIL, or to not round at all
     * @return int Average of specified column
     */
    public function avg_by_complex($avg = false, $where = false, $round = 'FLOOR')
    {

        if (!isset($avg) || !isset($where)) {
            return 0;
        }
        $this->db->select_avg($avg);
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {
            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }
        $r = $this->db->get($this->_table)->row();

        if ($round == 'FLOOR')  { return floor($r->$avg); }
        if ($round == 'CEIL')   { return ceil($r->$avg); }
        if ($round == 'ROUND')  { return round($r->$avg, 2); }
        return $r->$avg;
    }


    /**
     * Get max of of specific column, matching simple WHERE clause
     *
     * @param string $max Column name, which needs to be averaged
     * @param string $field Column name to chich match
     * @param string $value Value to be matched
     * @param string|bool $round Whether to round to FLOOR, CEIL, or to not round at all
     * @return int Average of specified column
     */
    public function max_by($max = false, $field = false, $value = false, $round = 'FLOOR')
    {
        if (!$max || !$field || $value) {
            return 0;
        }
        $this->db->select_max($max);
        $this->_set_soft_delete_where();
        if (is_array($value)) {
            $this->db->where_in($field, $value);
        } else {
            $this->db->where($field, $value);
        }
        $r = $this->db->get($this->_table)->row();

        return $r->$max;
    }


    /**
     * Get max of of specific column, matching simple WHERE clause
     *
     * @param string $max Column name, which needs to be averaged
     * @param string $where Multidimensional array of column names and values
     * @param string|bool $round Whether to round to FLOOR, CEIL, or to not round at all
     * @return int Average of specified column
     */
    public function max_by_complex($max = false, $where = false, $round = 'FLOOR')
    {

        if (!isset($max) || !isset($where)) {
            return 0;
        }
        $this->db->select_max($max);
        $this->_set_soft_delete_where();
        foreach ($where as $field => $value) {
            if ($value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        }
        $r = $this->db->get($this->_table)->row();

        return $r->$max;
    }


    /**
     * Get list of uniqued fields
     *
     * @param string $field Field name
     * @return obj|bool Codeigniter database result object, false on error
     */
    public function get_unique_fields($field = false)
    {
        if (!$field) {
            return false;
        }
        $this->db->select($field);
        $this->db->where("$field IS NOT NULL", NULL, FALSE);
        $this->db->where("$field !=", "");
        $this->db->group_by($field);
        return $this->db->get($this->_table)->result();
    }


    /**
     * Get list of uniqued fields matching simple WHERE clause
     *
     * @param string $unique_field Field name
     * @param string $field Column name
     * @param string $value Column value
     * @return obj|bool Codeigniter database result object, false on error
     */
    public function get_unique_fields_by($unique_field = false, $field = false, $value = false)
    {
        if (!$unique_field) {
            return false;
        }
        $this->db->select($unique_field);
        $this->db->where("$unique_field IS NOT NULL", NULL, FALSE);
        $this->db->where("$unique_field !=", "");
        if ($field && $value) {
            if (is_array($value)) {
                $this->db->where_in($field, $value);
            } else {
                $this->db->where($field, $value);
            }
        }
        $this->db->group_by($unique_field);
        return $this->db->get($this->_table)->result();
    }


    /**
     * Get list of uniqued fields, matching complex WHERE clause
     *
     * @param string $field Field name
     * @param array $where description
     * @return obj|bool Codeigniter database result object, false on error
     */
    public function get_unique_fields_by_complex($field = false, $where = false)
    {
        if (!$field || !$where) {
            return false;
        }
        $this->db->select($field);
        $this->db->where("$field IS NOT NULL", NULL, FALSE);
        foreach ($where as $f => $v) {
            if ($v) {
                if (is_array($v)) {
                    $this->db->where_in($f, $v);
                } else {
                    $this->db->where($f, $v);
                }
            }
        }
        $this->db->group_by($field);
        return $this->db->get($this->_table)->result();
    }


    /**
     * Get list of uniqued fields, with according count, matching complex WHERE clause
     *
     * @param string $field Field name
     * @param array $where description
     * @return obj|bool Codeigniter database result object, false on error
     */
    public function get_unique_fields_with_count_by_complex($field = false, $where = false)
    {
        if (!$field || !$where) {
            return false;
        }
        $this->db->select($field);
        $this->db->select('COUNT(*) AS count');
        $this->db->where("$field IS NOT NULL", NULL, FALSE);
        foreach ($where as $f => $v) {
            if ($v) {
                if (is_array($v)) {
                    $this->db->where_in($f, $v);
                } else {
                    $this->db->where($f, $v);
                }
            }
        }
        $this->db->group_by($field);
        return $this->db->get($this->_table)->result();
    }


    /**
     * Check if entry with specified primary key exists
     *
     * @param string $id Row ID
     *
     * @return bool
     */
    public function exists($id = false)
    {
        if (!isset($id)) {
            return false;
        }
        if ($this->count_by($this->_pk, $id) > 0) {
            return true;
        }
        return false;
    }


    /**
     * Check if entry with specified field
     *
     * @param string $id Row ID
     *
     * @return bool
     */
    public function exists_by($field = false, $value = false)
    {
        if (!$field || !$value) {
            return false;
        }

        if ($this->count_by($field, $value) > 0) {
            return true;
        }

        return false;
    }


    /**
     * Check if entry with specified fields
     *
     * @param array $where List of fieilds and values
     *
     * @return bool
     */
    public function exists_by_complex($where = false)
    {
        if (!$where || !is_array($where) || !count($where)) {
            return false;
        }

        if ($this->count_by_complex($where) > 0) {
            return true;
        }

        return false;
    }


    /**
     * Include soft deleted rows for next call
     *
     * @param void
     * @return object Self
     */
    public function with_deleted()
    {
        $this->_with_deleted = true;
        return $this;
    }


    /**
     * Include only soft deleted rows for next call
     *
     * @param void
     * @return object Self
     */
    public function only_deleted()
    {
        $this->_only_deleted = true;
        return $this;
    }


    /******************** PRIVATE METHODS *************************************/


    private function _set_soft_delete_where()
    {
        if ($this->_soft_delete === true & $this->_only_deleted === true) {
            $this->db->where($this->_soft_delete_key, 1);
        }
        if ($this->_soft_delete === true & $this->_with_deleted === false) {
            $this->db->where($this->_soft_delete_key, 0);
        }
        if ($this->_soft_delete === true & $this->_with_deleted === true) {
            $this->db->where_in($this->_soft_delete_key, array(0,1));
        }
    }


    /******************** UTILITY METHODS *************************************/


    /**
     * Return table name
     *
     * @param void
     * @return string Table name
     */
    public function get_table()
    {
        return $this->_table;
    }


    /**
     * Return table prefix
     *
     * @param void
     * @return string Table prefix
     */
    public function get_table_prefix()
    {
        return $this->_table_prefix;
    }


    /**
     * Return tables primary key
     *
     * @param void
     * @return string Primary key
     */
    public function get_primary_key()
    {
        return $this->_pk;
    }


    /**
     * Get required fields
     *
     * @param void
     * @return array List of required fields
     */
    public function get_required_fields()
    {
        return $this->_required_fields;
    }


    /**
     * Get soft delete
     *
     * @param void
     * @return bool Soft delete
     */
    public function get_soft_delete()
    {
        return $this->_soft_delete;
    }


    /**
     * Get soft delete key
     *
     * @param void
     * @return string Soft delete key
     */
    public function get_soft_delete_key()
    {
        return $this->_soft_delete_key;
    }


}
