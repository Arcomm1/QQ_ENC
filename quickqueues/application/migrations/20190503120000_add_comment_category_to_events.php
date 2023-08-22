<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_comment_category_to_events extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'ADDCOMMENT',
            'has_calls' => 'yes',
        );

        $data[] = array(
            'name' => 'ADDCATEGORY',
            'has_calls' => 'yes',
        );

        $this->db->insert_batch('qq_event_types', $data);
    }


    public function down()
    {
        $this->db->query("DELETE from qq_event_types WHERE name = 'ADDCOMMENT'");
        $this->db->query("DELETE from qq_event_types WHERE name = 'ADDCATEGORY'");

    }

}
