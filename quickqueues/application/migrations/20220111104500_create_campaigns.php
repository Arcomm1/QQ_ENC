<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_campaigns extends CI_Migration {


    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'description' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 500,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'CAMPAIGN_PAUSED'
                ),
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0
                ),
                'deleted' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'default' => 0
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_campaigns');

        $data[] = array(
            'name' => 'app_campaigns',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $this->db->insert_batch('qq_config', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_campaigns');
        $this->db->query("DELETE from qq_config WHERE name = 'app_campaigns'");
    }
}
