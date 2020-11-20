<?php
require_once("script.admin.php");
/*
* Created 17.11.2020
* Mail und ID = Unique
*
*/
if(isset($_POST['nsAmount']) && isset($_POST['nsMail']) && isset($_POST['nsTime'])&& isset($_POST['nsID'])){
  $id = trim($_POST['nsID']); // Erhalte ID von POST (trim)
  $amount = trim($_POST['nsAmount']); // Erhalte Anzahl von POST (trim)
  $mail = trim($_POST['nsMail']); // Erhalte neue Mail von POST (trim)
  $time = trim($_POST['nsTime']); // Erhalte neue Zeit von POST (trim)
  // Wenn Alle Variablen gesetzt sind
  if(strlen($id) >= 1 && strlen($amount) >= 1 && strlen($mail) >= 3 && strlen($time) >= 3){
    $overview = new Overview();
    $update = $overview->updateNoShow($id,$amount,$mail,$time);
    if($update){
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

if(isset($_POST['nsEdit'])){
  $edit = trim($_POST['nsEdit']);
  if(strlen($edit) >= 1){
    $overview = new Overview();
    $result;
    if($edit != 'Create'){
      $result = $overview->deleteNoShow($edit);
    } else {
      $result = $overview->createNoShow();
    }

    if($result){ echo "1"; return;}
    echo "0"; return;
  }
}

if(isset($_POST['rsLoad'])){
  $rID = $_POST['rsLoad'];
  $rData = getReservierungData($rID);

  if($rData != false){
    echo json_encode($rData);
    return;
  }
  echo "0";
  return;
}

if(isset($_POST['acpButton']) && isset($_POST['acpReserveID']) && isset($_POST['acpDate'])){
  $func = false; $state = $_POST['acpButton']; $rID = $_POST['acpReserveID']; $date = $_POST['acpDate'];
  switch ($state) {
    case '1':
      /* Zeit wird nicht aktualisiert sondern nur State auf eingetroffen gesetzt */
      $func = true;
      break;
    case '2':
      /* Setze ReserveEnd auf jetzige Uhrzeit */
      if(updateReserveEnd($rID)){ $func = true; }
      break;
    case '3':
      /*Hierfür muss checkTime usw. angepasst werden*/
      $func = true;
      break;
    case '4':
      /* checkTime usw. anpasen und NoShow eintragen*/
      if(addNoShow($rID, $date)){ $func = true; }
      break;
  }
  if($func == true){ if(updateReserveState($state,$rID)) { echo "1"; return;} }
  echo "0";
  return;
}




function getReservierungData($id) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "SELECT rreserve.tableID, rreserve.clientID, reserveDate, reserveStart, reserveEnd, reserveDuration, reserveAmount, rClient.clientName, rClient.clientVorname, rClient.clientMail, rClient.clientAdresse, rClient.clientTNR, rTable.tableType,rTable.tableActive FROM rReserve INNER JOIN rClient ON rReserve.reserveID = rClient.reserveID INNER JOIN rTable ON rreserve.tableID = rtable.tableID WHERE rReserve.reserveID = '$id'";
  $query = $con->query($statement) or die();

  if($query){
    $data = array(); $r = 0;
    foreach ($query as $key) {
      $data[$r]['tableID'] = $key['tableID'];
      $data[$r]['clientID'] = $key['clientID'];
      $data[$r]['reserveDate'] = $key['reserveDate'];
      $data[$r]['reserveStart'] = $key['reserveStart'];
      $data[$r]['reserveEnd'] = $key['reserveEnd'];
      $data[$r]['reserveDuration'] = $key['reserveDuration'];
      $data[$r]['reserveAmount'] = $key['reserveAmount'];
      $data[$r]['tableType'] = $key['tableType'];
      $data[$r]['tableActive'] = $key['tableActive'];
      $data[$r]['clientName'] = $key['clientName'];
      $data[$r]['clientVorname'] = $key['clientVorname'];
      $data[$r]['clientMail'] = $key['clientMail'];
      $data[$r]['clientAdresse'] = $key['clientAdresse'];
      $data[$r]['clientTNR'] = $key['clientTNR'];
      $r++;
    }
    return $data;
  }
  return false;
}

function updateReserveState($state, $rID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rReserve SET reserveState = '$state' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveEnd($rID) {
  $con = connect();
  $datetime = echoTime();
  $statement = "UPDATE rReserve SET reserveEnd = '$datetime' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function addNoShow($rID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $date = date("Y-m-d G:i:s");
  $statement = "SELECT rClient.clientID, clientMail FROM rReserve INNER JOIN rClient ON rReserve.clientID = rClient.clientID WHERE rReserve.reserveID = '$rID'";
  $query = $con -> query($statement);
  foreach ($query as $key) {
    if($key['clientID'] && $key['clientMail']){
      $clientID = $key['clientID'];
      $clientMail = $key['clientMail'];
      $statement2 = "INSERT INTO rNoshow (nsID,nsMail,nsAmount,nsDate) VALUES ('null','$clientMail','1','$date') ON DUPLICATE KEY UPDATE nsAmount = nsAmount +1";
      $query2 = $con -> query($statement2);
      if($query2 === TRUE) { return true; }
      return false;
    }
  }
  return false;
}

?>
