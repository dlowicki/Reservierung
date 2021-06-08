<?php
function connectDatabase() {
  $conn = new mysqli('localhost', 'shop', '123456','reservierung'); // Create connection
  if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); return false; }
  return $conn;
}

if(updateClients()){
  echo '['.echoDateTime().'] Client-Daten wurden erfolgreich entfernt.';
} else {
  echo '['.echoDateTime().'] Client-Daten konnten nicht entfernt werden.';
}

function updateClients(){
  $con = connectDatabase();
  $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-14, date("Y")));
  $query = $con->query("DELETE FROM rclient WHERE clientDate < '$date 00:00:00'");
  if($query===TRUE){ return true; } return false;
}

// Update Analyse

function echoDateTime() { return date("Y-m-d G:i:s"); }
?>
