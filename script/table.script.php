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

  function getDatabaseDay() {
    $con = $this->connectDatabase();
    $para = "rReserve.reserveID,rReserve.tableID,reserveStart,reserveEnd,reserveDuration,reserveAmount,clientName,clientTNR";
    $query = $con -> query("SELECT $para FROM rReserve INNER JOIN rClient ON rReserve.clientID=rClient.clientID WHERE reserveTime LIKE '$date %' ORDER BY reserveTime ASC");

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

    return false;
  }

}

?>
