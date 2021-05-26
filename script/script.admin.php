<?php
/**
 *
 */
class Overview
{
  private $servername = "localhost";
  private $username = "shop";
  private $password = "123456";
  private $db = "reservierung";

  function connectDatabase() {
    $conn = new mysqli($this->servername, $this->username, $this->password, $this->db); // Create connection
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); return false; }
    return $conn;
  }

  public function getOverview($type, $day) {
    $con = $this->connectDatabase();
    $para = "rreserve.reserveID,rreserve.tableID,reserveDate,reserveTime,reserveAmount,clientName,clientTNR";
    if($type == "Day"){
      $query = $con -> query("SELECT $para FROM rreserve INNER JOIN rclient ON rreserve.clientID=rclient.clientID WHERE reserveDate = '$day' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5) ORDER BY reserveDate ASC");
    } else {
      $date = new DateTime($day);
      $date->modify('+7 day');
      $newDay = $date->format('Y-m-d');
      $query = $con -> query("SELECT $para FROM rreserve INNER JOIN rclient ON rreserve.clientID=rclient.clientID WHERE reserveDate BETWEEN '$day' AND '$newDay' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5) ORDER BY reserveDate ASC");
    }

      if($query){
        $data = array();
        $s=0;
        foreach ($query as $key) {
          $data[$s]['rID'] = $key['reserveID'];
          $data[$s]['tID'] = $key['tableID'];
          $data[$s]['rDate'] = $key['reserveDate'];
          $data[$s]['rA'] = $key['reserveAmount'];
          $data[$s]['cName'] = $key['clientName'];
          $data[$s]['cTNR'] = $key['clientTNR'];
          $s++;
        }
        return $data;
      }
    return false;
  }

    public function getBlacklist() {
    $con = $this->connectDatabase();
    $query = $con->query("SELECT * FROM rblacklist") or die();

    if($query){
      $data = array();
      $r=0;
      foreach ($query as $key) {
        $data[$r]['id'] = $key['bID'];
        $data[$r]['mail'] = $key['bMail'];
        $data[$r]['tnr'] = $key['bTNR'];
        $data[$r]['amount'] = $key['bAnzahl'];
        $r++;
      }
      return $data;
    }
    return false;
  }

  public function getNoShow() {
    $con = $this->connectDatabase();
    $statement = "SELECT nsID, nsMail, nsTNR, nsAmount, nsDate FROM rnoshow";
    $query = $con->query($statement) or die();

    if($query){
      $data = array();
      $r=0;
      foreach ($query as $key) {
        $data[$r]['id'] = $key['nsID'];
        $data[$r]['mail'] = $key['nsMail'];
        $data[$r]['tnr'] = $key['nsTNR'];
        $data[$r]['amount'] = $key['nsAmount'];
        $data[$r]['time'] = $key['nsDate'];
        $r++;
      }
      return $data;
    }
    return false;
  }

  public function getNoShowWithMailAndNumber($mail, $tnr) {
    $con = $this->connectDatabase();
    $statement = "SELECT nsID, nsAmount, nsDate FROM rnoshow WHERE nsMail = '$mail' OR nsTNR = '$tnr'";
    $query = $con->query($statement) or die();

    if($query){
      $data = array();
      foreach ($query as $key) {
        $data['id'] = $key['nsID'];
        $data['amount'] = $key['nsAmount'];
        $data['time'] = $key['nsDate'];
      }
      return $data;
    }
    return false;
  }

  public function updateListe($id,$amount,$mail,$tnr,$type) {
    $con = $this->connectDatabase();
	if($type == "b"){
		$statement = "UPDATE rblacklist SET bMail = '$mail', bAnzahl = '$amount', bTNR = '$tnr' WHERE bID = '$id'";
	} elseif($type == "ns"){
		$statement = "UPDATE rnoshow SET nsMail = '$mail', nsAmount = '$amount', nsTNR = '$tnr' WHERE nsID = '$id'";
	}
    $query = $con->query($statement) or die();
    if($query === TRUE){ return true; } return false;
  }

  public function createListeRow($type) {
    $con = $this->connectDatabase();
	if($type == "b"){
		$statement = "INSERT INTO rblacklist (bID, bMail, bTNR, bAnzahl) VALUES (null, 'mail@domain.de','0','1')";
	} elseif($type == "ns"){
		$time = date('Y-m-d');
		$statement = "INSERT INTO rnoshow (nsID, nsMail, nsTNR, nsAmount, nsDate) VALUES (null, 'mail@domain.de','0','1','$time')";
	}
    $query = $con->query($statement) or die();
    if($query === TRUE){ return true; } return false;
  }

  public function deleteListeRow($id, $type) {
    $con = $this->connectDatabase();
	if($type == "b"){
		$statement = "DELETE FROM rblacklist WHERE bID = '$id'";
	} elseif($type == "ns"){
		$statement = "DELETE FROM rnoshow WHERE nsID = '$id'";
	}
    $query = $con->query($statement) or die();
    if($query === TRUE){ return true; } return false;
  }

  public function loadTables() {
    $con = $this->connectDatabase();
    $query = $con -> query('SELECT tableID, tableMax, tableMin, tablePlace, tableActive FROM rtable');
    if($query){
      $arr=array(); $r=0;
      foreach ($query as $key) {
        $arr[$r]['tableID'] = $key['tableID'];
        $arr[$r]['tableMax'] = $key['tableMax'];
        $arr[$r]['tableMin'] = $key['tableMin'];
        $arr[$r]['tablePlace'] = $key['tablePlace'];
        $arr[$r]['tableActive'] = $key['tableActive'];
        $r++;
      } return $arr;
    } return false;
  }

  public function loadBlacklist() {
    $con = $this->connectDatabase();
    $query = $con -> query('SELECT * FROM rblacklist');
    if($query){
      $arr=array(); $r=0;
      foreach ($query as $key) {
        $arr[$r]['bID'] = $key['bID'];
        $arr[$r]['bMail'] = $key['bMail'];
        $arr[$r]['bTNR'] = $key['bTNR'];
        $arr[$r]['bAnzahl'] = $key['bAnzahl'];
        $r++;
      } return $arr;
    } return false;
  }

  public function loadDays() {
  $con = $this->connectDatabase();
  $query = $con -> query('SELECT * FROM rdays');
  if($query){
    $arr=array(); $r=0;
    foreach ($query as $key) {
      $arr[$r]['id'] = $key['daysID'];
      $arr[$r]['day'] = $key['daysDay'];
      $arr[$r]['time'] = $key['daysTime'];
      $arr[$r]['active'] = $key['daysActive'];
      $r++;
    } return $arr;
  } return false;
  }

  public function loadSpecialDays() {
  $con = $this->connectDatabase();
  $query = $con -> query('SELECT * FROM rspecial WHERE spDate >= CURDATE() ORDER BY spDate ASC');
  if($query){
    $arr=array(); $r=0;
    foreach ($query as $key) {
      $arr[$r]['id'] = $key['spID'];
      $arr[$r]['type'] = $key['spType'];
      $arr[$r]['name'] = $key['spName'];
      $arr[$r]['date'] = $key['spDate'];
      $r++;
    } return $arr;
  } return false;
  }

}

?>
