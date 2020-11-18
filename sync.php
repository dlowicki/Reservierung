<?php
if(isset($_POST['loadTables'])){
  $data = getTableData();
  echo json_encode($data);
  return;
}

if(isset($_POST['loadTableID'])){
  $data = getTableDataID($_POST['loadTableID']);
  echo json_encode($data);
  return;
}

if(isset($_POST['getTime']) && isset($_POST['sndDate'])){
  $r = getTableReserveTime($_POST['getTime'], $_POST['sndDate']);
  if($r){ echo json_encode($r); }
  return;
}

if(isset($_POST['getOverview']) && isset($_POST['oDate'])){
  if($_POST['getOverview'] == "day"){
    echo json_encode(getOverviewDay($_POST['oDate']));
    return;
  } else {
    echo json_encode(getOverviewWeek($_POST['oDate'],$_POST['o7Date']));
    return;
  }
  echo "0";
  return;
}


if(isset($_POST['createReserve'])){
  $daten = $_POST['createReserve'];
  $amount=$daten[0];
  $date=$daten[1];
  $time = $daten[2];
  $duration = $daten[3];
  $table = $daten[4];

  $hhExist = false;
  for ($i=0; $i < 5; $i++) {
    if(array_key_exists($i,$daten[5])==false){ continue; }
    if(strlen($daten[5][$i]) > 10){
      $exp = explode(";",$daten[5][$i]);
      if(strlen($exp[0])>0 && strlen($exp[1])>0 && strlen($exp[2])>0 && strlen($exp[3])>0 && strlen($exp[4])>0){
        $hhExist = true;
      }
    }
  }

  if($hhExist == false){
    echo "0";
    return;
  }

  switch($duration){
    case "1":
      $duration = "2:30";
      break;
    case "2":
      $duration = "gz";
      break;
  }

  // checkReserveTime TIME DATE TABLE
  if(checkReserveTime($time,$duration,$date,$table)){
	  $clientID = uniqid(); // ClientID
    $rCookie = uniqid(); // Um später Tisch bearbeiten zu können
    // $tID,$cID,$rDate,$rStart,$rEnd,$rD,$rA
    $rID = reserveTable($table,$clientID,$date,$time . ":00",$duration,$amount,$rCookie);
    if($rID != false){
      $date = echoDateTime();
      $count = 0;
      $con = connect();
      foreach ($daten[5] as $key => $value) {
        if($count != 0){ $clientID = uniqid(); }
        if($value){
          $exp = explode(";",$value);
          $cf = uniqid()."".uniqid();
          $vorname = $exp[0]; $name = $exp[1]; $mail = $exp[2]; $adresse = $exp[3]; $tnr = $exp[4];
          // $cID,$rID,$tID,$vorname,$name,$mail,$adresse,$tnr,$cf
          $sqlStatement = "INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientAdresse, clientTNR, clientDate, clientConfirm) VALUES ('$clientID','$rID','$vorname','$name','$mail','$adresse','$tnr','$date','$cf');";
          $query = $con -> query($sqlStatement) or die();
          if($query !== TRUE){
            echo "0";
            return;
          }
        }
        $count++;
      }
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

if(isset($_POST['hubName']) && isset($_POST['hubSecure'])){
  if(r($_POST['hubName']) == false && r($_POST['hubSecure']) == false){
    $con = connect();
    $query = $con->prepare("SELECT userName, userPW FROM rUser WHERE userName = ? AND userActive = '1'");
    $query->bind_param('s',$_POST['hubName']);
    $query->execute();

    $result = $query->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);

    if(sizeof($row) >= 1){
      if($row['userName'] == $_POST['hubName'] && $row['userPW'] == $_POST['hubSecure']){
        $cookie = md5(uniqid());
        $n = $_POST['hubName'];
        $query2 = $con->query("UPDATE rUser SET userCookie = '$cookie' WHERE userName = '$n'");
        if($query2===TRUE){
          setcookie("rSession", $cookie, time()+3600, "/");
          echo "1";
          return;
        }
        echo "0";
        return;
      }
    }

  }
  echo "0";
  return;
}

if(isset($_POST['user'])){
  $con = connect();
  $query = $con->prepare("SELECT userCookie FROM rUser WHERE userCookie = ?");
  $query->bind_param('s',$_POST['user']);
  $query->execute();

  $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);

  if(sizeof($row) >=1 ){
    if($row['userCookie'] == $_POST['user']) {
      echo md5($row['userCookie']);
      return;
    }
  }
  echo "0"; return;
}

if(isset($_POST['remove'])){
  $con = connect();
  $cookie = htmlspecialchars($_COOKIE['rSession']);
  $clientConfirm = $_POST['remove'];
  $query=$con->query("SELECT userActive FROM rUser WHERE userCookie = '$cookie'");
  if($query){
    foreach ($query as $key) {
      if($key['userActive'] == "1"){
        $query2 = $con->query("DELETE rClient, rReserve FROM rClient INNER JOIN rReserve ON rClient.clientID = rReserve.clientID WHERE rClient.clientConfirm = '$clientConfirm'");
        if($query2 === TRUE){
          echo "1";
          return;
        }
      }
    }
  }
  echo "0";
  return;
}

if(isset($_POST['qrCode'])){
  $con = connect();
  $query = $con->prepare("SELECT tableID FROM rTable WHERE tableCode = ?");
  $query->bind_param('s',$_POST['qrCode']);
  $query->execute();

  $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);
  if(isset($row)){
    if(sizeof($row) >=1 ){
        echo $row['tableID']; return;
    }
  }
  echo "0"; return;
}

