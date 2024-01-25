<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Common helper functions for Quickqueues */


/**
 * Log to file
 *
 * @param string $level Log level
 * @param string $message Log message
 * @return bool true on successfull log write, false otherwise
 */
function log_to_file($level = false, $message = false) {
    if (!$level || !$message) {
        return false;
    }
    $ci =& get_instance();
    $log_file = $ci->Config_model->get_item('app_log_path');
    if (!$log_file) {
        return false;
    }
    if (file_put_contents($log_file, date('Y-m-d H:i:s')." - ".$level." - ".$message."\n", FILE_APPEND) === false) {
        return false;
    } else {
        return true;
    }
}


/**
 * Lock parser
 *
 * @param string $pid Process ID
 * @return bool
 */

function parser_lock($pid = false) {
    if (!$pid) {
        $pid = getmypid();
    }
    $ci =& get_instance();
    
    if (file_put_contents(QQ_PARSER_LOCK_PATH, $pid) === false)
    {
        return false;
    } else {
        return true;
    }
}

/**
 * Read parser lock
 *
 * @return mixed Process ID of lock owner, or false on error
 */
function parser_read_lock() {
    $ci =& get_instance();
    if (file_exists(QQ_PARSER_LOCK_PATH)) {
        return file_get_contents(QQ_PARSER_LOCK_PATH);
    }
    return false;

}


/**
 * Release parser lock
 *
 * @return bool
 */

 function parser_unlock() {
    $ci =& get_instance();
    $lockFilePath = QQ_PARSER_LOCK_PATH;

    if (file_exists($lockFilePath) && unlink($lockFilePath)) {
        return true;
    } else {
        return false;
    }
}

function parser_unlock_complex() {
    $ci =& get_instance();
    $lockFilePath = QQ_PARSER_LOCK_PATH;

    if (file_exists($lockFilePath)) {
        // Read the PID from the lock file
        $pid = file_get_contents($lockFilePath);

        if ($pid === false || !is_numeric($pid) || !posix_kill((int)$pid, 0)) {
            // Either failed to read PID, or PID is not valid, or process is not running
            // Proceed to unlock
            if (unlink($lockFilePath)) {
                return true; // Successfully unlocked
            }
        }
    else {
	echo "Failed to unlock or process: $pid is still running\n";
    return false; // Failed to unlock or process is still running
}
}
}

/**
 * Get available roles in application
 *
 * @return array List of roles
 */
function get_roles()
{
    return array('admin', 'manager', 'agent');
}

/**
 * Get available languages
 *
 * @return array List of languages
 */
function get_languages()
{
    return array('georgian', 'english');
}

/**
 * Get possible extension states
 *
 * @see https://www.voip-info.org/asterisk-manager-api-action-extensionstate/
 * @return array extension states
 */
function get_extension_states()
{
    $states['-1'] = "Extension not found";
    $states['0']  = "Idle";
    $states['1']  = "In Use";
    $states['2']  = "Busy";
    $states['4']  = "Unavailable";
    $states['8']  = "Ringing";
    $states['9']  = "In Use";
    $states['16'] = "On Hold";
    $states['99'] = "Unknown";
    return $states;
}


/**
 * Get possible extension states
 *
 * @see https://www.voip-info.org/asterisk-manager-api-action-extensionstate/
 * @return array extension states
 */
function get_extension_state_colors()
{
    $state_colors['0']  = "success";
    $state_colors['1']  = "primary";
    $state_colors['2']  = "danger";
    $state_colors['4']  = "secondary";
    $state_colors['8']  = "warning";
    $state_colors['9']  = "primary";
    $state_colors['16'] = "warning";
    $state_colors['99'] = "secondary";
    return $state_colors;
}


/**
 * Load CI views in order we want them to
 *
 * @param mixed $view String for single viw, or array of views
 * @param mixed $data Data passed to views
 * @param bool $new_assets Whether or not to load new, post 6.x.x release assets
 */
function load_views($view = false, $data = false, $new_assets = false)
{
    $ci =& get_instance();
    if ($new_assets) {
        $ci->load->view('common/header_v6', $data);
    } else {
        $ci->load->view('common/header', $data);
    }
    if ($view) {
        if (is_string($view)) {
            $ci->load->view($view);
        }
        if (is_array($view)) {
            foreach ($view as $v) {
                $ci->load->view($v);
            }
        }
    }
    if ($new_assets) {
        $ci->load->view('common/footer_v6', $data);
    } else {
        $ci->load->view('common/footer', $data);
    }
}


/**
 * Convert seconds to hh:mm:ss format
 *
 * @param string $seconds Seconds
 * @return string
 */
 function sec_to_time($sec = 0) {
    if ($sec == 0) {
        return "00:00:00";
    }
    $hours = floor($sec / 3600);
    $minutes = floor(($sec / 60) % 60);
    $seconds = $sec % 60;
    if ($hours < 10)    { $hours   = "0".$hours;   }
    if ($minutes < 10)  { $minutes = "0".$minutes; }
    if ($seconds < 10)  { $seconds = "0".$seconds; }

    return $hours.":".$minutes.":".$seconds;
 }


