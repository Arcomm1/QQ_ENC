<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Tools.php - various, mostly command-line utilities */


class Tools extends CI_Controller {


    public function __construct()
    {
        $this->_user_ctl_allowed_actions = array(
            'create', 'edit', 'delete', 'disable', 'list'
        );
        $this->_config_ctl_allowed_actions = array(
            'create', 'edit', 'reset'
        );

        parent::__construct();


        $this->load->model('./../models/Settings_model', 'globalSettings');
        //$this->parse_queue_log();
    }

    // public function index()
    // {
    //     load_views(array('agents/index'), $this->data, true);
    // }


    /** User management */
    public function user_ctl($action = false, $username = false, $password = false, $role = false)
    {
        if (!$action) {
            echo "Usage index.php tools user_ctl ACTION [USERNAME [PASSWORD [ROLE]]]\n";
            exit("No action specified\n");
        }
        if (!in_array($action, $this->_user_ctl_allowed_actions)) {
            echo "Usage index.php tools user_ctl ACTION [USERNAME [PASSWORD [ROLE]]]\n";
            exit("Not a valid action\n");
        }


        if ($action == 'list') {
            echo "USERNAME - ROLE - EMAIL - STATUS/ENABLED\n";
            foreach ($this->User_model->get_all() as $u) {
                echo $u->name." - ".$u->role." - ".$u->email." - ".$u->enabled."\n";
            }
            exit();
        }

        if ($action == 'create') {
            if (!$username or !$password or !$role) {
                exit("Please provide username, password and role\n");
            }
            if (!in_array($role, array('guest', 'agent', 'manager', 'admin'))) {
                exit("Please specify valid role\n");
            }
	
			// Initialize an empty array to store names
			$userNames = array();
			// Iterate through the results and populate the $userNames array
			foreach ($this->User_model->get_all() as $u) {
				$userNames[] = $u->name;
			}
				
			if (in_array($username, $userNames)) {
				echo "User already exists, skip creation";
			} else {
				if ($this->User_model->create(array('name' => $username, 'password' => md5($password), 'role' => $role, 'enabled' => 'yes'))) {
					echo "User created successfully.\n";
				} else {
					echo "Could not create user.\n";
				}
			}			
            exit();
        }
    }


    /** Configuration management */
    public function config_ctl($action = false, $name = false, $value = false, $default = false, $category = false)
    {
        if (!$action) {
            echo "Usage index.php tools config_ctl ACTION [ITEM [VALUE [DEFAULT [CATEGORY]]]\n";
            exit("No action specified\n");
        }
        if (!in_array($action, $this->_config_ctl_allowed_actions)) {
            echo "Usage index.php tools config_ctl ACTION [ITEM [VALUE [DEFAULT [CATEGORY]]]\n";
            exit("Not a valid action\n");
        }

        if ($action == 'list') {
            echo "SETTING - VALUE - DEFAULT\n";
            foreach ($this->Config_model->get_all() as $s) {
                echo $s->name." - ".$s->value." - ".$s->default."\n";
            }
            exit();
        }

        if ($action == 'create') {
            if (!$name or !$value or !$default) {
                exit("Please provide configuration item name, value and default value\n");
            }
            if (!$category) {
                $category = 'custom';
            }
            if ($this->Config_model->create(array('name' => $name, 'value' => $value, 'default' => $default, 'category' => $category))) {
                echo "Configuration item created successfully.\n";
            } else {
                echo "Could not create configuration item.\n";
            }
        }

    }


    public function parse_queue_log2()
    {
        // empty function for cron job, manual testing only
    }

