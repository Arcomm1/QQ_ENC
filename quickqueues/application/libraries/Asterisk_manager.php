<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Asterisk_manager.php - Asterisk Manager Interface class for Quickqueues */


class Asterisk_manager
{

    /**
     * CodeIgniter instance
     */

    protected $ci;

    /**
     * Asterisk manager interface hostname
     */
    protected $ast_host;

    /**
     * Asterisk manager interface port
     */
    protected $ast_port;

    /**
     * Asterisk manager interface username
     */
    protected $ast_username;

    /**
     * Asterisk manager interface password
     * @var [type]
     */
    protected $ast_password;

    /**
     * Socket errot
     */
    public $errno;

    /**
     * Socket error description
     */
    public $errstr;

    /**
     * Class status
     */
    public $status;



    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ast_host = defined('HOST') == true ? HOST : $this->ci->Config_model->get_item('ast_ami_host');
        $this->ast_port = $this->ci->Config_model->get_item('ast_ami_port');
        $this->ast_user = $this->ci->Config_model->get_item('ast_ami_user');
        $this->ast_pass = $this->ci->Config_model->get_item('ast_ami_password');
    }


    /**
     * Create socket to AMI
     *
     * @return mixed socket or false on error
     */
    protected function create_socket()
    {
        $socket = fsockopen($this->ast_host, $this->ast_port, $this->errno, $this->errstr, 1);
        if ($socket == false) {
            $this->status = 'ERR_NO_SOCKET';
            return false;
        }
        $this->status = 'OK_SOCKET';
        return $socket;
    }


    /**
     * Close the socket
     *
     * @return bool Based on success
     */
    protected function close_socket($socket = false) {
        if (!$socket) {
            $this->status = 'ERR_NO_SOCKET';
            return false;
        }
        fclose($socket);
        $this->status = 'OK_SOCKET';
        return true;
    }


    /**
     * Authenticate over AMI
     *
     * @param obj $socket Socket
     * @return bool True of false based on success
     */
    protected function authenticate($socket = false)
    {
        if (!$socket) {
            $this->status = 'ERR_NO_SOCKET';
            return false;
        }
        $request  = "Action: Login\r\n";
        $request .= "Username: ".$this->ast_user."\r\n";
        $request .= "Secret: ".$this->ast_pass."\r\n";
        $request .= "Events: no\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            $this->status = 'ERR_SOCKET_WRITE';
            return false;
        }
        $line = "";
        $response_lines = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            $params = explode(": ",$l);
            if ($params[0] == 'Response') {
                if ($params[1] == 'Success') {
                    $this->status = 'OK_AUTH';
                    return true;
                } else {
                    $this->status = 'ERR_AUTH_NO_AUTH';
                    return false;
                }
            }
        }
        $this->close_socket($socket);

    }


    /**
     * Get queue realtime status
     *
     * Executes QueueStatus Asterisk Manager Interface command
     *
     * @param string $queue Queue name/extension
     * @return mixed Array containing queue information, false on error
     */
    public function queue_status($queue = false)
    {
        $socket = $this->create_socket();

        if (!$socket) {
            $this->status = 'ERR_NO_SOCKET';
            return false;
        }
        if (!$queue) {
            $this->status = 'ERR_NO_QUEUE';
            return false;
        }

        if (!$this->authenticate($socket)) {
            $this->status = 'ERR_QUEUESTATUS_NO_AUTH';
            return false;
        }

        $request  = "Action: QueueStatus\r\n";
        $request .= "Queue: $queue\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            $this->status = 'ERR_SOCKET_NO_WRITE';
            return FALSE;
        }

        $line = "";
        $response_lines  = array();
        $response_chunks = array();
        $queue = array();
        $queue['agents'] = array();
        $queue['data'] = array();
        $queue['callers'] = array();


        // Read until event end
        while ($line != "Event: QueueStatusComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // Put separate event into respective array
        foreach ($response_lines as $l) {
            if ($l == false) {
                $response_chunks[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(": ",$l);
            if (count($params) == 2) {
                $chunk[$params[0]] = rtrim($params[1]);

            }
        }

        // Organize data
        foreach ($response_chunks as $c) {
            if (array_key_exists('Event', $c)) {
                if (trim($c['Event']) == 'QueueMember') {
                    $queue['agents'][] = $c;
                }
                if (trim($c['Event']) == 'QueueParams') {
                    $queue['data'] = $c;
                }
                if (trim($c['Event']) == 'QueueEntry') {
                    $queue['callers'][] = $c;
                }
            }
        }
        $this->status = 'OK_QUEUESTATUS';
        return $queue;
    }


    /**
     * Get active calls for specific queue
     *
     * @param string $agentnum Agent number
     * @return mixed Array of calls or false on error
     */
    function get_agent_call($agentnum = FALSE) {

        $socket = $this->create_socket();
        if (!$socket) {
            return FALSE;
        }

        if (!$this->authenticate($socket)) {
            return FALSE;
        }

        if (!$agentnum) {
            return FALSE;
        }

        $calls = array();
        // first get all channels
        $channels = $this->get_status();


        foreach ($channels as $c) {
            // if this is channel array
            if (array_key_exists('Channel', $c)) {
                // if channel matches requested agent extension
                if (preg_match("/$agentnum/", $c['Channel'])) {
                    // we only need channel arrays that contain 'Seconds' key
                    if (array_key_exists('Seconds', $c)) {
                        // in some cases this WILL result in multiple channel arrays.
                        // if queue member will transfer call without Asterisk feature code,
                        // say through physical phone transfer button, we will receive multiple channels
                        // if member receives call and previous transferred calls is not ended yet.
                        // this is up to controller logic to find actual active call if multiple channels are returned
                        $calls[] = $c;
                    }
                }
            }
        }
        // array_reverse($calls);
        return $calls;
    }


    /**
     * Get list of active channels - Status
     *
     * @return bool|array FALSE on error or array of channels
     */
    function get_status() {

        $socket = $this->create_socket();
        if (!$socket) {
            return FALSE;
        }

        if (!$this->authenticate($socket)) {
            return FALSE;
        }

        $request  = "Action: Status\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return FALSE;
        }

        $line               = "";
        $response_lines     = array();
        $response_chunks    = array();
        $channels           = array();

        // parse till event end
        while ($line != "Event: StatusComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // put each event into separate array
        foreach ($response_lines as $l) {
            if ($l == FALSE) {
                $channels[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(": ",$l);
            if (count($params) > 1) {
                $chunk[$params[0]] = rtrim($params[1]);
            }
        }

        return $channels;

    }


    /**
     * Get status/hint of specific extension - ExtensionState
     *
     * @param string $exten Extension
     * @param string $context Context
     * @return bool|array Response data on success, false on error
     */
    function get_agent_status($exten = false) {

        if (!$exten) {
            return false;
        }

        $status_arr[0] ="Idle";
        $status_arr[1] ="In Use";
        $status_arr[2] ="Busy";
        $status_arr[4] ="Unavailable";
        $status_arr[8] ="Ringing";
        $status_arr[16] ="On Hold";


        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: ExtensionState\r\n";
        $request .= "Context: ".QQ_AGENT_CONTEXT."\r\n";
        $request .= "Exten: ".$exten."\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line               = "";
        $response_lines     = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            $params = explode(": ",$l);
            if (sizeof($params) >= 2) {
                $extension_state[$params[0]] = $params[1];
            }
            // If for some reason we did not get Status, Set status 99
            if (is_array($extension_state)) {
                if (!array_key_exists('Status', $extension_state)) {
                    unset($extension_state);
                    $extension_state['Status'] = 99;
                }
            } else {
                $extension_state['Status'] = 99;
            }
        }
        $states = get_extension_states();
        $extension_state['StatusMsg'] = $states[$extension_state['Status']];

        $peer_status = $this->sip_show_peer($exten);

        if (array_key_exists('Status', $peer_status)) {
            if ($peer_status['Status'] == 'UNKNOWN') {
                $extension_state['Status'] = 4;
            }
        }

        $pause_status = $this->database_get('QQPAUSE/'.$exten);
        if ($pause_status) {
            $extension_state['PauseStatus'] = $pause_status[0][1];
            if ($pause_status[0][1] == 1) {
                if ($extension_state['Status'] != 4) {
                    $extension_state['Status'] = 2;
                }
            }
        } else {
            $extension_state['PauseStatus'] = 0;
        }

        return $extension_state;
    }


    /**
     * Execute QueueLog command
     *
     * @param string $queue Queue
     * @param string $agent Agent
     * @param string $event QueueLog event
     * @param string $uniqueid Uniqueid
     * @param string $data '|'-separated string of event data
     * @return bool|array Response data on success, false on error
     */
    function queue_log($queue = false, $agent = false, $event = false, $uniqueid = false, $data = false) {

        if (!$event) {
            return false;
        }

        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: QueueLog\r\n";
        if ($queue) {
            $request .= "Queue: ".$queue."\r\n";
        } else {
            $request .= "Queue: NONE\r\n";
        }
        if ($agent) {
            $request .= "Agent: ".$agent."\r\n";
        }
        if ($uniqueid) {
            $request .= "Uniqueid: ".$uniqueid."\r\n";
        }
        $request .= "Event: ".$event."\r\n";
        $request .= "Message: ".$data."\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line = "";
        $response_lines = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            $params = explode(": ",$l);
            if ($params[0] == 'Response') {
                if ($params[1] == 'Success') {
                    $this->status = 'EVENT_SUCCESS';
                    $this->close_socket($socket);
                    return true;
                } else {
                    $this->status = "EVENT_FAIL";
                    $this->close_socket($socket);
                    return true;

                }
            }
        }
    }


    /**
     * Create AstDB database entry
     *
     * @param int $family AstDB family
     * @param int $key AstDB key
     * @param int $value AstDB value
     * @return bool True on success, false otherwise
     */
    public function database_put($family = false, $key = false, $value = false)
    {
        if (!$family || !$key || !$value) {
            return false;
        }

        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: Command\r\n";
        $request .= "Command: database put $family $key $value\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line = "";
        $response_lines = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            if ($l == 'Updated database successfully') {
                $this->close_socket($socket);
                $this->status = 'EVENT_SUCCESS';
                return true;
            }
        }
        $this->close_socket($socket);
        $this->status = 'EVENT_FAIL';
        return true;
    }


    /**
     * Delete AstDB database entry
     *
     * @param int $family AstDB family
     * @param int $key AstDB key
     * @return bool True on success, false otherwise
     */
    public function database_del($family = false, $key = false)
    {
        if (!$family || !$key) {
            return false;
        }

        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: Command\r\n";
        $request .= "Command: database del $family $key\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line = "";
        $response_lines = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            if ($l == 'Database entry removed.') {
                $this->close_socket($socket);
                $this->status = 'EVENT_SUCCESS';
                return true;
            }
        }
        $this->close_socket($socket);
        $this->status = 'EVENT_FAIL';
        return true;
    }


    /**
     * Get AstDB database entries
     *
     * @param int $family AstDB family
     * @return bool True on success, false otherwise
     */
    public function database_get($family = false)
    {
        if (!$family) {
            return false;
        }

        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: Command\r\n";
        $request .= "Command: database show $family\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line = "";
        $response_lines = array();

        while (!strpos($line, 'END COMMAND')) {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        $entries = array();
        foreach ($response_lines as $l) {
            if (strpos($l, 'Response') !== false) {
                continue;
            }
            if (strpos($l, 'Privilege') !== false) {
                continue;
            }
            if (strpos($l, 'results') !== false) {
                continue;
            }
            if (strpos($l, 'END COMMAND') !== false) {
                continue;
            }
            $e = explode(':', $l);
            $e[0] = str_replace(' ', '', $e[0]);
            $e[1] = str_replace(' ', '', $e[1]);
            $entries[] = $e;
        }
        $this->close_socket($socket);
        $this->status = 'EVENT_SUCCESS';
        return($entries);
        return true;
    }


    /**
     * Emulate FreePBX app-dnd-on dialplan. This should be used to set Agent DND from web
     *
     * @param int Agent extension
     * @return bool True on success, false otherwise
     */
    public function emulate_app_dnd_on($extension = false)
    {
        if (!$extension) {
            return false;
        }

        $this->database_put('DND', $extension, 'YES');

    }


    /**
     * Emulate FreePBX app-dnd-off dialplan. This should be used to set Agent DND off from web
     *
     * @param int Agent extension
     * @return bool True on success, false otherwise
     */
    public function emulate_app_dnd_off($extension = false)
    {
        if (!$extension) {
            return false;
        }

        $this->database_del('DND', $extension);
    }

	function get_all($queue = false,$extens)
	{
		$obj = array();
		$socket = $this->create_socket();

        if (!$socket) {
            $this->status = 'ERR_NO_SOCKET';
            return false;
        }

        if (!$this->authenticate($socket)) {
            $this->status = 'ERR_QUEUESTATUS_NO_AUTH';
            return false;
        }
		
		if (is_array($queue)) {
			$queues = [];
			foreach ($queue as $q) {
				$queueStatus = $this->queue_status_content($socket,$q->name);
                if (isset($queueStatus['data'])) 
                {
                    $queues[] = $queueStatus;
                }				
			}
			$obj['queue'] = $queues;
		} elseif (is_string($queue)) {
			// Assuming $queue is a single queue name string.
			$queueStatus = $this->queue_status_content($socket, $queue);
			if (isset($queueStatus['data'])) {
				$obj['queue'] = [$queueStatus]; // Wrap in array for consistency.
			} else {
				$obj['queue'] = $this->queue_status_content($socket,false); // Get all queues as 1 array, currently not used
			}
		}

		$obj['status'] = $this->get_status_content($socket);
		$obj['extensions'] = $this->get_extension_state_list_content($socket);
		$obj['sip_status'] = $this->sip_show_peer_content($socket,$extens);	
		
        $this->close_socket($socket);
		
		return $obj;
	}

    	public function sip_show_peer_content($socket, $extens)
	{
		$peers = [];
		if (!$socket) {
			return false;
		}

		foreach ($extens as $exten) {
			$actionId = uniqid(); // Generate a unique ActionID for each request
			$request  = "Action: SipShowPeer\r\n";
			$request .= "ActionID: $actionId\r\n"; // Include the ActionID in the request
			$request .= "Peer: $exten\r\n\r\n";

			if (!fwrite($socket, $request)) {
				return false;
			}

			$peer_info = []; // Initialize peer_info array for each extension
			$isRelevantResponse = false; // Flag to track if the response is for the current request

			while (!feof($socket)) {
				$line = fgets($socket, 4096);
				if (strpos($line, "ActionID: $actionId") !== false) {
					$isRelevantResponse = true; // Start capturing the response
				}
				if ($isRelevantResponse) {
					$response_lines[] = rtrim($line);
					if ($line == "\r\n") {
						break; // End of the current response
					}
				}
			}

			// Process the response lines relevant to the current request
			foreach ($response_lines as $l) {
				if (empty($l)) {
					continue;
				}
				$params = explode(": ", $l, 2);
				if (count($params) == 2) {
					$peer_info[$params[0]] = $params[1];
				}
			}

			$peers[$exten] = $peer_info; // Assign peer info to the extension key
		}

		return $peers;
	}
	
    function get_extension_state_list_content($socket) {

        $request  = "Action: ExtensionStateList\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return FALSE;
        }

        $line               = "";
        $response_lines     = array();
        $response_chunks    = array();
        $channels           = array();

        // parse till event end
        while ($line != "Event: ExtensionStateListComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // put each event into separate array
        foreach ($response_lines as $l) {
            if ($l == FALSE) {
                $channels[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(":",$l);
            if (count($params) > 1) {
                $chunk[$params[0]] = preg_replace('/\s/', '', $params[1]);
            }
        }

        return $channels;
    }


    function get_status_content($socket) {

        $request  = "Action: Status\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return FALSE;
        }

        $line               = "";
        $response_lines     = array();
        $response_chunks    = array();
        $channels           = array();

        // parse till event end
        while ($line != "Event: StatusComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // put each event into separate array
        foreach ($response_lines as $l) {
            if ($l == FALSE) {
                $channels[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(": ",$l);
            if (count($params) > 1) {
                $chunk[$params[0]] = rtrim($params[1]);
            }
        }

        return $channels;

    }
	
	function queue_status_content($socket,$queue = false)
	{
		/// queue status start 
		$request  = "Action: QueueStatus\r\n";
		if($queue)
		{
			$request .= "Queue: $queue\r\n";
		}
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            $this->status = 'ERR_SOCKET_NO_WRITE';
            return FALSE;
        }

        $line = "";
        $response_lines  = array();
        $response_chunks = array();
        $queue = array();
        $queue['agents'] = array();
        $queue['data'] = array();
        $queue['callers'] = array();


        // Read until event end
        while ($line != "Event: QueueStatusComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // Put separate event into respective array
        foreach ($response_lines as $l) {
            if ($l == false) {
                $response_chunks[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(": ",$l);
            if (count($params) == 2) {
                $chunk[$params[0]] = rtrim($params[1]);

            }
        }

        // Organize data
        foreach ($response_chunks as $c) {
            if (array_key_exists('Event', $c)) {
                if (trim($c['Event']) == 'QueueMember') {
                    $queue['agents'][] = $c;
                }
                if (trim($c['Event']) == 'QueueParams') {
                    $queue['data'] = $c;
                }
                if (trim($c['Event']) == 'QueueEntry') {
                    $queue['callers'][] = $c;
                }
            }
        }
        $this->status = 'OK_QUEUESTATUS';
		
		return $queue;
	}

    /**
     * Get list of extension states - Status
     *
     * @return bool|array FALSE on error or array of channels
     */
    function get_extension_state_list() {

        $socket = $this->create_socket();
        if (!$socket) {
            return FALSE;
        }

        if (!$this->authenticate($socket)) {
            return FALSE;
        }

        $request  = "Action: ExtensionStateList\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return FALSE;
        }

        $line               = "";
        $response_lines     = array();
        $response_chunks    = array();
        $channels           = array();

        // parse till event end
        while ($line != "Event: ExtensionStateListComplete\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }

        // put each event into separate array
        foreach ($response_lines as $l) {
            if ($l == FALSE) {
                $channels[] = $chunk;
                $chunk = array();
                continue;
            }
            $params = explode(":",$l);
            if (count($params) > 1) {
                $chunk[$params[0]] = preg_replace('/\s/', '', $params[1]);
            }
        }

        return $channels;

    }


    /**
     * Get SIP peer details
     *
     * @param int $family AstDB family
     * @return bool|array Response data on success, false on error
     */
    public function sip_show_peer($exten = false)
    {
        if (!$exten) {
            return false;
        }

        $socket = $this->create_socket();
        if (!$socket) {
            return false;
        }

        if (!$this->authenticate($socket)) {
            return false;
        }

        $request  = "Action: SipShowPeer\r\n";
        $request .= "Peer: ".$exten."\r\n";
        $request .= "\r\n";

        if (!fwrite($socket, $request)) {
            return false;
        }

        $line               = "";
        $response_lines     = array();

        while ($line != "\r\n") {
            $line = fgets($socket,128);
            $response_lines[]= rtrim($line);
        }
        foreach ($response_lines as $l) {
            if ($l == false) {
                continue;
            }
            $params = explode(": ",$l);
            if (sizeof($params) >= 2) {
                $peer_info[$params[0]] = $params[1];
            }
        }

        return $peer_info;
    }


}
