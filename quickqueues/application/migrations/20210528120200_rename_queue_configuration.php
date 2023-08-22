<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_rename_queue_configuration extends CI_Migration {

    public function up()
    {
        $this->dbforge->rename_table('qq_queues_config', 'qq_queue_config');
        $this->db->query("TRUNCATE qq_queue_config");
        $this->db->query("DELETE FROM qq_config WHERE category = 'queue'");

        $data[] = array(
            'name'      => 'queue_enable_survey',
            'value'     => 'no',
            'default'   => 'no',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_survey_max_results',
            'value'     => '20',
            'default'   => '20',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_survey_hour_start',
            'value'     => '09',
            'default'   => '09',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_survey_hour_end',
            'value'     => '18',
            'default'   => '18',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_survey_min_calltime',
            'value'     => '0',
            'default'   => '0',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_survey_grace_period',
            'value'     => '60',
            'default'   => '60',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_sla_call_time',
            'value'     => '120',
            'default'   => '120',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_sla_hold_time',
            'value'     => '120',
            'default'   => '120',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'queue_sla_overflow',
            'value'     => '10',
            'default'   => '10',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'app_time_distribution_map',
            'value'     => '15,30,60,120',
            'default'   => '15,30,60,120',
            'category'  => 'queue',
        );

        $data[] = array(
            'name'      => 'app_holdtime_distribution_map',
            'value'     => '15,30,60,120',
            'default'   => '15,30,60,120',
            'category'  => 'queue',
        );

        $this->db->insert_batch('qq_config', $data);

        echo "Please don't forget to run tools reset_queue_config\n";
        echo "WARNING: This is backwards incompatible change!\n";

    }

    public function down()
    {
        $this->dbforge->rename_table('qq_queue_config', 'qq_queues_config');
    }

}
