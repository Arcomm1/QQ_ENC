<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_queue_config extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'value' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                ),
                'default' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300
                ),
            )
        );
        $this->dbforge->add_key('queue_id');
        $this->dbforge->create_table('qq_queues_config');


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

        $this->db->insert_batch('qq_config', $data);

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_queues_config');
        $this->db->query("DELETE FROM qq_config WHERE name LIKE 'qq_sla_%'");
    }
}