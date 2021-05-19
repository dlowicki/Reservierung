<?php
require_once("script.admin.php");

if(!isset($_COOKIE['rSession'])){ return; }
if(isAdmin() != true){ return; }

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
      if(updateReserveEnd($rID,date("G:i:s"))){ $func = true; }
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

if(isset($_POST['submitClients'])){
  $data = $_POST['submitClients'];
  $error = false;
  foreach ($data as $key) {
    if(strlen($key[2]) >= 2){ // Wenn Name Länge >= 2
      $updateClient = updateClient($key[0],$key[1],$key[3],$key[2],$key[4],$key[5],$key[6],date("Y-m-d"),uniqid());
      if($updateClient == false){ $error = true; }
    }
  }
  // Bei der Übertragung der Client gab es einen Fehler
  if($error == true){ echo "0"; } else { echo "1"; }
  return;
}

// Delete Client
if(isset($_POST['deleteClient'])){if(deleteClient($_POST['deleteClient'])){echo"1";return;} else {echo"0";return;}}

if(isset($_POST['createReserveButton']) && isset($_POST['table']) && isset($_POST['date'])){
  $tID = $_POST['table']; $date = $_POST['date']; $clientID = uniqid();
  $cnrID = createNewReserve($tID,$clientID,$date);
  if($cnrID != false){
    if(updateClient($clientID,$cnrID,'Vorname','Nachname','E-Mail','Adresse','Telefon',date("Y-m-d"),uniqid())){
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

if(isset($_POST['updateReserveStart']) && isset($_POST['startTime']) && isset($_POST['endTime'])){
  $rID = $_POST['updateReserveStart']; $startTime = $_POST['startTime']; $endTime = $_POST['endTime'];
  if(updateReserveStart($rID,$startTime, $endTime)){
    echo "1"; return;
  }
  echo "0"; return;
}
if(isset($_POST['updateReserveDuration']) && isset($_POST['duration'])&& isset($_POST['endTime'])){
  $rID = $_POST['updateReserveDuration']; $duration = $_POST['duration']; $endTime = $_POST['endTime'];
  if(updateReserveDuration($rID,$duration,$endTime)){
    echo "1"; return;
  }
  echo "0"; return;
}
if(isset($_POST['updateReserveAmount']) && isset($_POST['amount'])){
  $rID = $_POST['updateReserveAmount']; $amount = $_POST['amount'];
  if(updateReserveAmount($rID,$amount)){
    echo "1"; return;
  }
  echo "0"; return;
}


function isAdmin(){
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->prepare("SELECT userCookie FROM rUser WHERE userCookie = ?");
  $query->bind_param('s',$_COOKIE['rSession']);
  $query->execute();

  $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);

  if(sizeof($row) >=1 ){
    if($row['userCookie'] == $_COOKIE['rSession']) {
      return true;
    }
  }
  return false;
}






function getReservierungData($id) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "SELECT rreserve.tableID, rreserve.clientID, reserveDate, reserveStart, reserveEnd, reserveDuration, reserveAmount, rClient.clientID, rClient.clientName, rClient.clientVorname, rClient.clientMail, rClient.clientTNR, rTable.tableType,rTable.tableActive FROM rReserve INNER JOIN rClient ON rReserve.reserveID = rClient.reserveID INNER JOIN rTable ON rreserve.tableID = rtable.tableID WHERE rReserve.reserveID = '$id'";
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
      // Client Data
      $data[$r]['clientID'] = $key['clientID'];
      $data[$r]['clientName'] = $key['clientName'];
      $data[$r]['clientVorname'] = $key['clientVorname'];
      $data[$r]['clientMail'] = $key['clientMail'];
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

function updateReserveEnd($rID,$time) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rReserve SET reserveEnd = '$time' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveStart($rID, $startTime, $endTime) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con -> query("UPDATE rReserve SET reserveStart = '$startTime', reserveEnd = '$endTime' WHERE reserveID = '$rID'");
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveDuration($rID,$duration,$endTime) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rReserve SET reserveEnd = '$endTime', reserveDuration = '$duration' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveAmount($rID,$amount) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rReserve SET reserveAmount = '$amount' WHERE reserveID = '$rID'";
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
      $statement2 = "INSERT INTO rNoshow (nsID,nsMail,nsAmount,nsDate) VALUES (null,'$clientMail','1','$date') ON DUPLICATE KEY UPDATE nsAmount = nsAmount +1";
      $query2 = $con -> query($statement2);
      if($query2 === TRUE) { return true; }
      return false;
    }
  }
  return false;
}

function updateClient($cID,$rID,$vn,$nn,$ma,$adr,$tnr,$date,$cc) {
  $db = new Overview();
  $con = $db->connectDatabase();
  if(strlen($cID)<=0){ $cID = uniqid(); }
  $temp = "clientID,reserveID,clientVorname,clientName,clientMail,clientTNR,clientDate,clientConfirm";
  $temp2 = "'$cID','$rID','$vn','$nn','$ma','$adr','$tnr','$date','$cc'";
  $statement = "INSERT INTO rClient ($temp) VALUES ($temp2) ON DUPLICATE KEY UPDATE clientVorname = '$vn', clientName = '$nn', clientMail = '$ma', clientTNR='$tnr'";
  $query = $con -> query($statement);
  if($query===TRUE){
    return true;
  }
  return false;
}
function deleteClient($cID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "DELETE FROM rClient WHERE clientID = '$cID'";
  $query = $con -> query($statement);
  if($query===TRUE){return true;}
  return false;
}

function createNewReserve($table,$client,$date) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $rC = uniqid();
  $p = "INSERT INTO rReserve (reserveID, tableID, clientID, reserveDate, reserveStart, reserveEnd, reserveDuration, reserveAmount, reserveCookie, reserveState) VALUES (null,'$table','$client','$date','00:00:00','00:00:00','2:30','0','$rC','0')";
  $query = $con->query($p) or die();
  if($query === TRUE){
    return $con->insert_id;
  }
  return false;
}

?>