if(isset($_POST['changeReserve'])){
  $id = $_POST['changeReserve'];
  $con = connect();
  $query = $con->query("SELECT reserveID, tableID, reserveStart, reserveEnd FROM rReserve WHERE reservecookie = '$id'");
  $data = array();

  if($query){
    $r = 0;
    foreach ($query as $key) {
      $data[$r]['reserveID'] = $key['reserveID'];
      $data[$r]['tableID'] = $key['tableID'];
      $data[$r]['reserveStart'] = $key['reserveStart'];
      $data[$r]['reserveEnd'] = $key['reserveEnd'];
      $data[$r]['clients'] = getClientsFromReserve($key['reserveID']);
    }
    echo json_encode($data);
    return;
  }
  return false;
}

if(isset($_POST['crSubmit']) && isset($_POST['crID'])){
  $rID = $_POST['crID'];
  $data = $_POST['crSubmit']; // Hier Einträge von CHANGE RESERVE
  $base = getClientsFromReserve($rID); // Clients von Reserverierung
  $dateTime = echoDateTime();

  foreach ($data as $key => $value) { // value beinhaltet Array mit Input;Input;Input;Input
    if(is_string($value)){ // Wenn value == string
      $inside = false; // Boolean, falls ID in $base
      $exp = explode(";",$value); // Aufteilen bei Semikolon
      $vorname = $exp[0]; $name = $exp[1]; $mail = $exp[2]; $adresse = $exp[3]; $tnr = $exp[4]; $cID = $exp[5];
      foreach ($base as $key2) { // Für jeden Client in Reservierung
        if($key2['clientID'] == $cID){ // Wenn ClientID von jetzigem Client == $cID
          $inside = true; // Dann ist der Client schon in Datenbank
          // Wenn Vorname und Name gesetzt sind
          acpUpdateClient($cID,$rID,$vorname,$name,$mail,$adresse,$tnr,$dateTime,uniqid());
        }
      }
      if($inside == false){ // Wenn Client nicht in Datenbank --> hinzufügen
        $cf = uniqid();
        $con = connect(); // Baue Verbindung auf
        $sqlStatement = "INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientAdresse, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$adresse','$tnr','$dateTime','$cf');";
        $query = $con -> query($sqlStatement) or die(); // Führe Query aus
        if($query === FALSE){ // Wenn INSERT Fehler hat
          echo "0";
          return;
        }
      }
    }
  }
  echo "1";
  return false;
}

if(isset($_POST['crDelete'])){
  if(acpDeleteClient($_POST['crDelete'])){
    echo "1";
    return;
  }
  echo "0";
  return;
}


/* ACP ISSET ABFRAGEN */

