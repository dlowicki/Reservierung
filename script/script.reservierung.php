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

  public function loadReservierungenList() {
    $db = new Overview();
    $con = $db->connectDatabase();
    $statement = "SELECT reserveID, reserveStart, reserveEnd, reserveState FROM rReserve WHERE tableID='$this->tID' AND reserveDate='$this->rDate' ORDER BY reserveStart ASC";
    $query = $con->query($statement) or die();
    if($query){
      $data = array();
      $t=0;
      foreach ($query as $key) {
        $data[$t]['rID'] = $key['reserveID'];
        $data[$t]['rStart'] = $key['reserveStart'];
        $data[$t]['rEnd'] = $key['reserveEnd'];
        $data[$t]['rState'] = $key['reserveState'];
        $t++;
      }
      return $data;
    }
    return false;
  }
}

?>
