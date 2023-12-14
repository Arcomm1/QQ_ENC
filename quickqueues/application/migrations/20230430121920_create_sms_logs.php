<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_qq_sms_logs extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ),
                'sms_content' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'sms_token' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'sms_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'queue_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_sms_logs');
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_sms_logs');
    }
}
