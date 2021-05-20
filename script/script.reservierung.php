<?php
require_once("script.admin.php");
/**
 *
 */
class Reservierung extends Overview {
  private $tID;
  private $rDate;

  function __construct($tID, $rDate) {
    $this->tID = $tID;
    $this->rDate = $rDate;
  }

  public function tableExists() {
    $db = new Overview();
    $con = $db->connectDatabase();
    $query = $con->query("SELECT tableID FROM rTable WHERE tableID = '$this->tID'");
    if(mysqli_num_rows($query)==1){
      return true;
    }
    return false;
  }

  public function loadReservierungenList() {
    $db = new Overview();
    $con = $db->connectDatabase();
    $statement = "SELECT reserveID, reserveTime, reserveState FROM rReserve WHERE tableID='$this->tID' AND reserveDate='$this->rDate' ORDER BY reserveBlock ASC";
    $query = $con->query($statement) or die();
    if($query){
      $data = array();
      $t=0;
      foreach ($query as $key) {
        $data[$t]['rID'] = $key['reserveID'];
        $data[$t]['rTime'] = $key['reserveTime'];
        $data[$t]['rState'] = $key['reserveState'];
        $t++;
      }
      return $data;
    }
    return false;
  }
}

?>
