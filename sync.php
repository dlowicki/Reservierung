<?php
require_once('mail/sendmail.php');
require_once('class/class.admin.php');

/*
$servername = "127.0.0.1:3306";
$username = "w10072res";
$password = "jHwsa2rr";
$db = "w10072res";

jk2R_6X
*/


if(isset($_POST['loadTableID']) && isset($_POST['loadTableDate'])){
  $data = getTableDataID($_POST['loadTableID'], $_POST['loadTableDate']);
  echo json_encode($data);
  return;
}

if(isset($_GET['getReservierungen'])){
  $data = explode(';',$_GET['getReservierungen']);
  if(strlen($data[0]) > 0 && strlen($data[1] > 0)){
	$r = getTableReserveTime($data[0],$data[1]); // 0 = TableID, 1 = Datum
	if($r){ echo json_encode($r); }
	return;
  }
}

if(isset($_POST['confirmDay'])){
   // Wenn return true dann Restaurant am Tag geschlossen
  if(checkDayActive($_POST['confirmDay']) || checkEventOnDay($_POST['confirmDay'])){ echo '1'; return; } else { echo '0'; return; }
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

function getTischplan($type){
  $con = connect();
  $query = $con->query("SELECT sWert FROM rsettings WHERE sEinstellung='$type'");
  if($query){ foreach ($query as $ke) { return $ke['sWert']; } }
}

function loadAmpelTables($date) {
  $con = connect();
  $query = $con->query("SELECT tableID, reserveBlock FROM rreserve WHERE reserveDate LIKE '$date' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)");
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

function loadAmpelFortable($tableID, $date) {
  $con = connect();
  $query = $con->query("SELECT tableID, reserveBlock FROM rreserve WHERE reserveDate LIKE '$date' AND tableID = $tableID AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)");
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

if(isset($_POST['hubName']) && isset($_POST['hubSecure'])){
  if(r($_POST['hubName']) == false && r($_POST['hubSecure']) == false){
    $con = connect(); $name=$_POST['hubName']; $pw = $_POST['hubSecure'];
    $query = $con->query("SELECT userName, userPW FROM ruser WHERE userName = '$name' AND userActive = '1'");

    if($query){
      foreach ($query as $key) {
        if($key['userName'] == $name && $key['userPW'] == $pw){
          $cookie = md5(uniqid()); $ip = getIP();
          $query2 = $con->query("UPDATE ruser SET userCookie = '$cookie', userIP = '$ip' WHERE userName = '$name'");
          if($query2===TRUE){
            setcookie("rSession", $cookie, time()+86400, "/"); // Für einen Tag
            echo "1";
            return;
          }
          echo "0";
          return;
        }
      }
    }

  }
  echo "0";
  return;
}

if(isset($_POST['userCheck'])){
  $con = connect(); $cookieUser = $_POST['userCheck'];
  $query = $con->query("SELECT userCookie FROM ruser WHERE userCookie = '$cookieUser'");
  if($query){ foreach ($query as $key) { if($key['userCookie'] == $cookieUser) { echo md5($key['userCookie']); return; } } }
  echo "0"; return;
}

if(isset($_POST['createReserve'])){
  $daten = $_POST['createReserve'];
  $amount = $daten[0];  // Anzahl von Personen z.B. 5
  $date = $daten[1];    // Genaues Datum der Reservierung 2021-05-20

  // checkDayActive returned 1 Wenn ausgewählter Tag geschlossen ist
  if(checkDayActive($date) || in_array($date, getFeiertage())){ echo '0'; return; }

  $block = $daten[2];   // BlockID z.B. 1
  $table = $daten[3];   // TischID 9

  $hhExist = false;
  for ($i=0; $i < 5; $i++) { // Schleife, die 5 mal durchlaufen wird
    if(array_key_exists($i,$daten[4])==false){ continue; } // Wenn $i in array nicht existiert
    $exp = explode(";",$daten[4][$i]);
    // Wenn Anzahl von Count == 4 und alle splitts Längen größer als 0
	if(count($exp) == 4){  if(strlen($exp[0])>0 && strlen($exp[1])>0 && strlen($exp[2])>0 && strlen($exp[3])>0){ $hhExist = true; } }
  if(!filter_var($exp[2], FILTER_VALIDATE_EMAIL)){ echo '4'; return; }
  }
  // $hhExist == false, kein Haushalt wurde eingetragen
  if($hhExist == false){ echo "0"; return; }

	//Überprüfe ob NoShow Eintrag vorhanden oder nicht
	$nsClient = explode(";",$daten[4][0]); // Erhalte E-Mail von Client0
	$overview = new Overview();
  // Wenn NoShow von client vorhanden dann return false mit echo 2
	$noshow = $overview->getNoShowWithMailAndNumber($nsClient[2], $nsClient[3]);
	if($noshow){ if($noshow['amount'] >= 2){ echo '2'; return false; } }

	// checkReserveTime = Überprüfe ob Reservierung für Block bereits vorhanden | return false wenn belegt
  if(checkReserveTime($block,$date,$table)){
	$clientID = uniqid(); $rCookie = md5(uniqid(rand (),true)); $mailTO = "";
    // $tID,$cID,$rDate,$rTime,$rD,$rA
    $rID = reserveTable($table,$clientID,$date,getTimeBlockTime($block),$block,$amount,$rCookie);
    if($rID != false){  // $rID wurde erfolgreich erstellt --> Reservierung eingetragen
      $dateToday = echoDateTime(); $count = 0; $con = connect();
	  if($daten[4][0] != null){ $mailTO = explode(";",$daten[4][0])[2]; } // Erhalte Mail von Client0
      foreach ($daten[4] as $key => $value) { // Für jeden Datensatz (Haushalt)
        if($count != 0){ $clientID = uniqid(); }
        if($value){ // Wenn Wert von Haushalt existiert
          $exp = explode(";",$value);
          $cf = uniqid()."".uniqid();
          $vorname = trim($exp[0]); $name = trim($exp[1]); $mail = trim($exp[2]); $tnr = trim($exp[3]);
          // $cID,$rID,$tID,$vorname,$name,$mail,$adresse,$tnr,$cf
          $sqlStatement = "INSERT INTO rclient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$clientID','$rID','$vorname','$name','$mail','$tnr','$dateToday','$cf');";
          $query = $con -> query($sqlStatement) or die();
          if($query !== TRUE){ echo "0"; return; }
        }
        $count++;
      }
      // Clients wurden erfolgreich erstellt. Versende Mail
      if(sendmail($table,$date, getTimeBlockTime($block),$amount, trim($mailTO))){ echo "1"; return; }
      echo "3"; return; // 3 = Reservierung angelegt aber E-Mail konnte nicht verschickt werden
    }
  } echo "0"; return;
}



if(isset($_POST['remove'])){
  $con = connect();
  $cookie = htmlspecialchars($_COOKIE['rSession']);
  $clientConfirm = $_POST['remove'];
  $query=$con->query("SELECT userActive FROM ruser WHERE userCookie = '$cookie'");
  if($query){
    foreach ($query as $key) {
      if($key['userActive'] == "1"){
        $query2 = $con->query("DELETE rclient, rreserve FROM rclient INNER JOIN rreserve ON rclient.clientID = rreserve.clientID WHERE rclient.clientConfirm = '$clientConfirm'");
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
  $query = $con->prepare("SELECT tableID FROM rtable WHERE tableCode = ?");
  $query->bind_param('s',$_POST['qrCode']);
  $query->execute();

  $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);
  if(isset($row)){ if(sizeof($row) >=1 ){ echo $row['tableID']; return; } } echo "0"; return;
}

if(isset($_POST['changeReserve'])){
  $id = $_POST['changeReserve'];
  $con = connect();
  $query = $con->query("SELECT reserveID, tableID, reserveTime FROM rreserve WHERE reservecookie = '$id'");
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
        $sqlStatement = "INSERT INTO rclient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$tnr','$dateTime','$cf');";
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
  $statement = "SELECT tableActive FROM rtable WHERE tableID = '$id' LIMIT 1";
  $query = $con -> query($statement);
  $query = $query->fetch_array(MYSQLI_ASSOC);
  if($query['tableActive']){ echo $query['tableActive']; return; }
  echo "0";
  return;
}

if(isset($_POST['setTableActive']) && isset($_POST['value'])){
  $id = $_POST['setTableActive'];
  $val = $_POST['value'];
  if($val == "true"){ $val = "open"; } else { $val = "closed"; }
  $con = connect();
  $statement = "UPDATE rtable SET tableActive = '$val' WHERE tableID = '$id'";
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
  $statement = "SELECT tableID, clientID, reserveDate, reserveTime, reserveBlock, reserveAmount FROM rreserve WHERE reserveID = '$id'";
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
  $statement = "SELECT clientID,clientVorname,clientName,clientMail,clientTNR FROM rclient WHERE reserveID = '$id'";
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
  /*
  $servername = "127.0.0.1:3306";
$username = "w10072res";
$password = "jHwsa2rr";
$db = "w10072res";*/

  $conn = new mysqli($servername, $username, $password, $db); // Create connection

  if ($conn->connect_error) {   // Check connection
      die("Connection failed: " . $conn->connect_error);
      return false;
  }
  return $conn;
}

function reserveTable($tID,$cID,$rDate,$rT,$rB,$rA,$rC) {
  $con = connect();
  $p = "INSERT INTO rreserve (reserveID, tableID, clientID, reserveDate, reserveTime, reserveBlock, reserveAmount, reserveCookie, reserveState) VALUES (null,'$tID','$cID','$rDate','$rT','$rB','$rA','$rC','0')";
  $query = $con->query($p) or die();
  if($query === TRUE){
    $p2="SELECT reserveID FROM rreserve ORDER BY reserveID DESC LIMIT 1";
    $query2 = $con->query($p2) or die();
    if($query2){ foreach ($query2 as $key) { return $key['reserveID']; } }
    return false;
  }
  return false;
}

function createClient($cID,$rID,$vorname,$name,$mail,$tnr,$cf) {
  $con = connect();
  $date = echoDateTime();
  $p="INSERT INTO rclient (clientID, reserveID, clientVorname, clientName, clientMail, clientTNR, clientDate, clientConfirm) VALUES ('$cID','$rID','$vorname','$name','$mail','$tnr','$date','$cf')";
  $query = $con -> query($p) or die();
  if($query === TRUE){
    return true;
  }
  return false;
}

function getTimeBlockTime($id) {
  $con = connect();
  $query= $con->query("SELECT timeStart, timeEnd FROM rtime WHERE timeActive = 1 AND timeID = $id");
  if($query){ foreach ($query as $key) { return $key['timeStart'] . " - " . $key['timeEnd']; } }
  return false;
}
function getTimeBlocks() {
  $con = connect();
  $query= $con->query('SELECT timeID, timeStart, timeEnd, timeActive FROM rtime');
  if($query){
    $arr = array(); $r=0;
    foreach ($query as $key) {
      $arr[$r]['start'] = $key['timeStart']; $arr[$r]['end'] = $key['timeEnd']; $arr[$r]['id'] = $key['timeID']; $r++;
    }
    return $arr;
  }
  return false;
}

// Übergeben werden Datum und gesuchter Blocksatz
/*function getTableData($date,$bs,$amount) {
  $con = connect();
  $query = $con -> query("SELECT * FROM rtable") or die();
  $data = array();

  // Überprüfe ob Datum größer als 6 Wochen in Zukunft
  $dateEnd = date('Y-m-d', strtotime('+6 week'));
  if($date > $dateEnd){ return false; }

  if($query){
    $c = 0; $ampelTables = loadAmpelTables($date); // Erhalte alle Tische und Blocksätze die am Datum belegt sind
    foreach ($query as $table) {
      $data[$c]['tableID'] = $table['tableID'];
      $data[$c]['tableType'] = $table['tableType'];
      $data[$c]['tableMax'] = $table['tableMax']; $data[$c]['tableMin'] = $table['tableMin'];
      $data[$c]['tableCode'] = $table['tableCode'];
      $data[$c]['tableActive'] = $table['tableActive'];
      $data[$c]['width'] = $table['tableWidth']; $data[$c]['height'] = $table['tableHeight'];
      $data[$c]['x'] = $table['tableX']; $data[$c]['y'] = $table['tableY'];

      // Wenn tableID in Array von ampelTables enthalten ist, dann sind Reservierungen für Tisch vorhanden
      $ampelBlocks = 0; $blockReserved = 0;
      foreach ($ampelTables as $key) {  // Für jeden Tisch im AmpelArray
        // Wenn der Tisch aus Datenbank in Array von Ampelsystem vorhanden, Anzahl abspeichern
        if($key['table'] == $table['tableID']){ $ampelBlocks++; if($key['block'] == $bs){ $blockReserved = 1; } }
      }
      if($ampelBlocks >= 2 || $blockReserved == 1){ $data[$c]['tableActive'] = "closed"; }
      // Wenn MAX ANZAHL kleiner als AMOUNT und MIN ANZAHL GRÖßER ALS AMOUNT = deaktivieren
      if($table['tableMax'] < $amount || $table['tableMin'] > $amount){ $data[$c]['tableActive'] = "closed"; }
      $c++;
    }
    return $data;
  }
  return false;
}*/

function getTableDataID($id, $date) {
  $con = connect();
  $queryTable = $con -> query("SELECT * FROM rtable WHERE tableID = '$id'") or die();
  $queryTime = $con -> query("SELECT timeID, timeStart, timeEnd FROM rtime WHERE timeActive = 1") or die();
  $data = array();

  if($queryTable && $queryTime){
    $ampelTable = loadAmpelFortable($id, $date);
    // Lade queryTime in Array timeData
    $count = 1;
    foreach ($queryTime as $keyTime) { $timeData[$count]['timeStart'] = $keyTime['timeStart']; $timeData[$count]['timeEnd'] = $keyTime['timeEnd']; $count++; }
    foreach ($queryTable as $table) { // Tisch mit TischID erhalten
      $data['tableID'] = $table['tableID'];
      $data['tableName'] = $table['tableName'];
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
// Tote Reservierung = Reservierung existiert aber Client nicht in Datenbank
function getTableReserveTime($tableID, $date) {
  $con = connect();
  $p = "SELECT rreserve.reserveID,rreserve.reserveDate,rreserve.reserveTime,rreserve.reserveBlock,rreserve.reserveState,clientConfirm,clientName FROM rreserve INNER JOIN rclient ON rreserve.clientID=rclient.clientID WHERE rreserve.tableID = $tableID AND reserveDate = '$date' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5) ORDER BY reserveBlock ASC";
  $query = $con -> query($p) or die();
  $data = array();

  if($query){
    $r=0;
    foreach ($query as $key) {
      $data[$r]['rID'] = $key['reserveID'];
      $data[$r]['rDate'] = $key['reserveDate'];
      $data[$r]['rT'] = $key['reserveTime'];
      $data[$r]['rB'] = $key['reserveBlock'];
	  $data[$r]['rState'] = $key['reserveState'];
      $data[$r]['cc'] = $key['clientConfirm'];
      $data[$r]['cn'] = $key['clientName'];
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
// epvvZ3nmA2#HzbTe
 // verschlüsseln
function encrypt($value, $key) { return openssl_encrypt($value, "AES-128-ECB", $key); }
// entschlüsseln
function decrypt($value, $key) {  return openssl_decrypt($value, "AES-128-ECB", $key);
}
function echoDate() { return date("Y-m-d"); }
function echoDateTime() { return date("Y-m-d G:i:s"); }
function echoTime() { return date("G:i"); }

function r($str){
  if(strlen($str) >= 4){
    $re = '/[+\;\/*{}´^<>=%$§#]+/m';
    return preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
  }
  return false;
}

function checkDayActive($date) {
  $names = array('Mon'=>'Montag','Tue'=>'Dienstag','Wed'=>'Mittwoch','Thu'=>'Donnerstag','Fri'=>'Freitag','Sat'=>'Samstag','Sun'=>'Sonntag');
  $nameOfDay = date('D', strtotime($date));
  $con = connect();
  $query = $con->query("SELECT daysDay FROM rdays WHERE daysActive = '0'");
  if($query){
    foreach ($query as $key) { if($names[$nameOfDay] == $key['daysDay']){ return 1; } }
    return 0;
  }
  return 0;
}
function checkEventOnDay($date) {
  $con = connect();
  $query = $con->prepare("SELECT * FROM rspecial WHERE spDate = '$date'");
  $query->execute(); $result = $query->get_result();
  $row = $result->fetch_array(MYSQLI_ASSOC);
  if(sizeof($row) >= 1){ return true; } return false;
}

//https://reservierung.hubraum-durlach.de/script/load.feiertag.php
function getFeiertage(){
  $arr = array();
  $json = json_decode(file_get_contents('http://localhost/html/Reservierung/script/load.feiertag.php'));
  foreach ($json as $key => $value) { array_push($arr, $value->{'date'}); }
  return $arr;
}


// Überprüfe ob Überschneidung mit Reservierung
function checkReserveTime($block,$date,$tableID) {
  $con = connect();
  $p = "SELECT reserveBlock FROM rreserve WHERE reserveDate LIKE '$date' AND tableID = $tableID AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)";
  $query = $con->query($p) or die();

  if($query){
    $arr = array(); $r=0;
    foreach ($query as $key) { $arr[$r] = $key['reserveBlock']; $r++; } // Daten in Array abspeichern
    if(sizeof($arr) == 2){ return false; } // Wenn Anzahl an Blöcke in Datenbank grüßer als 2 (zwei Reserverierung an dem Tag)
    for ($i = 0; $i < sizeof($arr); $i++) { if($arr[$i] == $block){ return false; } } // Block in Datenbank ist gleich Block ausgewählt, heißt Block nicht frei
    return true;
  }
  return true; // Wenn keine Reservierung am besagten Tag gefunden wurde, trotzdem true zurückgeben
}


?>