    /** Parse queue log file */
    public function parse_queue_log()
    {
        log_to_file('NOTICE', 'Running parser');

        parser_unlock();

        $lock = parser_read_lock();
        if ($lock) 
        {
            log_to_file('NOTICE', 'Parser is locked by procces '.$lock.', Exitting');
            exit;
        }

        log_to_file('NOTICE', 'Locking parser');
        parser_lock();


        $globalConfig   = $this->globalSettings->getSettings();
        $queue_log_path = $this->Config_model->get_item('ast_queue_log_path');
        if (!$queue_log_path) 
        {
            log_to_file('ERROR', 'Quickqueues is not configured properly, ast_queue_log_path not specified, Exitting');
            parser_unlock();
            exit();
        }

        $last_parsed_event = $this->Config_model->get_item('app_last_parsed_event');
        if ($last_parsed_event === false) 
        {
            log_to_file('ERROR', 'Quickqueues is not configured properly, app_last_parsed_event not specified, Exitting');
            parser_unlock();
            exit();
        }

        $send_sms_on_exit_event = $this->Config_model->get_item('app_send_sms_on_exit_event');

        $queue_log = @fopen($queue_log_path, 'r');
        if (!$queue_log) 
        {
            log_to_file("ERROR", "Can not open log file, Exitting");
            parser_unlock();
            exit();
        }

        // Event types
        foreach ($this->Event_type_model->get_all() as $et) 
        {
            $event_types[$et->name] = $et->id;
        }

        // Queues
        $queues = array();
        foreach ($this->Queue_model->get_all() as $q) 
        {
            $queues[$q->name] = $q;
        }

        // Agents
        $agents = array();
        foreach ($this->Agent_model->get_all() as $a) 
        {
            $agents[$a->name] = $a;
        }

        $parsed_events           = 0;
        $last_event              = false;
        $track_ringnoanswer      = $this->Config_model->get_item('app_track_ringnoanswer');
        $track_transfers         = $this->Config_model->get_item('app_track_transfers');
        $mark_answered_elsewhere = $this->Config_model->get_item('app_mark_answered_elsewhere');
        $track_duplicate_calls   = $this->Config_model->get_item('app_track_duplicate_calls');
       
        // Begin parsing
        while (($line = fgets($queue_log)) !== FALSE) 
        {

            /**
            * Event structure
            * 1366720340  |1366720340.303267  |MYQUEUE     |SIP/8007    |RINGNOANSWER |1000|x|y|z
            * Timestamp   |Unique ID          |Queue name  |Agent name  |Event name   |Variable event data
            * $ev_data[0] |$ev_data[1]        |$ev_data[2] |$ev_data[3] |$ev_data[4]  |$ev_data[n]
            */
            
            if (!$line or strlen($line) == 0) 
            {
                log_to_file("NOTICE", "Skipping empty line");
                continue;
            }

            $ev_data = explode("|", $line);

            /**
             * Some broken Asterisk instances log crooked timestamps
             * For some events. This should fix it
             */
            if (strlen($ev_data[0]) == 13) 
            {
                continue;
            }

            // Skip already parsed events
            if ($last_parsed_event >= $ev_data[0]) 
            {
                continue;
            }

            // Do not parse unknown events
            if (!array_key_exists($ev_data[4], $event_types)) 
            {
                log_to_file('WARNING', "Skipping unknown event ".$ev_data[4]);
                continue;
            }

          
            if ($ev_data[4] === 'CONFIGRELOAD') 
            {
                continue;
            }

            if ($track_ringnoanswer == 'no' and $ev_data[4] == 'RINGNOANSWER') 
            {
                continue;
            }

            $queue_id = null;

            if ($ev_data[2] == 'NONE')
			{
                $queue_id = false;
            } 
            else 
            {
                if (!array_key_exists($ev_data[2], $queues)) 
                {
                    $new_queue_id = $this->Queue_model->create(array('name' => $ev_data[2], 'display_name' => $ev_data[2]));
                    if (!$new_queue_id) 
                    {
                        log_to_file('ERROR', 'Could not create new queue, event timestamp and uniqueid are '.$ev_data[0]."|".$ev_data['1']);
                        continue;
                    }
                    $queues[$ev_data[2]] = $this->Queue_model->get($new_queue_id);
                    $queue_id = $new_queue_id;
                } 
                else 
                {
                    $queue_id = $queues[$ev_data[2]]->id;
                }
            }
            
            $agent_id = null;

            if ($ev_data[3] == 'NONE') 
            {
                $agent_id = false;
            } 
            else 
            {
                if (!array_key_exists($ev_data[3], $agents)) 
                {
                    if (in_array($ev_data[4], array('STARTPAUSE', 'STARTSESSION', 'STOPSESSION', 'STOPPAUSE'))) 
                    {
                        log_to_file('NOTICE', 'Not creating new agent since they have no calls yet');
                        continue;
                    }
                    $new_agent_id = $this->Agent_model->create(array('name' => $ev_data[3], 'display_name' => $ev_data[3]));
                    if (!$new_agent_id) 
                    {
                        log_to_file('ERROR', 'Could not create new agent, event timestamp and uniqueid are '.$ev_data[0]."|".$ev_data['1']);
                        continue;
                    }
                    $agent_id = $new_agent_id;
                    $this->Agent_model->set_extension($agent_id);
                    $agents[$ev_data[3]] = $this->Agent_model->get($new_agent_id);
                } 
                else 
                {
                    $agent_id = $agents[$ev_data[3]]->id;
                }
            }

            /** Process the event *********************************************/

            /** Get "constant" event data *************************************/
            $event = array(
                'timestamp'     => $ev_data[0],
                'uniqueid'      => $ev_data[1],
                'queue_id'      => $queue_id,   // $ev_data[2]
                'agent_id'      => $agent_id,   // $ev_data[3]
                'event_type'    => $ev_data[4],
                'date'          => date('Y-m-d H:i:s', $ev_data[0]),
            );

            /** Get "variable" event data *************************************/


            /**
             * ENTERQUEUE - call entered queue, new Call row should be created
             */
            if ($ev_data[4] == 'ENTERQUEUE') 
            {
                // $ev_data[5] is holding "url" param, which we do not use
                $event['src'] = $ev_data[6];
                $this->Call_model->create($event);
            }

            /**
             * CONNECT - call that entered queue (ENTERQUEUE event) was connected to agents,
             * update call entry matching ENTERQUEUE and current unique ID
             */
            if ($ev_data[4] == 'CONNECT') 
            {
                // This event has holdtime, in $ev_data[5], but we do not need to
                // store this since same value will be processed through COMPLETECALLER
                // or COMPLETEAGENT event
                $event['linked_uniqueid'] = $ev_data[6];
                $event['ringtime'] = $ev_data[7];
                $event['dst'] = $agents[$ev_data[3]]->extension;
                $this->Call_model->update_by_complex(array('uniqueid' => $ev_data[1],'event_type' => 'ENTERQUEUE'), $event);
                unset($event['dst']);
                $this->Queue_model->add_agent($queue_id, $agent_id);
                $this->Agent_model->add_primary_queue($agent_id, $queue_id);
            }
            
            /**
             * COMPLETE - call that was CONNECTed to agent was completed.
             * Update call entry matching CONNECT event and current unique ID
             */

            $callProcessed = false;

            if ($ev_data[4] == 'COMPLETEAGENT' || $ev_data[4] == 'COMPLETECALLER') 
            {
                if(!$callProcessed)
                {
                    $event['holdtime'] = $ev_data[5];
                    $event['calltime'] = $ev_data[6];
                    $event['origposition'] = $ev_data[7];

                    $this_call = $this->Call_model->get_one_by_complex(array('uniqueid' => $ev_data[1], 'event_type' => 'CONNECT'));

                    if ($mark_answered_elsewhere > 0) 
                    {
                        log_to_file('DEBUG', 'Marking Unanswered calls as answered_elsewhere since config is set to '.$mark_answered_elsewhere);

                        log_to_file('DEBUG', 'Searching for unanswered calls from '.$this_call->src.', current call ID is '.$this_call->id);
                        $from = date('Y-m-d H:i:s', (time() - $mark_answered_elsewhere * 60));

                        if (strlen($this_call->src) > 1) 
                        {
                            $calls_to_mark = $this->Call_model->get_many_by_complex(
                                array(
                                    'src' => $this_call->src,
                                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT'),
                                    'date >' => $from,
                                )
                            );

                            log_to_file('DEBUG', 'Found '.count($calls_to_mark). ' calls to mark as answered elsewhere');
                            foreach ($calls_to_mark as $ctm) 
                            {
                                log_to_file('DEBUG', 'Marking '.$ctm->id.' as answered elsewhere');
                                $this->Call_model->update($ctm->id, array('answered_elsewhere' => $this_call->id));
                            }

                            unset($calls_to_mark);
                            unset($from);
                        }
                        else 
                        {
                            log_to_file('DEBUG', 'Something went wrong, not searching for calls to mark as answered elsewhere, uniqueid '.$ev_data[1]);
                        }
                    }

                    if ($track_duplicate_calls > 0) 
                    {
                        log_to_file('DEBUG', 'Marking calls as duplicate since config is set to '.$track_duplicate_calls);
                        log_to_file('DEBUG', 'Searching for all calls from '.$this_call->src.', current call ID is '.$this_call->id);

                        $from = date('Y-m-d H:i:s', (time() - $track_duplicate_calls * 60));

                        if (strlen($this_call->src) > 1) 
                        {
                            $calls_to_mark = $this->Call_model->get_many_by_complex(
                                array(
                                    'src' => $this_call->src,
                                    'date >' => $from,
                                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'COMPLETECALLER', 'COMPLETEAGENT'),
                                )
                            );

                            log_to_file('DEBUG', 'Found '.count($calls_to_mark). ' calls to mark as duplicate');

                            foreach ($calls_to_mark as $ctm) 
                            {
                                log_to_file('DEBUG', 'Marking '.$ctm->id.' as duplicate');
                                $this->Call_model->update($ctm->id, array('duplicate' => 'yes'));
                            }

                            unset($calls_to_mark);
                            unset($from);
                        } 
                        else 
                        {
                            log_to_file('DEBUG', 'Something went wrong, not search for calls to mark as duplicate, uniqueid '.$ev_data[1]);
                        }
                    }

                    unset($this_call);

                    $this->Call_model->update_by_complex(array('uniqueid' => $ev_data[1], 'event_type' => 'CONNECT'), $event);
                    $this->Agent_model->update($agent_id, array('last_call' => date('Y-m-d H:i:s')));
                    if (!$this->Call_model->get_recording($event['uniqueid'])) {
                        log_to_file('ERROR', "Could not get recording file for unique ID ".$event['uniqueid']);
                    }

                    /**
                     * We need to update corresponding DID event with new timestamp and date, so that
                     * call completion date matches DId event
                     */
                        $this->Event_model->update_by_complex(
                            array(
                                'uniqueid' => $ev_data[1],
                                'event_type' => 'DID_FUTURE',
                            ),
                            array(
                                'timestamp' => $event['timestamp'],
                                'date' => $event['date'],
                                'event_type' => 'DID'
                            )
                        );

                        if ($track_transfers == 'yes') 
                        {
                            $transfer = $this->check_transfer($ev_data[1]);
                            if (is_array($transfer)) 
                            {
                                log_to_file('NOTICE', "$uniqueid was transferred, updating call information");
                                $this->Call_model->update_by_complex(
                                    array('uniqueid' => $ev_data[1], 'event_type' => $ev_data[4]),
                                    array('transferred' => $transfer[0], 'transferdst' => $transfer[1])
                                );
                            }
                        }
                    /**
                     * If someone set some fields for this call from agent_crm,
                     * it should be stored in as Future Event.
                     */
                    $future_event = $this->Future_event_model->get_by('uniqueid', $ev_data[1]);
                    if (!$future_event) 
                    {
                        log_to_file('DEBUG', 'No future event found for call with uniqueid '.$ev_data[1]);
                    } 
                    else {
                        log_to_file('DEBUG', 'Future event found for call with uniqueid '.$ev_data[1].', updating call');

                        $this->Call_model->update_by_complex(
                            array('uniqueid' => $ev_data[1], 'event_type' => $ev_data[4]),
                            array(
                                'comment'       => $future_event->comment,
                                'status'        => $future_event->status,
                                'priority'      => $future_event->priority,
                                'curator_id'    => $future_event->curator_id,
                                'category_id'   => $future_event->category_id,
                                'service_id'    => $future_event->service_id,
                                'service_product_id' => $future_event->service_product_id,
                                'service_product_type_id' => $future_event->service_product_type_id,
                                'service_product_subtype_id' => $future_event->service_product_subtype_id,
                                'subject_family' => $future_event->subject_family,
                                /*'custom_1'      => $future_event->custom_1,
                                'custom_2'      => $future_event->custom_2,
                                'custom_3'      => $future_event->custom_3,
                                'custom_4'      => $future_event->custom_4,
                                'ticket_id'     => $future_event->ticket_id,
                                'ticket_department_id' => $future_event->ticket_department_id,
                                'ticket_category_id' => $future_event->ticket_category_id,
                                'ticket_subcategory_id' => $future_event->ticket_subcategory_id,
                                'subject_comment' => $future_event->subject_comment,*/
                            )
                        );
                        log_to_file('DEBUG', 'Deleting Future event with uniqueid '.$ev_data[1]);

                        // $this->Future_event_model->delete_by('uniqueid', $ev_data[1]);

                    }

                    $this->Call_model->mark_for_survey($ev_data[1]);
                    $callProcessed = true;
                }
            }
            /**
             * ABANDON, EXIT* - call was terminated in some way.
             * Call entry matching ENTERQUEUE and currect unique ID should updated
             */

      
            //echo "<br><br>";
            //var_dump($globalConfig);
            /*
            $globalConfig['sms_type'] == 
            1 abandon. exitempty. exittimeout - rodesac zari ar/ver shedga
            2 completecaller, completeagent   - rodesac zari carmatebit shedga da dasrulda
            */  

            $smsSent = false;
            $lastEventTimestamp = $this->Call_model->get_last_event_timestamp($ev_data[1]);
            // Check if the current event or queue change occurred after the last event for the call
            echo $event['timestamp'] . "LAST:".$lastEventTimestamp.'<br>';
            if ($event['timestamp'] >= $lastEventTimestamp)
            {
                // Check if the SMS has not been sent yet
                echo 'პირობა შესრულდა<br>';
                if(!$smsSent)
                {
                  
                    echo $ev_data[4].' '.$globalConfig['sms_type'].'<br>';
                    if (($globalConfig['sms_type'] == "1" && ($ev_data[4] == 'ABANDON' || $ev_data[4] == 'EXITEMPTY' || $ev_data[4] == 'EXITWITHTIMEOUT')) ||
                        ($globalConfig['sms_type'] == "2" && ($ev_data[4] == 'COMPLETECALLER' || $ev_data[4] == 'COMPLETEAGENT')))
                    { 
                        
                        echo 'should send<br>';
                        $event['position']     = $ev_data[5];
                        $event['origposition'] = $ev_data[6];
                        $event['waittime']     = $ev_data[7];

                        $this->Call_model->update_by_complex(array('uniqueid' => $ev_data[1],'event_type' => 'ENTERQUEUE'), $event);

                        $number_for_sms = $this->Call_model->get_number_for_sms($ev_data[1]);
                        
                        if($globalConfig['queue_id'] === $queue_id and $globalConfig['queue_id'] === $number_for_sms['queue_id'])
                        {
                            
                            $sms_number     = $number_for_sms['src'];
                            $this->send_sms($sms_number,$globalConfig['sms_content'],$globalConfig['sms_token']);
                            $smsSent        = true;
                        }
                        elseif($globalConfig['queue_id'] === 'all')
                        {
                            $sms_number     = $number_for_sms['src'];
                            $this->send_sms($sms_number,$globalConfig['sms_content'],$globalConfig['sms_token']);
                            $smsSent        = true;
                            // log_to_file('NOTICE', "Tried to send SMS for for unique ID ".$event['uniqueid']);
                        }

                    } 
                }  
            }
            $this->Event_model->update_by_complex(
                array(
                    'uniqueid' => $ev_data[1],
                    'event_type' => 'DID_FUTURE',
                ),
                array(
                    'timestamp' => $event['timestamp'],
                    'date' => $event['date'],
                    'event_type' => 'DID'
                )
                );

                if (!$this->Call_model->get_recording($event['uniqueid'])) 
                {
                    log_to_file('ERROR', "Could not get recording file for unique ID ".$event['uniqueid']);
                } 
            /**
             * EXITWITHKEY - caller left queue by pressing specific key.
             * Update call entry matching ENTERQUEUE and current unique ID
             */

            if ($ev_data[4] == 'EXITWITHKEY') {
                $event['position'] = $ev_data[6];
                $event['origposition'] = $ev_data[7];
                $event['waittime'] = $ev_data[8];

                $this_call = $this->Call_model->get_one_by_complex(array('uniqueid' => $ev_data[1], 'event_type' => 'ENTERQUEUE'));

                if ($track_duplicate_calls > 0) {
                    log_to_file('DEBUG', 'Marking EXITWITHKEY calls as duplicate since config is set to '.$track_duplicate_calls);
                    log_to_file('DEBUG', 'Searching for all EXITWITHKEY calls from '.$this_call->src.', current call ID is '.$this_call->id);

                    $from = date('Y-m-d H:i:s', (time() - $track_duplicate_calls * 60));

                    if (strlen($this_call->src) > 1) {
                        $calls_to_mark = $this->Call_model->get_many_by_complex(
                            array(
                                'src' => $this_call->src,
                                'date >' => $from,
                                //'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'COMPLETECALLER', 'COMPLETEAGENT'),
                                'event_type' => array('EXITWITHKEY'),
                            )
                        );

                        log_to_file('DEBUG', 'Found '.count($calls_to_mark). ' EXITWITHKEY calls to mark as duplicate');

                        foreach ($calls_to_mark as $ctm) {
                            log_to_file('DEBUG', 'Marking '.$ctm->id.' as duplicate');
                            $this->Call_model->update($ctm->id, array('duplicate' => 'yes'));
                        }

                        unset($calls_to_mark);
                        unset($from);
                    } else {
                        log_to_file('DEBUG', 'Something went wrong, not search for calls to mark as duplicate, uniqueid '.$ev_data[1]);
                    }
                }

                unset($this_call);

                $this->Call_model->update_by_complex(array('uniqueid' => $ev_data[1],'event_type' => 'ENTERQUEUE'), $event);

                $this->Event_model->update_by_complex(
                    array(
                        'uniqueid' => $ev_data[1],
                        'event_type' => 'DID_FUTURE',
                    ),
                    array(
                        'timestamp' => $event['timestamp'],
                        'date' => $event['date'],
                        'event_type' => 'DID'
                    )
                );

                $event['exit_key'] = $ev_data[5];
                $this->Call_model->update_by_complex(array('uniqueid' => $ev_data[1], 'event_type' => 'EXITWITHKEY'), array('duplicate' => 'no'));
                if (!$this->Call_model->get_recording($event['uniqueid'])) {
                    log_to_file('ERROR', "Could not get recording file for unique ID ".$event['uniqueid']);
                }
            }

            /**
             * We do some custom magic here.
             *
             * Since we are interested in DID events in realtion to already finished calls,
             * we are inserting DID events (which appear at the beginning of the call)
             * as DID_FUTURE, so that they are not visible to stats related functions.
             * Once call is finished in one way or another, these DID_FUTURE events
             * are updated, 'renamed' to be proper DID and updated with timestamp
             * of the corresponding event, be it COMPLETE, EXIT, or ABANDON
             */
            if ($ev_data[4] == 'DID') {
                $event['event_type'] = 'DID_FUTURE';
                $event['did'] = trim(preg_replace('/\s+/', '', $ev_data[5]));
            }

            /**
             * We use RINGNOANSWER to associate queues ans agents
             */
            if ($ev_data[4] == 'RINGNOANSWER') {
                $event['ringtime'] = $ev_data[5]/1000;
                $this->Queue_model->add_agent($queue_id, $agent_id);
                $this->Agent_model->add_primary_queue($agent_id, $queue_id);

                // No need to insert empty ringnoanswer events
                if ($ev_data[5] == 0) {
                    continue;
                }
            }

            /**
             * ADDCOMMENT, pretty self explanatory
             */
            if ($ev_data[4] == 'ADDCOMMENT') {
                $t_uniqueid = $ev_data[5];
                $t_comment = preg_replace('/\n$/','',$ev_data[6]);
                $this->Call_model->update_by('uniqueid', $t_uniqueid, array('comment' => $t_comment));
                log_to_file('NOTICE', "Adding comment to ".$t_uniqueid);

                unset($t_comment);
                unset($t_uniqueid);
            }

            /**
             * ADDCATEGORY, pretty self explanatory
             */
            if ($ev_data[4] == 'ADDCATEGORY') {
                $t_uniqueid = $ev_data[5];
                $t_category = $ev_data[6];
                $this->Call_model->update_by('uniqueid', $t_uniqueid, array('category_id' => $t_category));
                log_to_file('NOTICE', "Adding category to ".$t_uniqueid);

                unset($t_category);
                unset($t_uniqueid);
            }

            /**
             * Mark agent as paused
             */
            if ($ev_data[4] =='STARTPAUSE') {
                $l_pause = $this->Event_model->get_agent_last_pause_event($agent_id);
                if ($l_pause) {
                    if ($ev_data[4] == $l_pause->event_type) {
                        log_to_file('NOTICE', 'Skipping duplicate '.$ev_data[4].' event');
                        continue;
                    }
                } else {
                    log_to_file('DEBUG', 'Got event '.$ev_data[4].'. Agent has no previous pause related events');
                }
                $this->Agent_model->update($agent_id, array('on_break' => 'yes', 'last_break' => $event['date']));
                unset($l_pause);
            }

            /**
             * Mark agent as 'active'
             */
            if ($ev_data[4] =='STOPPAUSE') {
                $l_pause = $this->Event_model->get_agent_last_pause_event($agent_id);
                if ($l_pause) {
                    if ($ev_data[4] == $l_pause->event_type) {
                        log_to_file('NOTICE', 'Skipping duplicate '.$ev_data[4].' event');
                        continue;
                    }
                    /**
                     * If agent last event was STARPAUSE, we need to calculate delta
                     * between current event and last PAUSE event, to know how much time
                     * agent was on break.
                     */
                    $pausetime = $ev_data[0] - $l_pause->timestamp;
                    $event['pausetime'] = $pausetime;
                } else {
                    log_to_file('DEBUG', 'Got event '.$ev_data[4].'. Agent has no previous pause related events');
                }

                $this->Agent_model->update($agent_id, array('on_break' => 'no'));
                unset($l_pause);
                unset($pausetime);
            }

            /**
             * Start agent session
             */
            if ($ev_data[4] =='STARTSESSION') {
                $l_session = $this->Event_model->get_agent_last_session_event($agent_id);
                if ($l_session) {
                    if ($ev_data[4] == $l_session->event_type) {
                        log_to_file('NOTICE', 'Skipping duplicate '.$ev_data[4].' event');
                        continue;
                    }
                } else {
                    log_to_file('DEBUG', 'Got event '.$ev_data[4].'. Agent has no previous session related events');
                }
                $this->Agent_model->update($agent_id, array('in_session' => 'yes', 'last_session' => $event['date']));

                $settings = $this->get_settings($agent_id);

                if (array_key_exists('agent_work_start_time', $settings)) {
                    $delta = $timestamp - strtotime(date('Y-m-d '.$settings['agent_work_start_time']->value.':00'));
                    if ($delta > 600) {
                        $event['session_start_late'] = $delta;
                    }
                }
                unset($l_session);
            }

            /**
             * Stop/end agent session
             */
            if ($ev_data[4] =='STOPSESSION') {
                $l_session = $this->Event_model->get_agent_last_session_event($agent_id);
                if ($l_session) {
                    if ($ev_data[4] == $l_session->event_type) {
                        log_to_file('NOTICE', 'Skipping duplicate '.$ev_data[4].' event');
                        continue;
                    }
                    /**
                     * If agent last event was STARTSESSION, we need to calculate delta
                     * between current event and last SESSION event, to know how much time
                     * agent was in.
                     */
                    $sessiontime = $ev_data[0] - $l_session->timestamp;
                    $event['sessiontime'] = $sessiontime;
                } else {
                    log_to_file('DEBUG', 'Got event '.$ev_data[4].'. Agent has no previous pause related events');
                }

                $this->Agent_model->update($agent_id, array('in_session' => 'no'));
                unset($l_session);
                unset($sessiontime);
                $settings = $this->get_settings($agent_id);

                if (array_key_exists('agent_work_end_time', $settings)) {
                    $delta = $timestamp - strtotime(date('Y-m-d '.$settings['agent_work_end_time']->value.':00'));
                    if ($delta > 600) {
                        $event['session_end_early'] = $delta;
                    }
                }
            }

            /**
             * INCOMINGOFFWORK - This event is triggered by custom QQ dialplan.
             * This event is triggered when call is made on non-working hours.
             */
            if ($ev_data[4] == 'INCOMINGOFFWORK') {
                $event['src'] = $ev_data[5];
                $this->Call_model->create($event);
                log_to_file('debug', "Got event ".$ev_data[4]);
            }

            /**
             * SURVEYRESULT - This event is triggered by custom QQ dialplan.
             * This event should have the same Unique ID as already processed COMPLETECALLER
             * or COMPLETEAGENT event.
             * Just collect Survey result and update the call information
             */
            if ($ev_data[4] == 'SURVEYRESULT') {
                $event['src'] = $ev_data[5];
                log_to_file('NOTICE', "Got survey result for call ".$ev_data[1]);

                $event['survey_result'] = $ev_data[5];
                $event['survey_complete'] = '1';

                $this->Call_model->update_by_complex(
                    array('uniqueid' => $ev_data[1], 'event_type' => array('COMPLETEAGENT', 'COMPLETECALLER')),
                    array(
                        'survey_result'    => $ev_data[5],
                        'survey_complete'  => '1',
                    )
                );

            }

            /**
             * Custom DIALOUTATTEMPT event
             *
             * This event should be generated prior to executing outgoing call with API.
             * This event contains real unique ID of call, that can be used later to associate custom ID to real call
             */
            if ($ev_data[4] == 'DIALOUTATTEMPT') {
                $event['custom_uniqueid'] = trim(preg_replace('/\s+/', '', $ev_data[5]));
            }

            /**
             * Custom DIALOUTFAILED event
             *
             * This event should be generated when generated call fails for some reason
             */
            if ($ev_data[4] == 'DIALOUTFAILED') {
                $event['custom_uniqueid'] = trim(preg_replace('/\s+/', '', $ev_data[5]));
                $event['dialout_fail_reason'] = $ev_data[6];
            }

            /** End event processing ******************************************/

            // Create the event
            if (!$this->Event_model->create($event)) {
                log_to_file('ERROR', 'Could not insert '.$ev_data[4].' event with unique ID '.$ev_data[1]);
            }

            $last_event = $ev_data[0];
            $parsed_events++;
            unset($agent_id);
            unset($queue_id);
            unset($event);

        }


