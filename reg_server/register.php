<?php
  require_once('includes/dbconn.php');

  if(isset($_POST['agents'])) $agents=$_POST['agents'];
  if(isset($_POST['queues'])) $queues=$_POST['queues'];
  if(isset($_POST['users'])) $users=$_POST['users'];
  if(isset($_POST['qq_version'])) $qq_version=$_POST['qq_version'];

  $client_ip = $_SERVER['REMOTE_ADDR'];

  $sql = "INSERT INTO bond_tbl (agents, queues, users, qq_version, client_ip) VALUES (:agents, :queues, :users, :qq_version, :client_ip)";
  $statement = $conn->prepare($sql);

  $statement->bindParam(':agents',$agents);
  $statement->bindParam(':queues',$queues);
  $statement->bindParam(':users',$users);
  $statement->bindParam(':qq_version',$qq_version);
  $statement->bindParam(':client_ip',$client_ip);

  $status=$statement->execute();

  if($status){
    echo 'OK';
  }

?>