/**
 * Convert seconds to hh:mm:ss format
 *
 * @param string $seconds Seconds
 * @return string
 */
 function sec_to_min($sec = 0) {
    if ($sec == 0) {
        return "00:00";
    }
    $minutes = floor(($sec / 60) % 60);
    $seconds = $sec % 60;
    if ($minutes < 10) { $minutes = "0".$minutes; }
    if ($seconds < 10) { $seconds = "0".$seconds; }

    return $minutes.":".$seconds;
 }


 /**
  * Set session flashdata for notifications
  *
  * @param string $style Style of alert, should be bootstraps own 'danger', 'success' and so on...
  * @param string $body alert message body
  * @return bool True on success, false otherwise
  */
 function set_flash_notif($style = 'primary', $body = false)
 {
    if (!$body) {
        return false;
    }
    $ci =& get_instance();
    $ci->session->set_flashdata('msg_style', $style);
    $ci->session->set_flashdata('msg_body', $body);
    return true;
 }


/**
 * Check whether given string is JSON
 *
 * @see https://stackoverflow.com/a/6041773
 *
 * @param string String to check
 * @return bool
 */
function is_json($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}


/**
 * Get application version
 *
 * @return string Application version
 */
/*function get_qq_version()
{
    $ci =& get_instance();
    if (file_exists(APPPATH."/VERSION")) {
        return file_get_contents(APPPATH."/VERSION");
    }
    return " N/A";
}*/


/**
 * Get application version
 *
 * @return string Application version
 */
function qq_get_version()
{
    $ci =& get_instance();
    if (file_exists(APPPATH."/VERSION")) {
        return file_get_contents(APPPATH."/VERSION");
    }
    return " N/A";
}


/**
 * Generate random string
 *
 * @param int $length Length of random string, defaults to 16
 * @return string Random string
 */
function qq_generate_random_string($length = 16)
{
    return bin2hex(openssl_random_pseudo_bytes($length));
}


/**
 * Get array of styles for called_back calls
 *
 * @return array List of corresponding styles for called_back statuses
 */
function qq_get_called_back_styles()
{
    return array(
        'yes' => 'text-success',
        'no' => 'text-danger',
        'nop' => 'text-warning',
        'nah' => 'text-info'
    );
}


/**
 * Get recording file path for specific call
 *
 * @param obj Call object
 * @return Empty string on error, or full path of recording file
 */
function qq_get_call_recording_path($call = false)
{
    $path = '';
    if (!$call) {
        return $path;
    }
    $ci =& get_instance();

    $hot_path = $ci->Config_model->get_item('ast_monitor_path');
    $cold_path = '/var/monitor_archive';

    /**
     * Fix #116
     * Since we update timestamp of the call with each event, last timestamp
     * is when the call ended. This results in some situations were calls starts on
     * one day, and ends on next one, and calculating recording path results
     * in incorrect day/month/year, since asterisk stores recordings based on
     * when they were created, not ended.
     * To avoid that, we need to get timestamp from uniqueid, since it is not updated.
     */

    $t = $call->uniqueid;
    $t = explode(".", $t);
    $t = $t[0];

    $year   = date('Y',$t);
    $month  = date('m',$t);
    $day    = date('d',$t);

    $path = $hot_path."/".$year."/".$month."/".$day."/".$call->recording_file;
    // Try searching in backup
    if (!is_file($path)) {
        $path = $cold_path."/".$year."/".$month."/".$day."/".$call->recording_file;
        if (!is_file($path)) {
            $path = $hot_path."/".$year."/".$month."/".$day."/".$call->recording_file;
        }
    }
    return $path;
}


/**
 * Get available call statuses
 *
 * @return array List containing valid call statuses
 */
function qq_get_call_statuses()
{
    return array('open', 'ongoing', 'closed');
}


/**
 * Get available call statuses
 *
 * @return array List containing valid call statuses
 */
function qq_get_palitra_call_statuses()
{
    return array('call_status_finished_success', 'call_status_on_hold', 'call_status_finished_fail', 'call_status_finished_ok');
}


/**
 * Get available call statuses
 *
 * @return array List containing valid call statuses
 */
function qq_get_ticket_statuses()
{
    return array('open', 'ongoing', 'closed');
}


/**
 * Get available call priorities
 *
 * @return array List containing valid call priorities
 */
function qq_get_call_priorities()
{
    return array('low', 'normal', 'high', 'urgent');
}


/**
 * Generate v4 UUID
 *
 * @param void
 * @return string v4 UUID
 */
function qq_gen_uuid()
{
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}


/**
 * Die with <pre> and specific array to be printed
 *
 * @param array $data
 * @return void
 */
function qq_die($data = array())
{
    echo "<pre>";
    die(print_r($data));
}
