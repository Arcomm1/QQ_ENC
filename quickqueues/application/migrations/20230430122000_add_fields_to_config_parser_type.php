<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_fields_to_config_parser_type extends CI_Migration 
{

	public function up()
	{
		$data = array(
			'name' => 'parser_type',
			'value' => 'LOG',
			'default' => 'LOG',
			'category' => 'application',
		);

		// Check if the record already exists
		$query = $this->db->get_where('qq_config', array('name' => $data['name']));
		if ($query->num_rows() == 0) {
			// Record does not exist, proceed with insert
			$this->db->insert('qq_config', $data);
		} else {
			// Record exists, you can log this or take other actions if necessary
			log_message('info', 'Record with name ' . $data['name'] . ' already exists in qq_config.');
		}
	}

}