        $this->Config_model->set_item('app_last_parsed_event', $last_event);
        log_to_file('NOTICE', 'Parsed '.$parsed_events.' events');

        $this->collect_outgoing();
        // $this->collect_custom_dids();

        log_to_file('NOTICE', 'Unlocking parser');
        parser_unlock();
    }


    public function collect_outgoing()
    {
        $collect = $this->Config_model->get_item('app_track_outgoing');
        $from    = QQ_TODAY_START;
        $mark_called_back = $this->Config_model->get_item('app_auto_mark_called_back');

        if ($collect == 'no') {
            log_to_file('NOTICE', 'Collecting outgoing calls is disabled in configuration, aborting.');
            return 0;
        }

        log_to_file('NOTICE', 'Collecting outgoing calls');

        $this->cdrdb = $this->load->database('cdrdb', true);
        $agents = $this->Agent_model->get_all();
        foreach ($agents as $a) {
            log_to_file('NOTICE', 'Collecting outgoing calls for agent '.$a->display_name.'...');

            $this->cdrdb->where('src', $a->extension);
            $this->cdrdb->where('calldate >', $from);
            $this->cdrdb->where('userfield !=', 'QQCOLLECTED');
            $this->cdrdb->where_in('dcontext', array('from-internal', 'ext-local'));
            $calls = $this->cdrdb->get('cdr');

            if ($calls->num_rows() == 0) {
                log_to_file('NOTICE', 'Agent '.$a->display_name.' has no outgoing calls, skipping');
                continue;
            }
            log_to_file('NOTICE', 'Agent '.$a->display_name.' has '.$calls->num_rows().' outgoing calls, collecting');
            foreach ($calls->result() as $c) {

                $event_data = array();
                $event_data = array();
                $event_data['agent_id'] = $a->id;
                $event_data['event_type'] = str_replace(' ', '', 'OUT_'.$c->disposition);
                $event_data['timestamp'] = strtotime($c->calldate);
                $event_data['calltime'] = $c->billsec;
                $event_data['src'] = $c->src;
                $event_data['dst'] = $c->dst;
                $event_data['date'] = $c->calldate;
                $event_data['uniqueid'] = $c->uniqueid;
                if ($a->primary_queue_id) {
                    $event_data['queue_id'] = $a->primary_queue_id;
                }

                $call_data = $event_data;
                $future_event = $this->Future_event_model->get_by('uniqueid',$event_data['uniqueid']);
                if (!$future_event) {
                    log_to_file('DEBUG', 'No future event found for call with uniqueid '.$event_data['uniqueid']);
                } else {
                    log_to_file('DEBUG', 'Future event found for outgoing call with uniqueid '.$event_data['uniqueid'].', updating call');
                    $call_data['comment']       = $future_event->comment;
                    $call_data['status']        = $future_event->status;
                    $call_data['priority']      = $future_event->priority;
                    $call_data['curator_id']    = $future_event->curator_id;
                    $call_data['category_id']   = $future_event->category_id;
                    $call_data['service_id']    = $future_event->service_id;
                    $call_data['service_product_id'] = $future_event->service_product_id;
                    $call_data['service_product_type_id'] = $future_event->service_product_type_id;
                    $call_data['service_product_subtype_id'] = $future_event->service_product_subtype_id;
                    $call_data['custom_1']      = $future_event->custom_1;
                    $call_data['custom_2']      = $future_event->custom_2;
                    $call_data['custom_3']      = $future_event->custom_3;
                    $call_data['custom_4']      = $future_event->custom_4;
                    $call_data['subject_family'] = $future_event->subject_family;
                    $call_data['subject_comment'] = $future_event->subject_comment;


                    // log_to_file('DEBUG', 'Deleting Future event with uniqueid '.$ev_data[1]);
                    // $this->Future_event_model->delete_by('uniqueid', $ev_data[1]);
                }
                $call_data['recording_file'] = $c->recordingfile;

                $this->Event_model->create($event_data);
                $this->Call_model->create($call_data);

                if ($mark_called_back > 0) {
                    log_to_file('NOTICE', 'Auto marking of called back calls is enabled, getting relevant calls');
                    log_to_file('DEBUG', 'Searching for all calls from '.$call_data['dst']);
                    $mark_from = date('Y-m-d H:i:s', (time() - $mark_called_back * 60));

                    if (strlen($call_data['dst']) > 5) {
                        if ($call_data['event_type'] == 'OUT_ANSWERED') {
                            $sanitized_dst = substr($call_data['dst'], -9);
                            $calls_to_mark = $this->Call_model->get_many_by_complex(
                                array(
                                    'src' => $sanitized_dst,
                                    'date >' => $mark_from,
                                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'COMPLETECALLER', 'COMPLETEAGENT', 'INCOMINGOFFWORK'),
                                )
                            );

                            log_to_file('DEBUG', 'Found '.count($calls_to_mark). ' calls to mark as called_back');

                            foreach ($calls_to_mark as $ctm) {
                                log_to_file('DEBUG', 'Marking '.$ctm->id.' as called_back');
                                $this->Call_model->update($ctm->id, array('called_back' => 'yes'));
                            }

                            unset($calls_to_mark);
                            unset($mark_from);
                        }
                    } else {
                        log_to_file('DEBUG', 'Something went wrong or call is internal, not searching for calls to mark as called_back, uniqueid '.$call_data['uniqueid']);
                    }

                }


                $this->cdrdb->update('cdr', array('userfield' => 'QQCOLLECTED'), array('uniqueid' => $c->uniqueid));
                log_to_file('NOTICE', "Creating event and call ".$event_data['event_type']." for CDR with uniqueid ".$c->uniqueid);

                if ($event_data['timestamp'] > $a->last_call) {
                    $this->Agent_model->update($a->id, array('last_call' => $event_data['date']));
                }

                unset($event_data);
                unset($call_data);
            }
        }

    }

    public function send_sms($sms_number,$text,$token)
    {
		echo "Sending SMS: " . $sms_number. " | " . $text . "  |  " . $token . "<br>";
        /*----CURL SEND SMS---*/
        $data = array(
            "number" => $sms_number,
            "text"   => $text,
            "key"    => $token
        );
    
        
        $url = "https://sms.ar.com.ge/api/integration/sms";
        #$url = "https://sms.ar.com.ge/api/integration/sms-latin";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
     
        
        if (!$response) {
            die(curl_error($ch) . " - Code: " . curl_errno($ch));
        }
    
        curl_close($ch);
        //var_dump($response);
    }

    // public function collect_custom_dids()
    // {
    //     /**
    //      * This potentially can be moved to setting to simulate manual DID events
    //      */
    //     log_to_file('NOTICE', 'Collecting custom DID events');

    //     $this->cdrdb = $this->load->database('cdrdb', true);
    //     $this->cdrdb->where('did', '2560051');
    //     $this->cdrdb->where('userfield !=', 'QQDID');

    //     $events = $this->cdrdb->get('cdr');

    //     foreach ($events->result() as $e) {
    //         $this->Event_model->create(array(
    //             'event_type' => 'DID',
    //             'timestamp' => strtotime($e->calldate),
    //             'date' => $e->calldate,
    //             'did' => '2560051',
    //             'queue_id' => 1,
    //         ));
    //         $this->cdrdb->update('cdr', array('userfield' => 'QQDID'), array('uniqueid' => $e->uniqueid));

    //     }

    //     return 0;
    // }


    public function check_transfer($uniqueid = false)
    {
        if (!$uniqueid) {
            return false;
        }

        $this->cdrdb = $this->load->database('cdrdb', true);
        log_to_file('NOTICE', 'Checking if calls is transferred');
        $this->cdrdb->where('linkedid', $uniqueid);
        $this->cdrdb->where('eventtype', 'ATTENDEDTRANSFER');
        $events = $this->cdrdb->get('cel');

        if ($events->num_rows() == 0) {
            log_to_file('NOTICE', "$uniqueid was not transferred");
            return false;
        }

        if ($events->num_rows() > 1) {
            log_to_file('ERROR', "$uniqueid has too many transfer events");
            return false;
        }

        $transferdata = json_decode($events->row()->extra, true);

        $transferdst = $transferdata['transfer_target_channel_name'];
        $transferdst = explode('/', $transferdst);
        $transferdst = explode('-', $transferdst[1]);
        $transferdst = $transferdst[0];

        return array('yes', $transferdst);
    }


    public function qqctl_usage()
    {
        echo "qqcltl - Manage Quickqueues from command line\n\n";
        echo "Usage:\n";
        echo "  qqctl [command] [options]\n\n";
        echo "Available commands:\n";
        echo "version           - Show version version\n";
        echo "help              - Show this screen\n";
    }


    public function test_db() {
        include_once(APPPATH.'third_party/xlsxwriter.class.php');
        $filename = "/root/example.xlsx";
        $rows = array(
            array('2003','1','-50.5','2010-01-01 23:00:00','2012-12-31 23:00:00'),
            array('2003','=B1', '23.5','2010-01-01 00:00:00','2012-12-31 00:00:00'),
        );
        $writer = new XLSXWriter();
        $writer->setAuthor('Some Author');
        foreach($rows as $row)
        $writer->writeSheetRow('Sheet1', $row);
        $writer->writeToFile('/root/example.xlsx');

    }


    public function fix_dids()
    {
        $dids = $this->Event_model->get_many_by('event_type', 'DID');
        foreach ($dids as $d) {
            $c = $this->Call_model->get_one_by_complex(
                array(
                    'uniqueid' => $d->uniqueid,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT', 'ABANDON', 'EXITWITHTIMEOUT', 'EXITWITHKEY', 'EXITEMPTY')
                )
            );

            if (!$c) {
                echo "ERROR ERROR ERROR ERROR ".$d->uniqueid." - ".$d->date."\n";
            }
        }
    }


    public function merge_agents($src = false, $dst = false)
    {
        if (!$src || !$dst) {
            log_to_file('ERROR', 'Can not merge agents - source agent ID and destination agent ID are mandatory');
            echo "Can not merge agents - source agent ID and destination agent ID are mandatory\n";
            return 0;
        }
        $e = $this->Event_model->update_by('agent_id', $src, array('agent_id' => $dst));
        $c = $this->Call_model->update_by('agent_id', $src, array('agent_id' => $dst));
        $p = $this->User_model->update_by('associated_agent_id', $src, array('associated_agent_id' => $dst));

        foreach ($this->Queue_model->get_all() as $q) {
            $this->Queue_model->remove_agent($q->id, $src);
            $this->Queue_model->add_agent($q->id, $dst);
        }

        foreach ($this->User_model->get_all() as $u) {
           $this->User_model->unassign_agent($u->id, $src);
           $this->User_model->assign_agent($u->id, $dst);
        }


        $a = $this->Agent_model->delete($src);

        log_to_file('DEBUG', 'Agents merged succesfully - '.$e.' events and '.$c.' calls are affected. '.$a.' agents deleted');
        echo "Agents merged succesfully - ".$e." events and ".$c." calls are affected. ".$a." agents deleted and ".$p." users changed associated agent ID\n";
        return 0;
    }


    public function reset_all_agent_settings()
    {
        foreach ($this->Agent_model->get_all() as $a) {
            $this->Agent_model->set_default_settings($a->id);
        }
    }


    public function create_queue($name = false, $display_name = false)
    {
        if (!$name) {
            echo "Please provide queue name\n";
            exit();
        }

        if (!$display_name) {
            $display_name = $name;
        }

        $this->Queue_model->create(array('name' => $name, 'display_name' => $display_name));
        echo "Queue created succesfully\n";
    }


    public function create_agent($name = false, $extension = false, $primary_queue_id = false)
    {
        if (!$name || !$extension || !$primary_queue_id) {
            echo "Please provide agent name, extension, and primary queue ID\n";
            exit();
        }

        $agent_id = $this->Agent_model->create(
            array(
                'name' => $name,
                'display_name' => $name,
                'primary_queue_id' => $primary_queue_id
            )
        );

        $this->Agent_model->set_extension($agent_id, $extension);

        $this->Queue_model->add_agent($primary_queue_id, $agent_id);

        echo "Agent created succesfully\n";
    }


    public function perform_survey()
    {
        log_to_file('NOTICE', 'Gatheting calls for survey');
        $calls = $this->Call_model->get_survey_queue();

        if (count($calls) == 0) {
            log_to_file('NOTICE', 'No calls in survey queue');
            exit();
        }

        log_to_file('NOTICE', 'Found '.count($calls).' in survey queue, proceeding...');

        foreach ($calls as $c) {
            log_to_file('NOTICE', "Performing survey for call with Unique ID ".$c->uniqueid);
            $this->Call_model->make_survey_call($c);
        }
    }


    public function reset_queue_config()
    {
        foreach ($this->Queue_model->get_all() as $q) {
            $queue_config = $this->Queue_model->get_config($q->id);
        }
    }


    public function sync_queues()
    {
        $this->Queue_model->ingest_freepbx_queues();
    }


    public function sync_queue_agents()
    {
        $this->Queue_model->ingest_freepbx_queue_agents();
    }


    // Helper and temporary functions

    public function test_survey($uniqueid = false)
    {
        foreach ($this->Call_model->get_survey_queue() as $c) {
            print_r($c);
        }
    }


    public function get_auto_callback_queue()
    {
        $calls = $this->Call_model->get_auto_callback_queues();
        foreach ($calls as $call) {
            $this->Call_model->make_auto_callback($call);
        }
    }


    public function test($exten = false)
    {
        $this->load->library('asterisk_manager');
        print_r($this->asterisk_manager->queue_status(900));
    }

}