if(isset($_POST['acpReserve']) && isset($_POST['acpSubmit'])){
  $daten = $_POST['acpReserve'];
  $type = $_POST['acpSubmit'];

  $con = connect();
  $cookie = htmlspecialchars($_COOKIE['rSession']);
  $query=$con->query("SELECT userActive FROM rUser WHERE userCookie = '$cookie'");
  if($query){
    $amount = $daten[0];
    $date=$daten[1];
    $time = $daten[2];
    $duration = $daten[3];
    $rID = $daten[4];
    $tID = $daten[5];

    $hhExist = false;
    for ($i=0; $i < 5; $i++) {
      if(array_key_exists($i,$daten[6])==false){ continue; }
      if(strlen($daten[6][$i]) > 10){
        $exp = explode(";",$daten[6][$i]);
        if(strlen($exp[0])>0 && strlen($exp[1])>0 && strlen($exp[2])>0 && strlen($exp[3])>0 && strlen($exp[4])>0){
          $hhExist = true;
        }
      }
    }

    switch($duration){
      case "1":
        $duration = "2:30";
        break;
      case "2":
        $duration = "gz";
        break;
    }

    $clientID = uniqid(); // Falls Reservierung neu erstellt werden muss
    if($type == 'Bearbeiten'){
      acpUpdateReserve($rID,$date,$time,createReserveEnd($time,$duration),$duration,$amount);
    } elseif($type == 'Erstellen') {
      $rID = reserveTable($tID,$clientID,$date,$time . ":00",$duration,$amount,uniqid());
    }

    if($hhExist == true){
      $count = 0;
      foreach ($daten[6] as $key => $value) {
        if($value){
            if($count != 0){ $clientID = uniqid(); }
            $exp = explode(";",$value);
            $dateTime = echoDateTime();
            $vorname = $exp[0]; $name = $exp[1]; $mail = $exp[2]; $adresse = $exp[3]; $tnr = $exp[4]; $cID = $exp[5];
            if($type == 'Bearbeiten'){
              acpUpdateClient($cID,$rID,$vorname,$name,$mail,$adresse,$tnr,$dateTime,uniqid()."".uniqid());
            } elseif($type == 'Erstellen'){
              $cf = uniqid()."".uniqid();
              $sqlStatement = "INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientAdresse, clientTNR, clientDate, clientConfirm) VALUES ('$clientID','$rID','$vorname','$name','$mail','$adresse','$tnr','$dateTime','$cf');";
              $query = $con -> query($sqlStatement) or die();
              if($query === FALSE){
                // Hier eventuell Reservierung wieder entfernen
                echo "0";
                return;
              }
            }
        }
        $count++;
      }
    }
    echo "1";
    return;
  }
  echo "0";
  return;
}



if(isset($_POST['acpInputs'])){
  $data = array("reserve"=>getReserveData($_POST['acpInputs']), "clients"=>getClientsFromReserve($_POST['acpInputs']));
  echo json_encode($data);
  return;
}

if(isset($_POST['getTableActive'])){
  $id = $_POST['getTableActive'];
  $con = connect();
  $statement = "SELECT tableActive FROM rTable WHERE tableID = '$id' LIMIT 1";
  $query = $con -> query($statement);
  $query = $query->fetch_array(MYSQLI_ASSOC);
  if($query['tableActive']){
    echo $query['tableActive'];
    return;
  }
  echo "0";
  return;
}

if(isset($_POST['setTableActive']) && isset($_POST['value'])){
  $id = $_POST['setTableActive'];
  $val = $_POST['value'];
  if($val == "true"){ $val = "open"; } else { $val = "closed"; }
  $con = connect();
  $statement = "UPDATE rTable SET tableActive = '$val' WHERE tableID = '$id'";
  $query = $con -> query($statement);

  if($query === TRUE) {
    echo "1";
    return;
  }
  echo "0";
  return;
}



function acpUpdateReserve($rID,$rDate,$rS,$rE,$rD,$rA) {
  $con = connect();
  $statement = "UPDATE rReserve SET reserveDate = '$rDate', reserveStart = '$rS', reserveEnd = '$rE', reserveDuration = '$rD', reserveAmount='$rA' WHERE reserveID = '$rID'";
  $query = $con -> query($statement);
  if($query===TRUE){
    return true;
  }
  return false;
}

