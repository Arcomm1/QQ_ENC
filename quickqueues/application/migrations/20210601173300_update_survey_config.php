<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_update_survey_config extends CI_Migration {

    public function up()
    {
        $this->db->query("TRUNCATE qq_queue_config");

        $data[] = array(
            'name'      => 'queue_survey_dst',
            'value'     => 'qq-survey-ivr-template',
            'default'   => 'qq-survey-ivr-template',
            'category'  => 'queue',
        );

        $this->db->insert_batch('qq_config', $data);

        echo "Please don't forget to run tools reset_queue_config\n";
        echo "WARNING: This is backwards incompatible change!\n";

    }

    public function down()
    {
        $this->db->query("DELETE FROM qq_config WHERE name = 'queue_survey_dst'");
    }

}
