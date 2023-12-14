<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_qq_sms_logs extends CI_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function create_table() 
    {
        $fields = array(
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

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('qq_sms_logs', TRUE);

        $data = array(
            array(
                'sms_content' => '',
                'sms_token'   => '',
                'sms_type'    => '',
                'queue_id'    => '',
                'status'      => '',
            ) 
        );

        $this->db->insert_batch('qq_sms_logs', $data);
    }
}
