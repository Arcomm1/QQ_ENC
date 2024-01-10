<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_collation extends CI_Migration {

    public function up()
    {
      
        $query = "ALTER TABLE `qq_call_subjects_parent` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $this->db->query($query);

     
        $query = "ALTER TABLE `qq_call_subjects_child_1` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $this->db->query($query);

   
        $query = "ALTER TABLE `qq_call_subjects_child_2` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $this->db->query($query);

     
        $query = "ALTER TABLE `qq_call_subjects_child_3` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $this->db->query($query);
    }

    public function down()
    {
        // revert to the previous collation
    }
}
