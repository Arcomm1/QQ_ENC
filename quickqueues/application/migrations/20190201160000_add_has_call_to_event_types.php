<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_has_call_to_event_types extends CI_Migration {


    public function up()
    {
        $fields = array(
            'has_calls' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'no',
            )
        );
        $this->dbforge->add_column('qq_event_types', $fields);

        $query  = "UPDATE qq_event_types SET `has_calls` = 'yes' ";
        $query .= "WHERE name IN('ABANDON', 'COMPLETECALLER', 'COMPLETEAGENT', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY')";
        $this->db->query($query);
    }

    public function down()
    {
        $this->dbforge->drop_column('qq_event_types', 'has_calls');
    }

}
