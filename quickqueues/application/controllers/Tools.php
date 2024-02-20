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

		// This is added
		$this->load->model([
			'Agent_model',
			'Broadcast_notification_model',
			'Call_model',
			'Call_subjects_model',
			'Config_model',
			'Contact_model',
			// 'Crocobet_contact_model', // Uncomment if needed
			'Event_model',
			'Event_type_model',
			'Future_event_model',
			'Queue_model',
			'Reset_password_tmp_model',
			'User_log_model',
			'User_model',
			'Dnd_model'
		]);

		$this->initializeData(); // Ensure data is initialized.	
		$this->initializeR(); // Initialize $this->r
		$this->initializeRData(); // Initialize $this->r->data
        
        include_once(APPPATH.'controllers/Persistant.php');        
        $this->persist = new Persistant();
    }

	protected function initializeData() {
		// Ensure $this->data is an object
		if (!isset($this->data)) {
			$this->data = new stdClass();
		}
	}
	
    // Initialize $this->r as an empty stdClass
    protected function initializeR() {
        if (!isset($this->r)) {
            $this->r = new stdClass();
        }
    }	
	
    // Initialize $this->r as an empty stdClass
    protected function initializeRData() {
        if (!isset($this->r->data)) {
            $this->r->data = new stdClass();
        }
    }    

    public function index()
    {
        load_views(array('agents/index'), $this->data, true);
    }

    public function clearCacheKeys()
    {
        //var_dump($_SESSION['request_keys']);
        
        //$_SESSION['request_keys'] = null;
        //$_SESSION['cached_data'] = null;
        $this->persist->clearData();
        echo 'keyes are cleared';
        
    }

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
			if (!$name || !$value || !$default) {
				exit("Please provide configuration item name, value, and default value.\n");
			}
			if (!$category) {
				$category = 'custom';
			}

			// Check if the configuration item already exists
			$existingItem = $this->Config_model->get_item($name);
			if ($existingItem) {
				// Directly update the existing item in the database
				$updateQuery = "UPDATE qq_config SET value = ?, `default` = ?, category = ? WHERE name = ?";
				$result = $this->db->query($updateQuery, array($value, $default, $category, $name));
				if ($result) {
					echo "Configuration item updated successfully.\n";
				} else {
					echo "Could not update the configuration item.\n";
				}
			} else {
				// Create a new item
				if ($this->Config_model->create(array('name' => $name, 'value' => $value, 'default' => $default, 'category' => $category))) {
					echo "Configuration item created successfully.\n";
				} else {
					echo "Could not create the configuration item.\n";
				}
			}
		}
    }

	public function get_all_cached() {
        // Generate a random sleep time between 0 to 500 milliseconds
        //$milliseconds = rand(0, 500);

        // Sleep for the randomly generated time
        //usleep($milliseconds * 1000); // usleep takes microseconds
        
		$this->load->library('asterisk_manager');
		
		$all_agents = $this->Agent_model->get_all();
		$all_queues = $this->Queue_model->get_all();
		
		$extensions   = [];
        foreach ($all_agents as $a)
		{
			if ($a->extension !=""){array_push($extensions,$a->extension);}
        }		

		$all = $this->asterisk_manager->get_all($all_queues,$extensions);
		if ($all !== false) {
			$this->r->data = $all['queue'];
			
			$all_final = array();
			
			$queueData = $this->Queue_model->get_queue_entries();
		
			foreach ($this->r->data as &$queueStatus) {
				if (isset($queueStatus['data']['Queue'])) {
					foreach ($queueData as $queueEntry) {
						if ($queueStatus['data']['Queue'] == $queueEntry['name']) 
						{
							// Check if 'data' array exists before adding 'displayName'
							if (!isset($queueStatus['data'])) {
								$queueStatus['data'] = array();
							}
							$queueStatus['data']['displayName'] = $queueEntry['display_name'];
						}
					}
				}
			}
			// Set get_realtime_data
			$all_final['queue'] = $this->r->data;
			// Set get_stats_for_all_queues
			$all_final['queue_stats'] = $this->get_stats_for_all_queues(false);
			
			$all_calls = array();
			foreach ($all['status'] as $r) {
				if (array_key_exists('Channel', $r)) {
					if (array_key_exists('Seconds', $r) && ($r['Context'] == 'macro-dial-one' || $r['Context'] == 'macro-dialout-trunk')) {
						$r['second_party'] = '';
						$r['agent_exten'] = '';
						$r['duration'] = $r['Seconds'];
						$r['direction'] = '';
						if ($r['Context'] == 'macro-dial-one') {
							$r['direction'] = 'down';
							$r['second_party'] = $r['ConnectedLineNum'];
							$r['agent_exten'] = $r['CallerIDNum'];
						}
						if ($r['Context'] == 'macro-dialout-trunk') {
							$r['direction'] = 'up';
							$r['second_party'] = $r['ConnectedLineNum'];
							$num = explode('/', $r['Channel']);
							$num = explode('-', $num[1]);
							$r['agent_exten'] = $num[0];
						}
						$all_calls[$r['agent_exten']] = $r;
					}
				}
			}
			
			// Set get_current_calls_for_all_agents
			$all_final['all_calls'] = $all_calls;

			$extensions_line = array();		
			$statuses_line = array();
			$agent_statuses = array();
			$status = $all['extensions'];
			$extensions_line = $all['sip_status'];
			
			foreach ($extensions_line as $extensionData) {
				$extension = isset($extensionData["ObjectName"]) ? $extensionData["ObjectName"] : null;
				$ipStatus = isset($extensionData["Address-IP"]) ? $extensionData["Address-IP"] : null;
				$statusInfo = [
					"extension" => $extension,
					"ip_status" => $ipStatus,
				];
				$statuses_line[] = $statusInfo;
			}		
			
			foreach ($status as $ar) {
				$state_colors = get_extension_state_colors();
				if (array_key_exists('Context', $ar)) {
					if ($ar['Context'] == QQ_AGENT_CONTEXT) {
						if (in_array($ar['Exten'], $extensions)) {
							$ar['status_color'] = $state_colors[$ar['Status']];
							$agent_statuses[$ar['Exten']] = $ar;
						}
					}
				}
			}
			
			foreach ($agent_statuses as &$agentStatus) {
				$exten = $agentStatus["Exten"];
				$matchingStatus = null;
				foreach ($statuses_line as $statusInfo) {
					if ($statusInfo["extension"] === $exten) {
						$matchingStatus = $statusInfo;
						break;
					}
				}
				if ($matchingStatus !== null && $matchingStatus["ip_status"] === "(null)") {
					$agentStatus["StatusText"] = "Unavailable";
					$agentStatus["status_color"] = "secondary";
				}
			}		

			// Set get_realtime_status_for_all_agents
			$all_final['agent_statuses'] = $agent_statuses;	
			
			//// Extensions update end
			$cacheFile = './json/get_all.json';
			file_put_contents($cacheFile, json_encode($all_final, JSON_PRETTY_PRINT));		
		}
	}
	
    public function get_stats_for_all_queues($queue_id = false) {
		$this->data->user_queues = $this->Queue_model->get_all();
		$this->data->user_agents = $this->Agent_model->get_all();
		
        foreach ($this->data->user_queues as $q) {
            $queue_ids[] = $q->id;
        }

        $date_range['date_gt'] = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_range['date_lt'] = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $agent_call_stats  = $this->Call_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_ids, $date_range);
       
        foreach ($this->data->user_agents as $a) {
            $agent_stats[$a->id] = array(
                'display_name'              => $a->display_name,
                'calls_answered'            => 0,
                'calls_outgoing'            => 0,
                'calls_missed'              => 0,
                'total_calltime'            => 0,
                'total_ringtime'            => 0,
                'avg_calltime'              => 0,
                'avg_ringtime'              => 0,
                'agent_id'                  => 0,
                'total_data'                => 0,
                'calls_outgoing_answered'   => 0,
                'calls_outgoing_unanswered' => 0,
                'incoming_total_calltime'   => 0,
                'outgoing_total_calltime'   => 0
            );
        }
        foreach($agent_call_stats as $s) {
            $agent_stats[$s->agent_id]['calls_answered']            = $s->calls_answered;
            $agent_stats[$s->agent_id]['calls_outgoing']            = $s->calls_outgoing;
            $agent_stats[$s->agent_id]['total_calltime']            = $s->total_calltime;
            $agent_stats[$s->agent_id]['total_ringtime']            = $s->total_ringtime;
            $agent_stats[$s->agent_id]['avg_calltime']              = ceil($s->total_calltime == 0 ? 0 : $s->total_calltime / ($s->calls_answered + $s->calls_outgoing));
            $agent_stats[$s->agent_id]['avg_ringtime']              = ceil($s->total_ringtime == 0 ? 0 : $s->total_ringtime / $s->calls_answered);
            $agent_stats[$s->agent_id]['agent_id']                  = $s->agent_id;
            $agent_stats[$s->agent_id]['total_data']                = $s;
            $agent_stats[$s->agent_id]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $agent_stats[$s->agent_id]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $agent_stats[$s->agent_id]['outgoing_total_calltime']   = $s->outgoing_total_calltime;

        }
        foreach ($agent_event_stats as $s) 
        {
            if ($s->agent_id) 
            {
                $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
            }
        }
		
		return $agent_stats;
    }

    /** Parse queue log file */
    public function parse_queue_log()
    {
                // for testing only
        // $this->clearCacheKeys(); 

        echo '1<br>';
        log_to_file('NOTICE', 'Running parser');

                log_to_file('NOTICE', 'Unlocking parser');
        parser_unlock_complex();

        echo '2<br>';
        $lock = parser_read_lock();
        if ($lock) 
        {
            log_to_file('NOTICE', 'Parser is locked by procces '.$lock.', Exitting');
            exit;
        }


        echo '3<br>';
        log_to_file('NOTICE', 'Locking parser');
        parser_lock();

        $globalConfig   = $this->globalSettings->getSettings();
        $allSettings    = $this->globalSettings->getAllSettings();
        $queue_log_path = $this->Config_model->get_item('ast_queue_log_path');
        // Additional paramethers for manual roll back and reparse
		$queue_log_rollback = $this->Config_model->get_item('queue_log_rollback');
		$queue_log_rollback_days = $this->Config_model->get_item('queue_log_rollback_days');
        $queue_log_rollback_with_deletion = $this->Config_model->get_item('queue_log_rollback_with_deletion');
		$queue_log_force_duplicate_deletion = $this->Config_model->get_item('queue_log_force_duplicate_deletion');
        $queue_log_fix_agent_duplicates = $this->Config_model->get_item('queue_log_fix_agent_duplicates');
		
		// Directory containing queue_log files
		$directory_queue_log = '/var/log/asterisk';
		// Name of the merged file
		$merged_queue_log = $directory_queue_log . '/merged_queue_log';		
		
        if (!$queue_log_path) 
        {
            log_to_file('ERROR', 'Quickqueues is not configured properly, ast_queue_log_path not specified, Exitting');
            parser_unlock();
            exit();
        }

        echo '4<br>';
        $queue_log = @fopen($queue_log_path, 'r');
        if (!$queue_log) 
        {
            log_to_file("ERROR", "Can not open log file, Exitting");
            parser_unlock();
            exit();
        }
        
        echo '5<br>';
//***************************************************************
		if ($queue_log_rollback == "yes") 
        {
            echo '6<br>';
			// Calculate the date
			$period_in_days = date('Y-m-d', strtotime("-{$queue_log_rollback_days} days"));

			// Prepare the query
			$query = $this->db->select('timestamp')
							  ->from('qq_calls')
							  ->where('date >=', $period_in_days)
							  ->order_by('date', 'ASC')
							  ->limit(1)
							  ->get();

			// Fetch the result
			$result = $query->row_array();

			// Check for errors and result
			if ($query === FALSE || empty($result)) {
				log_message('error', 'Error fetching timestamp: ' . (isset($this->db->error()['message']) ? $this->db->error()['message'] : 'Unknown error'));
				$last_parsed_event = 1; // or some other default value
				echo "Timestamp from $queue_log_rollback_days days ago: NOT FOUND - NO DELETION \n";
			} else {
				echo "Timestamp from $queue_log_rollback_days days ago: " . $result['timestamp'] . "\n";
				$last_parsed_event = $result['timestamp'];
			}

			$this->db->update('qq_config', array('value' => $last_parsed_event), array('name' => 'app_last_parsed_event'));

			if ($queue_log_rollback_with_deletion == "yes" and $last_parsed_event != "1") {
				// Check if $last_parsed_event is set and is a valid timestamp
				if (isset($last_parsed_event) && is_numeric($last_parsed_event)) {
					// Delete records from qq_calls where timestamp is greater than $last_parsed_event
					$this->db->where('timestamp >', $last_parsed_event);
					$this->db->delete('qq_calls');

					$this->db->where('timestamp >', $last_parsed_event);
					$this->db->delete('qq_events');
                    
					$uniqueid = $this->db->select('uniqueid')->from('qq_calls')->where('timestamp', $last_parsed_event)->get()->row()->uniqueid;
					
					// Check if $uniqueid is not empty
					if (!empty($uniqueid)) {
						// Update the userfield in the cdr table where uniqueid is greater than $uniqueid
						$this->db->set('userfield', '')
								 ->where('uniqueid >', $uniqueid)
								 ->where('userfield', 'QQCOLLECTED')
								 ->update('asteriskcdrdb.cdr');
					}                    
                    
				} else {
					log_message('error', 'Invalid last_parsed_event timestamp, cannot perform deletion.');
				}
			}

			// Get all queue_log files
			$allFiles = glob($directory_queue_log . '/queue_log*');

			// Separate queue_log and dated files
			$datedFiles = array_filter($allFiles, function($filename) {
				return strpos($filename, 'queue_log-') !== false;
			});
			$queueLog = array_filter($allFiles, function($filename) {
				return strpos($filename, 'queue_log-') === false;
			});

			// Sort dated files by date in ascending order
			usort($datedFiles, function($a, $b) {
				return strcmp($a, $b);
			});

			// Append queue_log to the end of the array
			$filesToMerge = array_merge($datedFiles, $queueLog);

			// Check if merged file exists, delete it if it does
			if (file_exists($merged_queue_log)) {
				if (!unlink($merged_queue_log)) {
					die("Failed to delete existing file: $merged_queue_log");
				}
			}

			// Open file handle for writing to the merged file
			$handle = fopen($merged_queue_log, 'a'); // 'a' mode to append to the file

			// Check if handle is valid
			if ($handle) {
				foreach ($filesToMerge as $file) {
					$content = file_get_contents($file);
					fwrite($handle, $content . "\n");
				}
				fclose($handle);
				echo "Files merged successfully into '$merged_queue_log'.";
			} else {
				echo "Unable to open file for writing.";
			}


			// Read the merged file
			$queue_log = @fopen($directory_queue_log . '/merged_queue_log', 'r');
			if (!$queue_log) {
				log_to_file("ERROR", "Can not open log file, Exitting");
				parser_unlock();
				exit();
			}
		}
		else 
        {
			// Check if merged file exists, delete it if it does
			if (file_exists($merged_queue_log)) {
				if (!unlink($merged_queue_log)) {
					die("Failed to delete existing file: $merged_queue_log");
				}
			}			
        $last_parsed_event = $this->Config_model->get_item('app_last_parsed_event');
    }
		//***************************************************************
		
        if ($last_parsed_event === false) 
        {
            log_to_file('ERROR', 'Quickqueues is not configured properly, app_last_parsed_event not specified, Exitting');
            parser_unlock();
            exit();
        }
        echo '7<br>';
        $send_sms_on_exit_event = $this->Config_model->get_item('app_send_sms_on_exit_event');

        // Step 1: Select all extensions from qq_agents
		$this->db->where("extension IS NOT NULL");
		$this->db->where("extension !=", ""); // This checks if extension is not an empty string
		$query = $this->db->get('qq_agents');
        
        if ($query && $query->num_rows() > 0) {
            $qq_agents = $query->result();
        
            foreach ($qq_agents as $qq_agent) {
                // Step 2: Check if there is a matching name in users table with the same extension
                $extension = $qq_agent->extension;
                $matching_user = $this->db->get_where('users', array('extension' => $extension))->row();
        
                if ($matching_user && ($matching_user->name != $qq_agent->name)) {
                    // The condition above checks if there's a matching user and their names are different
                    // Step 3: Update qq_agents name and display_name only if the name in users does not match the name in qq_agents
                    $this->db->where('id', $qq_agent->id);
                    $this->db->update('qq_agents', array(
                        'name' => $matching_user->name,
                        'display_name' => $matching_user->name // You can update display_name to the same value as name
                    ));
                }
            }
        } else {
            // Handle the case where no data is returned or query fails
            // For example, log an error or handle it appropriately
            log_message('error', 'No data returned from qq_agents or query failed.');
        }

        /* New Method For Agent Management - Not Used Yet
		// Agent Rename based on asterisk users
		// Select all agents from qq_agents
		$query = $this->db->get('qq_agents');

		if ($query && $query->num_rows() > 0) {
			$qq_agents = $query->result();

			foreach ($qq_agents as $qq_agent) {
				// Prepare data for possible archiving, including the extension and current date
				$archiveData = [
					'agent_id'      => $qq_agent->id, // Use original ID as agent_id
					'name'          => $qq_agent->name,
					'display_name'  => $qq_agent->display_name,
					'extension'     => $qq_agent->extension, // Include the extension
					'date'          => date('Y-m-d H:i:s'), // Current timestamp
				];

				// Handle agents with non-empty extensions
				if (!empty($qq_agent->extension)) {
					// Check for a matching user by extension
					$matching_user = $this->db->get_where('users', ['extension' => $qq_agent->extension])->row();

                    if ($matching_user && ($matching_user->name != $qq_agent->name)) {
                        // The condition above checks if there's a matching user and their names are different
                        // Step 3: Update qq_agents name and display_name only if the name in users does not match the name in qq_agents
						$this->db->where('id', $qq_agent->id);
						$this->db->update('qq_agents', [
							'name' => $matching_user->name,
							'display_name' => $matching_user->name
						]);
					} else {
						// Extension exists in qq_agents but not in users, archive the agent
						$this->db->insert('qq_agents_archived', $archiveData);
						$this->db->where('id', $qq_agent->id);
						$this->db->delete('qq_agents');
					}
				} else {
					// For agents with empty or NULL extensions, check the name pattern before archiving
					if (!preg_match('/Local\/\d+@from-queue\/n/', $qq_agent->name)) {
						// If name does not match the pattern, proceed to archive
						$this->db->insert('qq_agents_archived', $archiveData);
						$this->db->where('id', $qq_agent->id);
						$this->db->delete('qq_agents');
					}
					// If the name matches the pattern, the agent is not archived, so no action needed here
				}
			}
		} else {
			// Log error if no agents found or if the query failed
			log_message('error', 'No data returned from qq_agents or query failed.');
		}
		
		// Clear unreal archived agents
		$query = $this->db->query("SELECT agent_id FROM qq_agents_archived");
		$agents = $query->result_array();
		foreach ($agents as $agent) {
			$agent_id = $agent['agent_id'];
			
			// Check for records in qq_calls
			$queryCalls = $this->db->query("SELECT COUNT(*) as count FROM qq_calls WHERE agent_id = $agent_id");
			$resultCalls = $queryCalls->row();
			
			// Check for records in qq_events
			$queryEvents = $this->db->query("SELECT COUNT(*) as count FROM qq_events WHERE agent_id = $agent_id");
			$resultEvents = $queryEvents->row();
			
			// If no records found in both tables, delete from qq_agents_archived
			if ($resultCalls->count == 0 && $resultEvents->count == 0) {
				$this->db->where('agent_id', $agent_id);
				$this->db->delete('qq_agents_archived');
			}
		}
		
		// Step 4: Create Agents
		$this->db->select('data');
		$this->db->where('keyword', 'member');
		$query = $this->db->get('queues_details');
		$queueMembers = $query->result_array();
		
		foreach ($queueMembers as $member) {
			// First, try to extract the extension number
			preg_match('/Local\/(\d+)@from-queue\/n,0/', $member['data'], $matches);
			$extensionFound = !empty($matches) && isset($matches[1]);
			$extension = $extensionFound ? $matches[1] : '';

			$insertData = [
				'name' => '',
				'display_name' => '',
				'extension' => '',
			];

			if ($extensionFound) {
				// If an extension was found, check if it exists in the users table
				$userQuery = $this->db->get_where('users', ['extension' => $extension]);
				if ($userQuery->num_rows() > 0) {
					$user = $userQuery->row();
					// Prepare insert data using user info
					$insertData['name'] = $user->name; // Assuming 'name' is the correct field in users
					$insertData['display_name'] = $user->name;
					$insertData['extension'] = $extension;
				}
			}

			// If no user was found with the extension, use a modified version of the data field
			if (empty($insertData['name'])) {
				if ($extensionFound && strlen($extension) > 6) {
					// Construct name from the data field if extension is not used
					$nameToUse = 'Local/' . $extension . '@from-queue/n';
					$insertData['name'] = $nameToUse;
					$insertData['display_name'] = $nameToUse;
					// Keep 'extension' empty since we're not using it in this case
				}
			}

			// Check if this name or extension already exists in qq_agents to avoid duplicates
			if (!empty($insertData['extension'])) {
				$agentQuery = $this->db->get_where('qq_agents', ['extension' => $insertData['extension']]);
			} else {
				$agentQuery = $this->db->get_where('qq_agents', ['name' => $insertData['name']]);
			}

			if ($agentQuery->num_rows() == 0 && (!empty($insertData['extension']) || !empty($insertData['name']))) {
				// Insert into qq_agents
				$this->db->insert('qq_agents', $insertData);
			}
		}        
        */
       
            try 
    {		
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
       
        echo '8<br>';
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
            echo '888<br>';

            $ev_data = explode("|", $line);

            /**
             * Some broken Asterisk instances log crooked timestamps
             * For some events. This should fix it
             */
            if (strlen($ev_data[0]) == 13) 
            {
                continue;
            }

            echo '9<br>';
            // Skip already parsed events
            if ($last_parsed_event >= $ev_data[0]) 
            {
                continue;
            }

            echo '10<br>';
            // Do not parse unknown events
            if (!array_key_exists($ev_data[4], $event_types)) 
            {
                log_to_file('WARNING', "Skipping unknown event ".$ev_data[4]);
                continue;
            }

          
            echo '11<br>';
            if ($ev_data[4] == 'CONFIGRELOAD') 
            {
                continue;
            }

            echo '12<br>';
            if ($track_ringnoanswer == 'no' and $ev_data[4] == 'RINGNOANSWER') 
            {
                continue;
            }

            $queue_id = null;

            echo '13<br>';
            if ($ev_data[2] == 'NONE')
			{
                $queue_id = false;
            } 
            else 
            {
                if (!array_key_exists($ev_data[2], $queues)) 
                {
                    echo '14<br>';
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
                    echo '15<br>';
                    $queue_id = $queues[$ev_data[2]]->id;
                }
            }
            
            $agent_id = null;

            if ($ev_data[3] == 'NONE') 
            {
                echo '16<br>';
                $agent_id = false;
            } 
            else 
            {
                echo '17<br>';
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
            
			//************************************************* - Must re check
			if ($ev_data[4] == 'ENTERQUEUE') 
            {
                echo '18<br>';
                //$this->clearCacheKeys();
                $event['src'] = $ev_data[6];
                $this->Call_model->create($event);
            }	
          		
			//************************************************* - Must re check
		 
            /**
             * CONNECT - call that entered queue (ENTERQUEUE event) was connected to agents,
             * update call entry matching ENTERQUEUE and current unique ID
             */
            if ($ev_data[4] == 'CONNECT') 
            {
                echo '19<br>';
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
                echo '20<br>';
                if(!$callProcessed)
                {
                    $event['holdtime'] = $ev_data[5];
                    $event['calltime'] = $ev_data[6];
                    $event['origposition'] = $ev_data[7];

                    $this_call = $this->Call_model->get_one_by_complex(array('uniqueid' => $ev_data[1], 'event_type' => 'CONNECT'));

                    if ($mark_answered_elsewhere > 0) 
                    {
                        echo '21<br>';
                        log_to_file('DEBUG', 'Marking Unanswered calls as answered_elsewhere since config is set to '.$mark_answered_elsewhere);

                        log_to_file('DEBUG', 'Searching for unanswered calls from '.$this_call->src.', current call ID is '.$this_call->id);
                        // Reparse with mark_answered_elsewhere
						if ($queue_log_rollback_with_deletion == "yes") {
							$from = date('Y-m-d H:i:s', strtotime("-{$queue_log_rollback_days} days"));
						}
						else {
							$from = date('Y-m-d H:i:s', (time() - $mark_answered_elsewhere * 60));
						}

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

            if ($ev_data[4] == 'ABANDON' || $ev_data[4] == 'EXITEMPTY' || $ev_data[4] == 'EXITWITHTIMEOUT') {
					$event['position'] = $ev_data[5];
					$event['origposition'] = $ev_data[6];
					$event['waittime'] = $ev_data[7];
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


					if (!$this->Call_model->get_recording($event['uniqueid'])) {
						log_to_file('ERROR', "Could not get recording file for unique ID ".$event['uniqueid']);
					}
				}
            
            /** SMS
            //echo "<br><br>";
            //var_dump($globalConfig);
            /*
            $globalConfig['sms_type'] == 
            1 abandon. exitempty. exittimeout - rodesac zari ar/ver shedga
            2 completecaller, completeagent   - rodesac zari carmatebit shedga da dasrulda
            */  

            $smsSent            = false;
            $lastEventTimestamp = $this->Call_model->get_last_event_timestamp($ev_data[1]);

            if ($queue_log_rollback !="yes") {
    			foreach ($allSettings as $setting) 
                {
                    // Check if the current event or queue change occurred after the last event for the call
                    echo $event['timestamp'] . "LAST:" . $lastEventTimestamp . '<br>';
                    if ($event['timestamp'] >= $lastEventTimestamp) 
                    {
                        // Check if the SMS has not been sent yet
                        echo 'პირობა შესრულდა<br>';
                        if (!$smsSent) 
                        {
                            $queue_id_arr = explode(',', $setting['queue_id']);
                            echo $ev_data[4] . ' ' . $setting['sms_type'] . '<br>';
                            if (($setting['sms_type'] == "1" && ($ev_data[4] == 'ABANDON' || $ev_data[4] == 'EXITEMPTY' || $ev_data[4] == 'EXITWITHTIMEOUT')) ||
                                ($setting['sms_type'] == "2" && ($ev_data[4] == 'COMPLETECALLER' || $ev_data[4] == 'COMPLETEAGENT'))) 
                            {
                                echo 'should send<br>';
                                $number_for_sms        = $this->Call_model->get_number_for_sms($ev_data[1]);
                
                                if (in_array($number_for_sms['queue_id'], $queue_id_arr) && $setting['queue_id'] === $number_for_sms['queue_id'] && $setting['status'] === 'active') 
                                {
                                    echo $setting['queue_id'] . "rigis aidi";
                                    $sms_number = $number_for_sms['src'];
                                    $this->send_sms($sms_number, $setting['sms_content'], $setting['sms_token']);
                                    $smsSent    = true;
                                }
                            }
                        }
                    }
                }
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
            //$this->clearCacheKeys();
        }


        $this->Config_model->set_item('app_last_parsed_event', $last_event);
        log_to_file('NOTICE', 'Parsed '.$parsed_events.' events');

        $this->collect_outgoing();
    
        // $this->collect_custom_dids();

		//*************************************************
		// Calculate the time range
		$endTime = date('Y-m-d H:i:s'); // Current time
		
		if ($queue_log_rollback == "yes") {
			$startTime = date('Y-m-d H:i:s', strtotime("-{$queue_log_rollback_days} days"));
		}
		else {
			$startTime = date('Y-m-d H:i:s', strtotime("-1 hours"));
		}

		// Can be used as Custom Start Date to remove duplicates
		// $startTime = date('2024-01-01 00 00');

		// Query to find duplicates, excluding the first occurrence in Calls
		$duplicateQuery_qq_calls = "
			SELECT *
			FROM qq_calls
			WHERE (uniqueid, event_type, src, agent_id, calltime, date)
			IN (
				SELECT uniqueid, event_type, src, agent_id, calltime, date
				FROM qq_calls
				WHERE date BETWEEN ? AND ?
				GROUP BY uniqueid, event_type, src, agent_id, calltime, date
				HAVING COUNT(*) > 1
			)
			AND id NOT IN (
				SELECT MIN(id)
				FROM qq_calls
				WHERE date BETWEEN ? AND ?
				GROUP BY uniqueid, event_type, src, agent_id, calltime, date
			);
		";
		
		// Query to find duplicates, excluding the first occurrence in Events
		$duplicateQuery_qq_events = "
			SELECT *
			FROM qq_events
			WHERE (uniqueid, event_type, agent_id, queue_id, date)
			IN (
				SELECT uniqueid, event_type, agent_id, queue_id, date
				FROM qq_events
				WHERE date BETWEEN ? AND ?
				GROUP BY uniqueid, event_type, agent_id, queue_id, date
				HAVING COUNT(*) > 1
			)
			AND id NOT IN (
				SELECT MIN(id)
				FROM qq_events
				WHERE date BETWEEN ? AND ?
				GROUP BY uniqueid, event_type, agent_id, queue_id, date
			);
		";
		
		// Bind parameters and execute the query
		$duplicateCalls = $this->db->query($duplicateQuery_qq_calls, array($startTime, $endTime, $startTime, $endTime))->result_array();
		$duplicateEvents = $this->db->query($duplicateQuery_qq_events, array($startTime, $endTime, $startTime, $endTime))->result_array();

		// Process the results and mart all found duplicate entries in DB
		foreach ($duplicateCalls as $one_call) {
			$this->Call_model->update($one_call['id'], array('duplicate_record' => 'yes'));
		}
		foreach ($duplicateEvents as $one_event) {
			$this->Event_model->update($one_event['id'], array('duplicate_record' => 'yes'));
		}		

		if ($queue_log_force_duplicate_deletion == "yes") {
			// Delete duplicate marked calls
			$this->db->where('duplicate_record', 'yes')->where('date >=', $startTime)->where('date <=', $endTime)->delete('qq_calls');
			// Delete duplicate marked events
			$this->db->where('duplicate_record', 'yes')->where('date >=', $startTime)->where('date <=', $endTime)->delete('qq_events');
		}
		
		// Tracking Local Calls
		// Step 1: Mark Local Calls
		$this->db->query("UPDATE qq_calls SET call_type = 'local' WHERE (src IN (SELECT extension FROM qq_agents) OR src IN (SELECT extension FROM users) OR src IN (SELECT extension FROM qq_agents_archived)) AND (dst IN (SELECT extension FROM qq_agents) OR dst IN (SELECT extension FROM users) OR dst IN (SELECT extension FROM qq_agents_archived)) AND date >= ? AND date <= ?", [$startTime, $endTime]);

		// Step 2: Mark Abandoned Local Calls
		$this->db->query("UPDATE qq_calls SET call_type = 'local_abandoned' WHERE (src IN (SELECT extension FROM qq_agents) OR src IN (SELECT extension FROM users) OR src IN (SELECT extension FROM qq_agents_archived)) AND (dst IS NULL OR dst = '') AND event_type = 'ABANDON' AND agent_id = 0 AND date >= ? AND date <= ?", [$startTime, $endTime]);

		// Step 3: Mark Local Queue Calls
		$this->db->query("UPDATE qq_calls SET call_type = 'local_queue' WHERE (src IN (SELECT extension FROM qq_agents) OR src IN (SELECT extension FROM users) OR src IN (SELECT extension FROM qq_agents_archived)) AND dst IN (SELECT extension FROM queues_config) AND date >= ? AND date <= ?", [$startTime, $endTime]);

		// Step 4: Mark Future Codes
		$this->db->query("UPDATE qq_calls SET call_type = 'local_fcode' WHERE (src IN (SELECT extension FROM qq_agents) OR src IN (SELECT extension FROM users) OR src IN (SELECT extension FROM qq_agents_archived)) AND dst LIKE '%*%' AND date >= ? AND date <= ?", [$startTime, $endTime]);		

        // Merge and Delete duplicate Agents by lower ID
		if ($queue_log_fix_agent_duplicates =="yes") {
			// Step 1: Identify duplicates
			$query = $this->db->query("
				SELECT name, extension, GROUP_CONCAT(id ORDER BY id) as ids
				FROM qq_agents
				GROUP BY name, extension
				HAVING COUNT(*) > 1
			");

			foreach ($query->result() as $row) {
				$ids = explode(',', $row->ids);
				$maxIDValue = max($ids); //Max agent_id value
				$originalId = array_shift($ids); // Keep the lowest ID and remove it from the array
				
				// Step 2 update last call
				// Select src and uniqueid where agent_id is equal to $maxIDValue
				$selectLastCallQuery = $this->db->select('src, uniqueid')
					->from('qq_agent_last_call')
					->where('agent_id', $maxIDValue)
					->get();

				// Check if the query executed successfully and fetch the result row
				if ($selectLastCallQuery && $row = $selectLastCallQuery->row()) {
					// Update the rows where agent_id is equal to $originalId
					$updateQuery = "UPDATE qq_agent_last_call SET src = ?, uniqueid = ? WHERE agent_id = ?";
					$updateResult = $this->db->query($updateQuery, [$row->src, $row->uniqueid, $originalId]);
				} 			

				// Step 3: Update and delete duplicates
				foreach ($ids as $id) {
					// Update qq_events
					$this->db->query("UPDATE qq_events SET agent_id = $originalId WHERE agent_id = $id");
					
					// Update qq_calls
					$this->db->query("UPDATE qq_calls SET agent_id = $originalId WHERE agent_id = $id");

					// Delete the duplicate agent
					$this->db->delete('qq_agents', array('id' => $id));
					$this->db->delete('qq_agent_settings', array('agent_id' => $id));
					$this->db->delete('qq_queue_agents', array('agent_id' => $id));
					$this->db->delete('qq_agent_last_call', array('agent_id' => $id));
				}
			}

			// Step 3: Cleanup qq_queue_agents
			$this->db->query("DELETE FROM qq_queue_agents WHERE NOT EXISTS (SELECT 1 FROM qq_agents WHERE qq_agents.id = qq_queue_agents.agent_id)");
		}
		//*************************************************				
    }
    catch (Exception $ex) 
    {
        log_to_file('ERROR', $ex->getMessage());
    }
        log_to_file('NOTICE', 'Unlocking parser');
		parser_unlock();
    }


    public function collect_outgoing()
    {
        $collect = $this->Config_model->get_item('app_track_outgoing');
        $from    = QQ_TODAY_START;
        $mark_called_back = $this->Config_model->get_item('app_auto_mark_called_back');
		$queue_log_rollback_days = $this->Config_model->get_item('queue_log_rollback_days');
        $queue_log_rollback_with_deletion = $this->Config_model->get_item('queue_log_rollback_with_deletion');

        $this->db->update('qq_config', array('value' => 'no'), array('name' => 'queue_log_rollback'));
        $this->db->update('qq_config', array('value' => '1'), array('name' => 'queue_log_rollback_days'));
        $this->db->update('qq_config', array('value' => 'no'), array('name' => 'queue_log_rollback_with_deletion'));        

		if ($queue_log_rollback_with_deletion == "yes") {
			// Set $from to the date-time of $queue_log_rollback_days days ago
			$from = date('Y-m-d H:i:s', strtotime("-{$queue_log_rollback_days} days"));
		}        

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

					if ($queue_log_rollback_with_deletion == "yes") {
						// Set $from to the date-time of $queue_log_rollback_days days ago
						$mark_from = date('Y-m-d H:i:s', strtotime("-{$queue_log_rollback_days} days"));
					}
					else {
						// Set $from to the date-time of $mark_answered_elsewhere minutes ago
						$mark_from = date('Y-m-d H:i:s', (time() - $mark_called_back * 60));
					}

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
