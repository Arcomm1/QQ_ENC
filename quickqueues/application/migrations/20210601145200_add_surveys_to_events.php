<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_surveys_to_events extends CI_Migration {


    public function up()
    {
        $fields = array(
            'survey_queue' => array(
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'default' => 0
            ),
            'survey_complete' => array(
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'default' => 0
            ),
            'survey_result' => array(
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'default' => 0
            )
        );

        $this->dbforge->add_column('qq_events', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_events', 'survey_queue');
        $this->dbforge->drop_column('qq_events', 'survey_result');
        $this->dbforge->drop_column('qq_events', 'survey_complete');
    }

}