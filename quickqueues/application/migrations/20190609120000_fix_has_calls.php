<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_fix_has_calls extends CI_Migration {


    public function up()
    {
        $this->db->query("UPDATE qq_event_types SET has_calls = 'no' WHERE name = 'ADDCOMMENT'");
        $this->db->query("UPDATE qq_event_types SET has_calls = 'no' WHERE name = 'ADDCATEGORY'");
    }


    public function down()
    {
        $this->db->query("UPDATE qq_event_types SET has_calls = 'yes' WHERE name = 'ADDCOMMENT'");
        $this->db->query("UPDATE qq_event_types SET has_calls = 'yes' WHERE name = 'ADDCATEGORY'");
    }

}
