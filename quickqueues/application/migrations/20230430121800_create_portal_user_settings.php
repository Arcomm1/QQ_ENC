<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_portal_user_settings extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(
            array(
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
        $this->dbforge->create_table('qq_portal_user_settings');

        $data[] = array(
            'name'      => 'call_overload',
            'value'     => '0',
            'default'   => '10'
        );

        $data[] = array(
            'name'      => 'sms_content',
            'value'     => '',
            'default'   => 'sms content'
        );
        
        $data[] = array(
            'name'      => 'sms_token',
            'value'     => '',
            'default'   => ''
        );

        $data[] = array(
            'name'      => 'sms_type',
            'value'     => '1',
            'default'   => '1'
        );

        $this->db->insert_batch('qq_portal_user_settings', $data);

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');
    }

}
