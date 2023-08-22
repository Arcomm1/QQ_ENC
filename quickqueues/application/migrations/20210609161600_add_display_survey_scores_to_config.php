<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_display_survey_scores_to_config extends CI_Migration {


    public function up()
    {
        $data[] = array(
            'name' => 'app_display_survey_scores',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);


        $this->dbforge->drop_column('qq_calls', 'subcategory_id');

    }


    public function down()
    {
        $this->db->query("DELETE from qq_config WHERE name = 'app_display_survey_scores'");
    }

}