function acpUpdateClient($cID,$rID,$vn,$nn,$ma,$adr,$tnr,$date,$cc) {
  $con = connect();
  $iClient = uniqid();
  if(strlen($cID)<=0){ $cID = uniqid(); }
  $temp = "clientID,reserveID,clientVorname,clientName,clientMail,clientAdresse,clientTNR,clientDate,clientConfirm";
  $temp2 = "'$cID','$rID','$vn','$nn','$ma','$adr','$tnr','$date','$cc'";
  $statement = "INSERT INTO rClient ($temp) VALUES ($temp2) ON DUPLICATE KEY UPDATE clientVorname = '$vn', clientName = '$nn', clientMail = '$ma', clientAdresse = '$adr', clientTNR='$tnr'";
  $query = $con -> query($statement);
  if($query===TRUE){
    return true;
  }
  return false;
}

function acpDeleteClient($cID) {
  $con = connect();
  $statement = "DELETE FROM rClient WHERE clientID = '$cID'";
  $query = $con -> query($statement);
  if($query===TRUE){
    return true;
  }
  return false;
}

function tischAktivieren($rID) {
  $con = connect();
  $statement = "UPDATE rTable INNER JOIN rReserve ON (rReserve.tableID = rTable.tableID) SET rTable.tableActive = 'open' WHERE rReserve.ReserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function tischDeaktivieren($rID) {
  $con = connect();
  $statement = "UPDATE rTable INNER JOIN rReserve ON (rReserve.tableID = rTable.tableID) SET rTable.tableActive = 'closed' WHERE rReserve.ReserveID = '$rID'";
  $query = $con -> query($statement);
  if($query === TRUE){
    return true;
  }
  return false;
}

