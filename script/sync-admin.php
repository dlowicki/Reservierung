<?php
require_once("script.admin.php");

if(!isset($_COOKIE['rSession'])){ return; }
if(isAdmin() != true){ return; }

/*
* Created 17.11.2020
* Mail und ID = Unique
*
*/



if(isset($_POST['nsAmount']) && isset($_POST['nsMail']) && isset($_POST['nsTNR']) && isset($_POST['nsTime']) && isset($_POST['nsID'])){
  $id = trim($_POST['nsID']); // Erhalte ID von POST (trim)
  $amount = trim($_POST['nsAmount']); // Erhalte Anzahl von POST (trim)
  $mail = trim($_POST['nsMail']); // Erhalte neue Mail von POST (trim)
  $tnr = trim($_POST['nsTNR']); // Erhalte neue Telefonnummer von POST (trim)
  $time = trim($_POST['nsTime']); // Erhalte neue Zeit von POST (trim)
  // Wenn Alle Variablen gesetzt sind
  if(strlen($id) >= 1 && strlen($amount) >= 1 && strlen($mail) >= 3 && strlen($tnr) >= 3 && strlen($time) >= 3){
    $overview = new Overview();
    $update = $overview->updateNoShow($id,$amount,$mail,$tnr,$time);
    if($update){
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

// Öffnungszeiten
if(isset($_POST['arbeitstag'])){
  $data = explode(';',$_POST['arbeitstag']);
  if(isset($data[0])&&isset($data[1])){ echo updateDays($data[0],$data[1],$data[2]); }
}
if(isset($_POST['specialDay'])){
  $exp = explode(";",$_POST['specialDay']);
  if(strlen($exp[0])>=1 && strlen($exp[1])>=1 && strlen($exp[2])>=1){ echo addSpecialDay($exp[0],$exp[1],$exp[2]); }
}
if(isset($_POST['specialDayDelete'])){
  $id = explode("-",$_POST['specialDayDelete'])[1];
  if(strlen($id)>=1){ echo deleteSpecialDay($id); }
}

// Tischplan
if(isset($_POST['updateAdminTables'])){
  $exp = explode(';',$_POST['updateAdminTables']);
  if(sizeof($exp) == 6){
    $oldID=$exp[0];$newID=$exp[1];$min=$exp[2];$max=$exp[3];$place=$exp[4];$check=$exp[5];
    if($check=='false'){$check="closed";}else{$check="open";}
    $db = new Overview();
    $con = $db->connectDatabase();
    $query = $con->query("UPDATE rtable SET tablePlace='$place', tableMax=$max, tableMin=$min, tableID=$newID, tableActive='$check' WHERE tableID=$oldID");
    if($query === TRUE){ echo '1'; return; } echo '0'; return;
  }
  echo '0'; return;
}
if(isset($_POST['tischPanel'])){
  $exp = explode(';',$_POST['tischPanel']); $all = $exp[0]; $stCheck = $exp[1];
  if(strlen($all)>= 1 && strlen($stCheck)>= 1){
    if($all=='false'){$all="closed";}else{$all="open";}
    if($stCheck=='false'){$stCheck="closed";}else{$stCheck="open";}
    $statement = ""; $st = $exp[2];

    if($stCheck == 'closed' && $all == 'open'){ // Standort closed aber alle auf (Nur Standort wird geschlossen)
      $statement = "UPDATE rtable SET tableActive = 'closed' WHERE tablePlace = '$st'";
    } elseif($stCheck == 'closed' && $all == 'closed') { // Beide sind closed, also alle Tische schließen
      $statement = "UPDATE rtable SET tableActive = 'closed'";
    } elseif($stCheck == 'open' && $all == 'open'){ // Beide sind open, also alle Tische auf
      $statement = "UPDATE rtable SET tableActive = 'open'";
    } elseif($stCheck == 'open' && $all == 'closed') { // Standort auf aber alle schließen
      $statement = "UPDATE rtable SET tableActive = 'open' WHERE tablePlace = '$st'";
    }

    $db = new Overview(); $con = $db->connectDatabase();
    $query = $con->query($statement);
    if($query===TRUE){ echo '1'; return; } echo '0'; return;
  }
}
if(isset($_POST['loadStandort'])){
  $standort = $_POST['loadStandort']; if(strlen($standort) <= 0){ echo '0'; return; }
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->query("SELECT tableActive FROM rtable WHERE tablePlace = '$standort'");
  if($query == TRUE){ foreach ($query as $key) { if($key['tableActive'] == 'closed'){ echo 'closed'; return; } } echo 'open'; return; }
  echo '0'; return;
}


// Liste NoShow
if(isset($_POST['listeEdit']) && isset($_POST['listeType'])){
  $edit = trim($_POST['listeEdit']); $type = trim($_POST['listeType']);
  if(strlen($edit) >= 1){
    $overview = new Overview(); $result;
    if($edit != 'Create'){ $result = $overview->deleteListeRow($edit, $type); } else { $result = $overview->createListeRow($type); }
    if($result){ echo "1"; return;} echo "0"; return;
  }
}
if(isset($_POST['updateListen'])){
  $exp = explode(";",$_POST['updateListen']);
  $overview = new Overview();
  echo $overview->updateListe($exp[0],$exp[1],$exp[2],$exp[3],$exp[4]);
}





// Reservierung Laden
if(isset($_POST['rsLoad'])){
  $rID = $_POST['rsLoad'];
  $rData = getReservierungData($rID);
  if($rData != false){ echo json_encode($rData); return; } echo "0"; return;
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
      $func = true;
      break;
    case '3':
      /*Hierfür muss checkTime usw. angepasst werden*/
      $func = true;
      break;
    case '4':
      /* checkTime usw. anpasen und NoShow eintragen*/
      if(addNoShow($rID, $date)){ $func = true; }
      break;
    case '5':
      /* checkTime usw. anpasen und NoShow eintragen*/
      if(addBlacklist($rID, $date)){ $func = true; }
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
      $updateClient = updateClient($key[0],$key[1],$key[3],$key[2],$key[4],$key[5],date("Y-m-d"),uniqid());
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
    if(updateClient($clientID,$cnrID,'Vorname','Nachname','E-Mail','Telefon',date("Y-m-d"),uniqid())){
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

if(isset($_POST['updateReserveBlock'])){
  $exp = explode(';',$_POST['updateReserveBlock']);
  if(sizeof($exp) == 3){ if(updateReserveBlock($exp[0],$exp[1], $exp[2])){ echo '1'; return; }  } echo "0"; return;
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

/* RECHTE USER */
if(isset($_POST['userSwitch'])){
  $db = new Overview(); $con = $db->connectDatabase();
  $exp = explode(';',$_POST['userSwitch']);
  $query = $con->query("UPDATE ruser SET userActive = $exp[1] WHERE userID = $exp[0]");
  if($query === TRUE){ echo '1'; return; } echo '0'; return;
}
if(isset($_POST['userAbmelden'])){
  $db = new Overview(); $con = $db->connectDatabase(); $id = $_POST['userAbmelden'];
  $uniq = md5(uniqid());
  $query = $con->query("UPDATE ruser SET userCookie = '$uniq' WHERE userID = $id");
  if($query === TRUE){ echo '1'; return; } echo '0'; return;
}


function isAdmin(){
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->prepare("SELECT userCookie FROM ruser WHERE userCookie = ?");
  $query->bind_param('s',$_COOKIE['rSession']);
  $query->execute();

  $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);

  if(sizeof($row) >=1 ){ if($row['userCookie'] == $_COOKIE['rSession']) { return true; } }
  return false;
}






function getReservierungData($id) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "SELECT rreserve.tableID, rreserve.clientID, reserveDate, reserveTime, reserveBlock, reserveAmount, rclient.clientID, rclient.clientName, rclient.clientVorname, rclient.clientMail, rclient.clientTNR, rtable.tableType,rtable.tableActive FROM rreserve INNER JOIN rclient ON rreserve.reserveID = rclient.reserveID INNER JOIN rtable ON rreserve.tableID = rtable.tableID WHERE rreserve.reserveID = '$id'";
  $query = $con->query($statement) or die();

  if($query){
    $data = array(); $r = 0;
    foreach ($query as $key) {
      $data[$r]['tableID'] = $key['tableID'];
      $data[$r]['clientID'] = $key['clientID'];
      $data[$r]['reserveDate'] = $key['reserveDate'];
      $data[$r]['reserveTime'] = $key['reserveTime'];
      $data[$r]['reserveBlock'] = $key['reserveBlock'];
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
  $statement = "UPDATE rreserve SET reserveState = '$state' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveEnd($rID,$time) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rreserve SET reserveEnd = '$time' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveBlock($rID, $block, $time) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con -> query("UPDATE rreserve SET reserveTime = '$time', reserveBlock = '$block' WHERE reserveID = '$rID'");
  if($query === TRUE){ return true; } return false;
}

function updateReserveDuration($rID,$duration,$endTime) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rreserve SET reserveEnd = '$endTime', reserveDuration = '$duration' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveAmount($rID,$amount) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "UPDATE rreserve SET reserveAmount = '$amount' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}


function addBlacklist($rID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $date = date("Y-m-d G:i:s");
  $statement = "SELECT rclient.clientID, clientMail, clientTNR FROM rreserve INNER JOIN rclient ON rreserve.clientID = rclient.clientID WHERE rreserve.reserveID = '$rID'";
  $query = $con -> query($statement);
  foreach ($query as $key) {
    if($key['clientID'] && $key['clientMail']){
      $clientID = $key['clientID'];
      $clientMail = $key['clientMail'];
      $clientTNR = $key['clientTNR'];
      $statement2 = "INSERT INTO rblacklist (bID,bMail,bTNR,bAnzahl) VALUES (null,'$clientMail','$clientTNR',1) ON DUPLICATE KEY UPDATE bAnzahl = bAnzahl +1";
      $query2 = $con -> query($statement2);
      if($query2 === TRUE) { return true; }
      return false;
    }
  }
  return false;
}


function addNoShow($rID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $date = date("Y-m-d G:i:s");
  $statement = "SELECT rclient.clientID, clientMail FROM rreserve INNER JOIN rclient ON rreserve.clientID = rclient.clientID WHERE rreserve.reserveID = '$rID'";
  $query = $con -> query($statement);
  foreach ($query as $key) {
    if($key['clientID'] && $key['clientMail']){
      $clientID = $key['clientID'];
      $clientMail = $key['clientMail'];
      $statement2 = "INSERT INTO rnoshow (nsID,nsMail,nsAmount,nsDate) VALUES (null,'$clientMail','1','$date') ON DUPLICATE KEY UPDATE nsAmount = nsAmount +1";
      $query2 = $con -> query($statement2);
      if($query2 === TRUE) { return true; }
      return false;
    }
  }
  return false;
}

function updateClient($cID,$rID,$vn,$nn,$ma,$tnr,$date,$cc) {
  $db = new Overview();
  $con = $db->connectDatabase();
  if(strlen($cID)<=0){ $cID = uniqid(); }
  $temp = "clientID,reserveID,clientVorname,clientName,clientMail,clientTNR,clientDate,clientConfirm";
  $temp2 = "'$cID','$rID','$vn','$nn','$ma','$tnr','$date','$cc'";
  $statement = "INSERT INTO rclient ($temp) VALUES ($temp2) ON DUPLICATE KEY UPDATE clientVorname = '$vn', clientName = '$nn', clientMail = '$ma', clientTNR='$tnr'";
  $query = $con -> query($statement);
  if($query===TRUE){
    return true;
  }
  return false;
}
function deleteClient($cID) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $statement = "DELETE FROM rclient WHERE clientID = '$cID'";
  $query = $con -> query($statement);
  if($query===TRUE){return true;}
  return false;
}

function createNewReserve($table,$client,$date) {
  $db = new Overview();
  $con = $db->connectDatabase();
  $rC = uniqid();
  $p = "INSERT INTO rreserve (reserveID, tableID, clientID, reserveDate, reserveTime,reserveBlock, reserveAmount, reserveCookie, reserveState) VALUES (null,'$table','$client','$date','00:00:00 - 00:00:00','1','0','$rC','0')";
  $query = $con->query($p) or die();
  if($query === TRUE){ return $con->insert_id; }
  return false;
}

function updateDays($id,$time,$active){
  if($active == "true"){ $active = "1"; } else { $active = "0"; }
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->query("UPDATE rdays SET daysTime = '$time',daysActive='$active' WHERE daysID = $id") or die();
  if($query === TRUE){ return true; } return false;
}
function addSpecialDay($beschreibung, $date, $type){
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->query("INSERT INTO rspecial (spID,spType,spName,spDate) VALUES (null,'$type','$beschreibung','$date')") or die();
  if($query === TRUE){ return true; } return false;
}
function deleteSpecialDay($id){
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->query("DELETE FROM rspecial WHERE spID = $id") or die();
  if($query === TRUE){ return true; } return false;
}
?>
