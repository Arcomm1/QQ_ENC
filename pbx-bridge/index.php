<?php


/* index.php - FreePBX (R) API for QuickQueues */


$bootstrap_settings = array();
$bootstrap_settings['freepbx_auth'] = false; // Allow FreePBX bootstrap to be used from web
require '/etc/freepbx.conf';
require 'lib/flight/Flight.php';


/**
 * Generate a response
 *
 * Echoes JSON response
 *
 * @param string $status Response status
 * @param string $mssage Response status description
 * @param mixed $data Response data, mostly there are arrays
 * @return void
 */
function respond($status = 'FAIL', $message = 'Invalid request', $data = false)
{
    $response           = new stdClass();
    $response->status   = $status;
    $response->message  = $message;
    $response->data     = $data;
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit();
}


header('Content-Type: application/json'); // Make sure every response is JSON


/**
 * Index route. Just let other know we are API.
 */
Flight::route('/', function () {
    echo "Welcome to pbx-bridge API for QuickQueues. You shoud not be here.";
});


/**
 * *****************************************************************************
 * Device management
 */

/**
 * Get device information.
 *
 * If Device ID is provided, show specific device details, otherwise return all devices.
 *
 * @method GET
 * @param int $id Device ID
 */
Flight::route('/devices/get(/@id)', function ($id) {

    if (!$id) {
        $devices = array();
        $f_devices = core_devices_list();
        if (is_array($f_devices)) {
            foreach (core_devices_list() as $d) {
                $devices[$d['id']] = $d['description'];
            }
        }
        respond('OK', 'Device list will follow', $devices);
    }
    $device = core_devices_get($id);
    if (!$device) {
        respond('FAIL', 'Device not found');
    }
    respond('OK', 'Device data will follow', $device);
});



/**
 * *****************************************************************************
 * Queue management
 */

/**
 * Get queue information.
 *
 * If Queue ID is provided, show specific queue details, otherwise return all queues.
 *
 * @method GET
 * @param int $id Queue ID
 */
Flight::route('/queues/get(/@id)', function ($id) {

    if (!$id) {
        $queues = array();
        foreach (queues_list() as $q) {
            $queues[$q[0]] = $q[1];
        }
        respond('OK', 'Queue list will follow', $queues);
    }

    $queue = queues_get($id);

    if (empty($queue)) {
        respond('FAIL', 'Queue not found');
    }

    respond('OK', 'Queue details will follow', $queue);

});


Flight::start();
