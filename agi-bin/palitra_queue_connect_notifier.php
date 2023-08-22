#!/usr/bin/php -q

<?php


include "phpagi.php";
$agi = new AGI;


$agi->verbose("Invoked palitra_queue_connect_notifier.php");


$params['src']          = $agi->request['agi_callerid'];
$params['uniqueid']     = $agi->request['agi_uniqueid'];
$params['extension']    = $agi->get_variable("MEMBERINTERFACE");
$params['extension']    = $params['extension']['data'];
$params['extension']    = explode('@', $params['extension']);
$params['extension']    = explode('/', $params['extension'][0]);
$params['extension']    = $params['extension'][1];
$agi->verbose("Got variable ".$params['extension']);

$data_string = json_encode($params);

$ch = curl_init('http://192.168.126.120/callcenter/index.php/api/palitra/misc/update_last_call/'.$params['extension']."/".$params['uniqueid']."/".$params['src']);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);



