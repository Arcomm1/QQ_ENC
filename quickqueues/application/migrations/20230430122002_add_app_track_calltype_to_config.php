<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_app_track_calltype_to_config extends CI_Migration {

    public function up()
    {
        // Check if the configuration 'app_track_calltype' already exists in the 'qq_config' table
        $exists = $this->db->get_where('qq_config', array('name' => 'app_track_calltype'))->row();

        if (!$exists) {
            $data = array(
                'name' => 'app_track_calltype',
                'value' => '120',
                'default' => '120',
                'category' => 'application',
            );

            // Insert the configuration if it does not exist
            $this->db->insert('qq_config', $data);
        }
    }
}
