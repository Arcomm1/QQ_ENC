<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_initial_schema extends CI_Migration {


    public function up()
    {
        /**
         * Create configuration table, and feed default configuration
         */

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
                'value' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                ),
                'default' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300
                ),
                'category' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_config');


         $data[] = array(
            'name' => 'app_last_parsed_event',
            'value' => '0',
            'default' => '0',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_language',
            'value' => 'english',
            'default' => 'english',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_application_name',
            'value' => 'Quickqueues',
            'default' => 'Quickqueues',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_ringnoanswer',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_ringnoanswer_minimum',
            'value' => '10',
            'default' => '10',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_ringnoanswer_unique',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_log_path',
            'value' => '/var/log/asterisk/quickqueues_log',
            'default' => '/var/log/asterisk/quickqueues_log',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'ast_queue_log_path',
            'value' => '/var/log/asterisk/queue_log',
            'default' => '/var/log/asterisk/queue_log',
            'category' => 'asterisk',
        );

        $data[] = array(
            'name' => 'ast_monitor_path',
            'value' => '/var/spool/asterisk/monitor',
            'default' => '/var/spool/asterisk/monitor',
            'category' => 'asterisk',
        );

        $data[] = array(
            'name' => 'app_track_outgoing',
            'value' => 'no',
            'default' => 'no',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_outgoing_mindst',
            'value' => '6',
            'default' => '6',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_outgoing_minbillsec',
            'value' => '10',
            'default' => '10',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_track_outgoing_from',
            'value' => '0000-00-00 00:00:00',
            'default' => '0000-00-00 00:00:00',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'app_agent_download_own_calls',
            'value' => 'yes',
            'default' => 'yes',
            'category' => 'application',
        );

        $data[] = array(
            'name' => 'q_enable_survey',
            'value' => 'no',
            'default' => 'no',
            'category' => 'queue',
        );

        $data[] = array(
            'name' => 'q_survey_max_results',
            'value' => '20',
            'default' => '20',
            'category' => 'queue',
        );

        $data[] = array(
            'name' => 'q_survey_hour_start',
            'value' => '09',
            'default' => '09',
            'category' => 'queue',
        );

        $data[] = array(
            'name' => 'q_survey_hour_end',
            'value' => '18',
            'default' => '18',
            'category' => 'queue',
        );

        $this->db->insert_batch('qq_config', $data);
        unset($data);


        /**
         * Create users table
         */

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
                'password' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                ),
                'role' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300
                ),
                'email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'extension' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'last_login' => array(
                    'type' => 'datetime',
                    'default' => '0000-00-00 00:00:00',
                ),
                'enabled' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),

            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_users');


        /**
         * Create user queue relationship
         */

        $this->dbforge->add_field(
            array(
                'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
            )
        );
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('queue_id');
        $this->dbforge->create_table('qq_user_queues');


        /**
         * Create user agent relationship
         */

        $this->dbforge->add_field(
            array(
                'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
            )
        );
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('agent_id');
        $this->dbforge->create_table('qq_user_agents');


        /**
         * Create event types and feed defalt events
         */

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
                    'constraint' => 100,
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_event_types');

        $data[] = array('name' => 'ABANDON');
        $data[] = array('name' => 'ADDMEMBER');
        $data[] = array('name' => 'AGENTDUMP');
        $data[] = array('name' => 'AGENTLOGIN');
        $data[] = array('name' => 'AGENTCALLBACKLOGIN');
        $data[] = array('name' => 'AGENTLOGOFF');
        $data[] = array('name' => 'AGENTCALLBACKLOGOFF');
        $data[] = array('name' => 'ATTENDEDTRANSFER');
        $data[] = array('name' => 'BLINDTRANSFER');
        $data[] = array('name' => 'COMPLETEAGENT');
        $data[] = array('name' => 'COMPLETECALLER');
        $data[] = array('name' => 'CONFIGRELOAD');
        $data[] = array('name' => 'CONNECT');
        $data[] = array('name' => 'ENTERQUEUE');
        $data[] = array('name' => 'EXITEMPTY');
        $data[] = array('name' => 'EXITWITHKEY');
        $data[] = array('name' => 'EXITWITHTIMEOUT');
        $data[] = array('name' => 'QUEUESTART');
        $data[] = array('name' => 'REMOVEMEMBER');
        $data[] = array('name' => 'RINGNOANSWER');
        $data[] = array('name' => 'SYSCOMPAT');
        $data[] = array('name' => 'TRANSFER');
        $data[] = array('name' => 'DID');

        $this->db->insert_batch('qq_event_types', $data);
        unset($data);


        /**
         * Create events table
         */

        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'queue_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'agent_id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ),
                'event_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'unsigned' => true,
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                ),
                'timestamp' => array(
                    'type' => 'INT',
                    'constraint' => 40,
                    'unsigned' => true,
                ),
                'date' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00',
                ),
                'ringtime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'calltime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'holdtime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'waittime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'ringtime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'position' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'origposition' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'did' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'default' => NULL,
                ),
                'src' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'default' => NULL,
                ),
                'exit_key' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
                    'default' => NULL,
                ),
                'linked_uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                    'default' => NULL,
                ),
                'dst' => array('type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                    'default' => null,)
                )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->add_key('agent_id');
        $this->dbforge->add_key('event_type');
        $this->dbforge->add_key('queue_id');
        $this->dbforge->add_key('date');
        $this->dbforge->create_table('qq_events');


        /**
         * Create calls table
         */
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'timestamp' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'date' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00'
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'queue_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'event_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'agent_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
                    'default' => NULL,
                ),
                'src' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'dst' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ),
                'calltime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'holdtime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'waittime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'ringtime' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                ),
                'position' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'origposition' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => NULL,
                ),
                'linked_uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                    'default' => NULL,
                ),
                'recording_file' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                    'null' => true,
                    'default' => NULL,
                ),
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
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->add_key('agent_id');
        $this->dbforge->add_key('event_type');
        $this->dbforge->add_key('queue_id');
        $this->dbforge->add_key('date');
        $this->dbforge->create_table('qq_calls');


        /**
         * Create queues table
         */
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'display_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'deleted' => array(
                    'type' => 'TINYINT',
                    'default' => '0',
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_queues');


        /**
         * Create agents table
         */
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'display_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ),
                'extension' => array(
                    'type' => 'INT',
                    'constraint' => 10,
                ),
                'last_call' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00'
                ),
                'on_break' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => 'no',
                ),
                'last_break' => array(
                    'type' => 'DATETIME',
                    'default' => '0000-00-00 00:00:00',
                ),
                'deleted' => array(
                    'type' => 'TINYINT',
                    'default' => '0',
                ),
            )
        );
        $this->dbforge->add_key('id');
        $this->dbforge->create_table('qq_agents');


    }


    public function down()
    {
        $this->dbforge->drop_table('qq_agents');
        $this->dbforge->drop_table('qq_calls');
        $this->dbforge->drop_table('qq_config');
        $this->dbforge->drop_table('qq_event_types');
        $this->dbforge->drop_table('qq_events');
        $this->dbforge->drop_table('qq_queues');
        $this->dbforge->drop_table('qq_user_agents');
        $this->dbforge->drop_table('qq_user_queues');
        $this->dbforge->drop_table('qq_users');

    }


}
