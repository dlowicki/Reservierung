<?php

require_once('script/script.admin.php');

if(isset($_POST['loadTables'])){
  $data = getTableData($_POST['loadTables']);
  echo json_encode($data);
  return;
}

if(isset($_POST['loadTableID']) && isset($_POST['loadTableDate'])){
  $data = getTableDataID($_POST['loadTableID'], $_POST['loadTableDate']);
  echo json_encode($data);
  return;
}

if(isset($_GET['getReservierungen'])){
  $data = explode(';',$_GET['getReservierungen']);
  $r = getTableReserveTime($data[0],$data[1]); // 0 = TableID, 1 = Datum
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

if(isset($_POST['loadAmpel'])){
  $date = $_POST['loadAmpel'];
  echo json_encode(loadAmpelTables($date));
}


function loadAmpelTables($date) {
  $con = connect();
  $query = $con->query("SELECT tableID, reserveBlock FROM rReserve WHERE reserveDate LIKE '$date' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)");
  if($query){
    $arr=array(); $row=1;
    ini_set('display_errors','off');
    foreach ($query as $key) {
      if(in_array($key['tableID'], $arr[$row]) == false){
        $arr[$row]['table'] = $key['tableID'];
        $arr[$row]['block'] = $key['reserveBlock'];
      } else {
        $arr[$row]['table'] = $key['tableID'];
        $arr[$row]['block'] = $key['reserveBlock'];
      }
      $row++;
    }
    ini_set('display_errors','on');
    return $arr;
  }
  return false;
}

function loadAmpelForTable($tableID, $date) {
  $con = connect();
  $query = $con->query("SELECT tableID, reserveBlock FROM rReserve WHERE reserveDate LIKE '$date' AND tableID = $tableID AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)");
  if($query){
    $arr=array(); $row=0;
    ini_set('display_errors','off');
    foreach ($query as $key) {
      if(in_array($key['tableID'], $arr[$row]) == false){
        $arr[$row]['table'] = $key['tableID'];
        $arr[$row]['block'] = $key['reserveBlock'];
      } else {
        $arr[$row]['table'] = $key['tableID'];
        $arr[$row]['block'] = $key['reserveBlock'];
      }
      $row++;
    }
    ini_set('display_errors','on');
    return $arr;
  }
  return false;
}

// Daten von Reservierung, bei die auch stattfinden zurzeit
// Dann wird block überprüft, welcher schon reserviert ist
// Wenn Tisch kein Block mehr frei = rot

// Ich hab den Tag
// Ich hab die Uhrzeit gegeben
// Wenn angegebene Uhrzeit > reserveEnd dann Eintrag nicht beachten



if(isset($_POST['createReserve'])){
  $daten = $_POST['createReserve'];
  $amount = $daten[0];  // Anzahl von Personen z.B. 5
  $date = $daten[1];    // Genaues Datum der Reservierung 2021-05-20
  $block = $daten[2];   // BlockID z.B. 1
  $table = $daten[3];   // TischID 9

  $hhExist = false;
  for ($i=0; $i < 5; $i++) { // Schleife, die 5 mal durchlaufen wird
    if(array_key_exists($i,$daten[4])==false){ continue; } // Wenn $i in array nicht existiert
    //if(strlen($daten[5][$i]) > 10){}
    $exp = explode(";",$daten[4][$i]);
    if(count($exp) == 4){  // Count gibt Wert 4 zurück, alle Felder ausgefüllt
      if(strlen($exp[0])>0 && strlen($exp[1])>0 && strlen($exp[2])>0 && strlen($exp[3])>0){ $hhExist = true; }
    }
  }
  // $hhExist == false, kein Haushalt wurde eingetragen
  if($hhExist == false){ echo "0"; return; }

  //Überprüfe ob NoShow Eintrag vorhanden oder nicht
  $nsClient = explode(";",$daten[4][0]);
  $overview = new Overview();
  $noshow = $overview->getNoShowWithMailAndNumber($nsClient[2], $nsClient[3]);
  if($noshow){ if($noshow['amount'] >= 2){ echo '2'; return false; } }

  // checkReserveTime = Überprüfe ob Reservierung für Block bereits vorhanden | return false wenn belegt
  if(checkReserveTime($block,$date,$table)){
	  $clientID = uniqid();
    $rCookie = md5(uniqid(rand (),true));
    // $tID,$cID,$rDate,$rTime,$rD,$rA
    $rID = reserveTable($table,$clientID,$date,getTimeBlockTime($block),$block,$amount,$rCookie);
    if($rID != false){  // $rID wurde erfolgreich erstellt --> Reservierung eingetragen
      $date = echoDateTime(); $count = 0; $con = connect();
      foreach ($daten[4] as $key => $value) { // Für jeden Datensatz (Haushalt)
        if($count != 0){ $clientID = uniqid(); }
        if($value){ // Wenn Wert von Haushalt existiert
          $exp = explode(";",$value);
          $cf = uniqid()."".uniqid();
          $vorname = $exp[0]; $name = $exp[1]; $mail = $exp[2]; $tnr = $exp[3];
          // $cID,$rID,$tID,$vorname,$name,$mail,$adresse,$tnr,$cf
          $sqlStatement = "INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$clientID','$rID','$vorname','$name','$mail','$tnr','$date','$cf');";
          $query = $con -> query($sqlStatement) or die();
          if($query !== TRUE){ echo "0"; return; }
        }
        $count++;
      } echo "1"; return;
    }
  } echo "0"; return;
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
  $query = $con->query("SELECT reserveID, tableID, reserveTime FROM rReserve WHERE reservecookie = '$id'");
  $data = array();

  if($query){
    $r = 0;
    foreach ($query as $key) {
      $data[$r]['reserveID'] = $key['reserveID'];
      $data[$r]['tableID'] = $key['tableID'];
      $data[$r]['reserveTime'] = $key['reserveTime'];
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
      $vorname = $exp[0]; $name = $exp[1]; $mail = $exp[2]; $tnr = $exp[3]; $cID = $exp[4];
      foreach ($base as $key2) { // Für jeden Client in Reservierung
        if($key2['clientID'] == $cID){ // Wenn ClientID von jetzigem Client == $cID
          $inside = true; // Dann ist der Client schon in Datenbank
          // Wenn Vorname und Name gesetzt sind
          acpUpdateClient($cID,$rID,$vorname,$name,$mail,$tnr,$dateTime,uniqid());
        }
      }
      if($inside == false){ // Wenn Client nicht in Datenbank --> hinzufügen
        $cf = uniqid();
        $con = connect(); // Baue Verbindung auf
        $sqlStatement = "INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$tnr','$dateTime','$cf');";
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



function getReserveData($id) {
  $con = connect();
  $statement = "SELECT tableID, clientID, reserveDate, reserveTime, reserveBlock, reserveAmount FROM rReserve WHERE reserveID = '$id'";
  $query = $con -> query($statement);
  $data = array();
  if($query){
    foreach($query as $key) {
      $data['tableID'] = $key['tableID'];
      $data['clientID'] = $key['clientID'];
      $data['reserveDate'] = $key['reserveDate'];
      $data['reserveTime'] = $key['reserveTime'];
      $data['reserveBlock'] = $key['reserveBlock'];
      $data['reserveAmount'] = $key['reserveAmount'];
    }
    return $data;
  }
  return false;
}

function getClientsFromReserve($id) {
  $con = connect();
  $statement = "SELECT clientID,clientVorname,clientName,clientMail,clientTNR FROM rClient WHERE reserveID = '$id'";
  $query = $con -> query($statement);
  $data = array();

  if($query){
    $r=1;
    foreach ($query as $key) {
      $data[$r]['clientID'] = $key['clientID'];
      $data[$r]['clientVorname'] = $key['clientVorname'];
      $data[$r]['clientName'] = $key['clientName'];
      $data[$r]['clientMail'] = $key['clientMail'];
      $data[$r]['clientTNR'] = $key['clientTNR'];
      $r++;
    }
    return $data;
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

function reserveTable($tID,$cID,$rDate,$rT,$rB,$rA,$rC) {
  $con = connect();
  $p = "INSERT INTO rReserve (reserveID, tableID, clientID, reserveDate, reserveTime, reserveBlock, reserveAmount, reserveCookie, reserveState) VALUES (null,'$tID','$cID','$rDate','$rT','$rB','$rA','$rC','0')";
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

function createClient($cID,$rID,$vorname,$name,$mail,$tnr,$cf) {
  $con = connect();
  $date = echoDateTime();
  $p="INSERT INTO rClient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$tnr','$date','$cf')";
  $query = $con -> query($p) or die();
  if($query === TRUE){
    return true;
  }
  return false;
}

function getTimeBlockTime($id) {
  $con = connect();
  $query= $con->query("SELECT timeStart, timeEnd FROM rTime WHERE timeActive = 1 AND timeID = $id");
  if($query){
    foreach ($query as $key) { return $key['timeStart'] . " - " . $key['timeEnd']; }
  }
  return false;
}
function getTimeBlocks() {
  $con = connect();
  $query= $con->query('SELECT timeStart, timeEnd, timeActive FROM rTime');
  if($query){
    $arr = array(); $r=0;
    foreach ($query as $key) {
      $arr[$r] = $key['timeStart']; $arr[$r] = $key['timeEnd']; $r++;
    }
  }
  return false;
}



function getTableData($date) {
  $con = connect();
  $query = $con -> query("SELECT * FROM rTable") or die();
  $data = array();

  // Überprüfe ob Datum größer als 6 Wochen in Zukunft
  $dateEnd = date('Y-m-d', strtotime('+6 week'));
  if($date > $dateEnd){ return false; }

  if($query){
    $c = 0;
    $ampelTables = loadAmpelTables($date);
    foreach ($query as $table) {
      $data[$c]['tableID'] = $table['tableID']; $data[$c]['tableType'] = $table['tableType'];
      $data[$c]['tableMax'] = $table['tableMax']; $data[$c]['tableMin'] = $table['tableMin'];
      $data[$c]['tableCode'] = $table['tableCode']; $data[$c]['tableActive'] = $table['tableActive'];

      // Wenn tableID in Array von ampelTables enthalten ist, dann sind Reservierungen für Tisch vorhanden
      $ampelBlocks = 0;
      foreach ($ampelTables as $key) {  // Für jeden Tisch im AmpelArray
        // Wenn der Tisch aus Datenbank in Array von Ampelsystem vorhanden, Anzahl abspeichern
        if($key['table'] == $table['tableID']){ $ampelBlocks++; }
      }
      if($ampelBlocks >= 2){ $data[$c]['tableActive'] = "closed"; }

      $c++;
    }
    return $data;
  }
  return false;
}

function getTableDataID($id, $date) {
  $con = connect();
  $queryTable = $con -> query("SELECT * FROM rTable WHERE tableID = '$id'") or die();
  $queryTime = $con -> query("SELECT timeID, timeStart, timeEnd FROM rTime WHERE timeActive = 1") or die();
  $data = array();

  if($queryTable && $queryTime){
    $ampelTable = loadAmpelForTable($id, $date);
    // Lade queryTime in Array timeData
    $count = 1;
    foreach ($queryTime as $keyTime) { $timeData[$count]['timeStart'] = $keyTime['timeStart']; $timeData[$count]['timeEnd'] = $keyTime['timeEnd']; $count++; }
    foreach ($queryTable as $table) { // Tisch mit TischID erhalten
      $data['tableID'] = $table['tableID'];
      $data['tableType'] = $table['tableType'];
      $data['tableMax'] = $table['tableMax'];
      $data['tableMin'] = $table['tableMin'];
      $data['tableCode'] = $table['tableCode'];
      $data['tableActive'] = $table['tableActive'];
      $data['tableReserved'] = "open";

      // ausgewählter Tisch ist in Ampel Array enthalten
      if(sizeof($ampelTable) >= 1){
        $ampelBlock = 0;
        ini_set('display_errors','off');
        foreach ($ampelTable as $key) {  // Für jeden Tisch im AmpelArray
          if($key['table'] == $table['tableID']){
            // Wenn Zeit Jetzt größer als Startzeit von Block und kleiner als Endzeit von Block (Uhrzeit gerade in Blockzeit)
            if(echoTime() > $timeData[$key['block']]['timeStart'] && echoTime() < $timeData[$key['block']]['timeEnd']){
              $data['tableReserved'] = "closed";
            }
            $ampelBlock++;
          }
        }
        ini_set('display_errors','on');
        // Wenn an einem Tag mehr als und oder 2 Blöcke reserviert sind. Tisch für Tag auf belegt setzen
        if($ampelBlock >= 2){ $data['tableReserved'] = "closed"; }
      }
    }
    return $data;
  }
  return false;
}

function getTableReserveTime($tableID, $date) {
  $con = connect();
  $p = "SELECT rReserve.reserveID,rReserve.reserveDate,rReserve.reserveTime,rReserve.reserveBlock,rReserve.reserveState,rReserve.clientID,clientConfirm,clientName FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE rReserve.tableID = $tableID AND reserveDate = '$date' ORDER BY reserveBlock ASC";
  $query = $con -> query($p) or die();
  $data = array();

  if($query){
    $r=0;
    foreach ($query as $key) {
      $data[$r]['rID'] = $key['reserveID'];
      $data[$r]['rDate'] = $key['reserveDate'];
      $data[$r]['rT'] = $key['reserveTime'];
      $data[$r]['rB'] = $key['reserveBlock'];
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
function checkReserveTime($block,$date,$tableID) {
  $con = connect();
  $p = "SELECT reserveBlock FROM rReserve WHERE reserveDate LIKE '$date' AND tableID = $tableID AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)";
  $query = $con->query($p) or die();

  if($query){
    $arr = array(); $r=0;
    foreach ($query as $key) {
      $arr[$r] = $key['reserveBlock'];
      $r++;
    }
    // Wenn beide Blöcke reserviert sind
    if(sizeof($arr) == 2){ return false; }

    for ($i = 0; $i < sizeof($arr); $i++) {
      // Block in Datenbank ist gleich Block ausgewählt, heißt Block nicht frei
      if($arr[$i] == $block){ return false; }
    }
    return true;
  }
  return true;
}


?>
