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
    if ($conn->connect_error) {   // Check connection
        die("Connection failed: " . $conn->connect_error);
        return false;
    }
    return $conn;
  }

  function getOverview($type, $day) {
    $con = $this->connectDatabase();
    $para = "rReserve.reserveID,rReserve.tableID,reserveDate,reserveStart,reserveEnd,reserveDuration,reserveAmount,clientName,clientTNR";
    if($type == "Day"){
      $query = $con -> query("SELECT $para FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE reserveDate = '$day' ORDER BY reserveDate ASC");
    } else {
      $date = new DateTime($day);
      $date->modify('+7 day');
      $newDay = $date->format('Y-m-d');
      $query = $con -> query("SELECT $para FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE reserveDate BETWEEN '$day' AND '$newDay' ORDER BY reserveDate ASC");
    }

      if($query){
        $data = array();
        $s=0;
        foreach ($query as $key) {
          $data[$s]['rID'] = $key['reserveID'];
          $data[$s]['tID'] = $key['tableID'];
          $data[$s]['rStart'] = $key['reserveStart'];
          $data[$s]['rEnd'] = $key['reserveEnd'];
          $data[$s]['rDate'] = $key['reserveDate'];
          $data[$s]['rDuration'] = $key['reserveDuration'];
          $data[$s]['rA'] = $key['reserveAmount'];
          $data[$s]['cName'] = $key['clientName'];
          $data[$s]['cTNR'] = $key['clientTNR'];
          $s++;
        }
        return $data;
      }
    return false;
  }

}

?>
