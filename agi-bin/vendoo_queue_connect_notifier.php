#!/usr/bin/php -q

<?php


include "phpagi.php";
$agi = new AGI;


$agi->verbose("Invoked vendoo_queue_connect_notifier.php");


$params['src']          = $agi->request['agi_callerid'];
$params['uniqueid']     = $agi->request['agi_uniqueid'];
$params['extension']    = $agi->get_variable("MEMBERNAME");
$params['extension']    = $params['extension']['data'];

$data_string = json_encode($params);

$ch = curl_init('http://localhost/callcenter/index.php/api/custom/update_last_call/'.$params['extension']."/".$params['uniqueid']."/".$params['src']);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);