function updateReserveStart($rID, $datetime) {
  $con = connect();
  $statement = "UPDATE rReserve SET reserveStart = '$datetime' WHERE reserveID = '$rID'";
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

function getReserveData($id) {
  $con = connect();
  $statement = "SELECT tableID, clientID, reserveDate, reserveStart, reserveEnd, reserveDuration, reserveAmount FROM rReserve WHERE reserveID = '$id'";
  $query = $con -> query($statement);
  $data = array();
  if($query){
    foreach($query as $key) {
      $data['tableID'] = $key['tableID'];
      $data['clientID'] = $key['clientID'];
      $data['reserveDate'] = $key['reserveDate'];
      $data['reserveStart'] = $key['reserveStart'];
      $data['reserveEnd'] = $key['reserveEnd'];
      $data['reserveDuration'] = $key['reserveDuration'];
      $data['reserveAmount'] = $key['reserveAmount'];
    }
    return $data;
  }
  return false;
}

function getClientsFromReserve($id) {
  $con = connect();
  $statement = "SELECT clientID,clientVorname,clientName,clientMail,clientAdresse,clientTNR FROM rClient WHERE reserveID = '$id'";
  $query = $con -> query($statement);
  $data = array();

  if($query){
    $r=1;
    foreach ($query as $key) {
      $data[$r]['clientID'] = $key['clientID'];
      $data[$r]['clientVorname'] = $key['clientVorname'];
      $data[$r]['clientName'] = $key['clientName'];
      $data[$r]['clientMail'] = $key['clientMail'];
      $data[$r]['clientAdresse'] = $key['clientAdresse'];
      $data[$r]['clientTNR'] = $key['clientTNR'];
      $r++;
    }
    return $data;
  }
  return false;
}






function getOverviewDay($date) {
  $con = connect();
  $cookie = htmlspecialchars($_COOKIE['rSession']);
  $query=$con->query("SELECT userActive FROM rUser WHERE userCookie = '$cookie'");

  if($query){
    $para = "rReserve.reserveID,rReserve.tableID,reserveStart,reserveEnd,reserveDuration,reserveAmount,clientName,clientTNR";
    $query2 = $con -> query("SELECT $para FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE reserveTime LIKE '$date %' ORDER BY reserveTime ASC");

    if($query2){
      $data = array();
      $s=0;
      foreach ($query2 as $key) {
        $data[$s]['rID'] = $key['reserveID'];
        $data[$s]['tID'] = $key['tableID'];
        $data[$s]['rStart'] = $key['reserveStart'];
        $data[$s]['rEnd'] = $key['reserveEnd'];
        $data[$s]['rDuration'] = $key['reserveDuration'];
        $data[$s]['rA'] = $key['reserveAmount'];
        $data[$s]['cName'] = $key['clientName'];
        $data[$s]['cTNR'] = $key['clientTNR'];
        $s++;
      }
      return $data;
    }
  }
  return false;
}

function getOverviewWeek($date,$d7) {
  $con = connect();
  $cookie = htmlspecialchars($_COOKIE['rSession']);
  $query=$con->query("SELECT userActive FROM rUser WHERE userCookie = '$cookie'");

  if($query){
    $para = "rReserve.reserveID,rReserve.tableID,reserveStart,reserveEnd,reserveDuration,reserveAmount,clientName,clientTNR";
    $query2 = $con -> query("SELECT $para FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE reserveTime BETWEEN '$date %' AND '$d7 %' ORDER BY reserveTime ASC");

    if($query2){
      $data = array();
      $s=0;
      foreach ($query2 as $key) {
        $data[$s]['rID'] = $key['reserveID'];
        $data[$s]['tID'] = $key['tableID'];
        $data[$s]['rStart'] = $key['reserveStart'];
        $data[$s]['rEnd'] = $key['reserveEnd'];
        $data[$s]['rDuration'] = $key['reserveDuration'];
        $data[$s]['rA'] = $key['reserveAmount'];
        $data[$s]['cName'] = $key['clientName'];
        $data[$s]['cTNR'] = $key['clientTNR'];
        $s++;
      }
      return $data;
    }
  }
  return false;
}


function connect() {
  $servername = "localhost";
  $username = "shop";
  $password = "123456";
  $db = "reservierung";

  $conn = new mysqli($servername, $username, $password, $db); // Create connection

  if ($conn->connect_error) {   // Check connection
      die("Connection failed: " . $conn->connect_error);
      return false;
  }
  return $conn;
}

function reserveTable($tID,$cID,$rDate,$rS,$rD,$rA,$rC) {
  $con = connect();
  $rE = createReserveEnd($rDate . " " . $rS, $rD);
  $p = "INSERT INTO rReserve (reserveID, tableID, clientID, reserveDate, reserveStart, reserveEnd, reserveDuration, reserveAmount, reserveCookie, reserveState) VALUES (null,'$tID','$cID','$rDate','$rS','$rE','$rD','$rA','$rC','0')";
  $query = $con->query($p) or die();
  if($query === TRUE){
    $p2="SELECT reserveID FROM rReserve ORDER BY reserveID DESC LIMIT 1";
    $query2 = $con->query($p2) or die();
    if($query2){
      foreach ($query2 as $key) {
        return $key['reserveID'];
      }
    }
    return false;
  }
  return false;
}

function createClient($cID,$rID,$vorname,$name,$mail,$adresse,$tnr,$cf) {
  $con = connect();
  $date = echoDateTime();
  $p="INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientAdresse, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$adresse','$tnr','$date','$cf')";
  $query = $con -> query($p) or die();
  if($query === TRUE){
    return true;
  }
  return false;
}



function getTableData() {
  $date = echoDate();
  $con = connect();
  $p = "SELECT * FROM rTable";
  $p2 = "SELECT tableID,reserveDate,reserveStart,reserveEnd,reserveDuration,reserveState FROM rReserve WHERE reserveStart LIKE '$date %'";
  $query = $con -> query($p) or die();
  $query2 = $con -> query($p2) or die();
  $data = array();

  if($query){
    $c = 0;
    foreach ($query as $table) {
      $data[$c]['tableID'] = $table['tableID']; $data[$c]['tableType'] = $table['tableType'];
      $data[$c]['tableMax'] = $table['tableMax']; $data[$c]['tableMin'] = $table['tableMin'];
      $data[$c]['tableCode'] = $table['tableCode']; $data[$c]['tableActive'] = $table['tableActive'];
      foreach ($query2 as $reserve) {
        if($reserve['tableID'] == $table['tableID']){
          if(checkReserveTime($reserve['reserveStart'],$reserve['reserveDuration'],$reserve['reserveDate'],$table['tableID']) == false){
            // Überschneidung ist aktiv
            $data[$c]['tableActive'] = "closed";
          }

          /*$dbTime = strtotime($row2['reserveTime']); $uH = date('H', $dbTime) + 2; $uM = date('i', $dbTime) + 30;
          if($uM >= 60){ $uH = date('H', $dbTime) + 3; $uM = date('i', $dbTime) + 30 - 60;
            if(strlen($uM)==1){ $uM = "0".$uM; } }
          if(echoTime() > date('H', $dbTime).":".date('i', $dbTime) && echoTime() < $uH.":".$uM){
            $data[$c]['tableReserved'] = "closed";
          }*/

        }
      }
      $c++;
    }
    return $data;
  }
  return false;
}


function getTableDataID($id) {
  $date = echoDate();
  $con = connect();
  $p = "SELECT * FROM rTable WHERE tableID = '$id'";
  $p2 = "SELECT tableID, reserveDate, reserveStart, reserveEnd, reserveDuration FROM rReserve WHERE tableID = '$id' AND reserveStart LIKE '$date %'";
  $query = $con -> query($p) or die();
  $query2 = $con -> query($p2) or die();
  $data = array();

  if($query && $query2){
    foreach ($query as $row) { // Tisch mit TischID erhalten
      $data['tableID'] = $row['tableID'];
      $data['tableType'] = $row['tableType'];
      $data['tableMax'] = $row['tableMax'];
      $data['tableMin'] = $row['tableMin'];
      $data['tableCode'] = $row['tableCode'];
      $data['tableActive'] = $row['tableActive'];
      $data['tableReserved'] = "open";
      $r=0;
      $timeSchneidung=false;
      foreach ($query2 as $row2) { // Jede Reservierung für den Tisch am besagten Tag
        $data['reserveA'] = $r;
        $data[$r]['rDate'] = $row2['reserveDate'];
        $data[$r]['rS'] = $row2['reserveStart'];
        $data[$r]['rE'] = $row2['reserveEnd'];
        $data[$r]['rD'] = $row2['reserveDuration'];

        // Wenn TIME >= startzeit && TIME <= ENDZEIT
        if(echoTime().":00">=$row2['reserveStart'] && echoTime().":00"<=$row2['reserveEnd']){
          $timeSchneidung = true; // Eine Reservierung läuft zurzeit
        }
        $r++;
      }
      if($timeSchneidung==true){ $data['tableReserved'] = "closed"; }
    }
    return $data;
  }
  return false;
}

function getTableReserveTime($t, $date) {
  $con = connect();
  $p = "SELECT rReserve.reserveID,rReserve.reserveDate,rReserve.reserveStart,rReserve.reserveEnd,reserveDuration,rReserve.reserveState,rReserve.clientID,clientConfirm,clientName FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE rReserve.tableID = '$t' AND reserveDate = '$date' ORDER BY reserveStart ASC";
  $query = $con -> query($p) or die();
  $data = array();

  if($query){
    $r=0;
    foreach ($query as $key) {
      $data[$r]['rID'] = $key['reserveID'];
      $data[$r]['rDate'] = $key['reserveDate'];
      $data[$r]['rS'] = $key['reserveStart'];
      $data[$r]['rE'] = $key['reserveEnd'];
      $data[$r]['rD'] = $key['reserveDuration'];
      $data[$r]['cc'] = $key['clientConfirm'];
      $data[$r]['cn'] = $key['clientName'];
      $data[$r]['rState'] = $key['reserveState'];
      $r++;
    }
    return $data;
  }
  return false;
}

function getIP() {
  $userIP = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  return $userIP;
}

function encrypt($value, $key) { // verschlüsseln
  return openssl_encrypt($value, "AES-128-ECB", $key);
}
function decrypt($value, $key) { // entschlüsseln
  return openssl_decrypt($value, "AES-128-ECB", $key);
}
function echoDate() {
  return date("Y-m-d");
}
function echoDateTime() {
  return date("Y-m-d G:i:s");
}
function echoTime() {
  return date("G:i");
}

function r($str){
  if(strlen($str) >= 4){
    $re = '/[+\;\/*{}´^<>=%$§#]+/m';
    return preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
  }
  return false;
}
/*function sanitize_mail($field) {
  $field = filter_var($field, FILTER_SANITIZE_EMAIL);
  if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
    return true;
  }
  return false;
}*/


// Überprüfe ob Überschneidung mit Reservierung
function checkReserveTime($time,$duration,$date,$tableID) {
  $startTime="17:00"; $endTime="21:30";
  if($time<$startTime||$time>$endTime){ // time kleiner als startTime oder time größer als endTime
    return false;
  }

  $con=connect();
  $p="SELECT reserveDate,reserveStart,reserveEnd,reserveDuration FROM rReserve WHERE reserveStart LIKE '$date %' AND tableID = '$tableID'";
  $query = $con->query($p) or die();

  if($query){
    $key = $query->fetch_array(MYSQLI_ASSOC);
    $reservierung = array();
    $reservierungCount=0;
    ini_set("display_errors","off");
    for ($s=0; $s < count($key); $s++) {
      //$r=explode(" ",$key['reserveStart']);
      if($duration == "gz" && $key['reserveDuration'] == "gz"){
        // Wenn Reservierung GANZTAGS aber GANZTAGS schon existiert
        return false;
      }

      $dbT = updatedTime($key['reserveDate'] . " " . $key['reserveStart']);
      if($duration == "gz" && $key['reserveDuration'] == "2:30"){
        // Reservierung GANZTAGS ausgewählt aber Tag hat schon Reservierung
        if($time>$dbT['uH'].":".$dbT['uM']){
          // Wenn Reservierungszeit > Updated Datenbankzeit kann kein GANZTAGS gesetzt werden
          $reservierung[$reservierungCount]=true;
        } else {
          $reservierung[$reservierungCount]=false;
        }
        continue;
      }

      if($duration == "2:30"){
        $pT = updatedTime($date . " " . $time . ":00");
        $b = true;
        // Wenn $time größer als $dbTime + 2:30 oder $time kleiner als $dbTime
        if($time > $dbT['uH'].":".$dbT['uM'] || $time < $dbT['hour'].":".$dbT['minute']){
          // Wenn $time +2:30 kleiner als $dbTime oder $time größer als $dbTime + 2:30
          if($pT['uH'].":".$pT['uM'] < $dbT['hour'].":".$dbT['minute'] || $time > $dbT['uH'].":".$dbT['uM']){
            $reservierung[$reservierungCount] = true;
          } else { $reservierung[$reservierungCount] = false; }
        } else { $reservierung[$reservierungCount] = false; }
      }

      $reservierungCount++;
    }
    ini_set("display_errors","on");
    // FALSE wurde in array gefunden = überschneidung irgenwo
    if(in_array(false,$reservierung)){
      return false;
    } else {
      return true;
    }

  }
  return true;
}

function createReserveEnd($rStart, $rDuration) {
  $time = strtotime($rStart);
  if($rDuration == "2:30"){
    $uH = date('H', $time) +2; $uM = date('i', $time) +30;
    if($uM >= 60){
      $uH = date('H', $time) + 3; $uM = date('i', $time) + 30 - 60; if(strlen($uM)==1){ $uM = "0".$uM; }
    }
    return echoDate() . " " . $uH . ":" . $uM . ":00";
  } else {
    return echoDate() . " 22:00:00";
  }
  return false;
}

function updatedTime($t) {
  $time = strtotime($t); $uH = date('H', $time) + 2; $uM = date('i', $time) + 30;
  if($uM >= 60){
    $uH = date('H', $time) + 3; $uM = date('i', $time) + 30 - 60; if(strlen($uM)==1){ $uM = "0".$uM; }
  }
  // uM = up Minutes uH = up Hours
  return array('uM' => $uM, 'uH' => $uH, 'hour' => date('H', $time), 'minute' => date('i', $time));
}
?>
