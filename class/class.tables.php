<?php
require_once('class.database.php');
/**
 * Klasse loadTables
 */
class Tables extends Database
{
  private $type;
  private $date;
  private $blocktime;
  private $amount;
  private $tischplan = array('24/1','24/2','25/1','25/2','26/1','26/2','27/1','27/2','54/1','54/2','53/1','53/2','52/1','52/2','51/1','51/2');
  private $tischplan2 = array('24','25','26','27','54','53','52','51');

  function __construct($type, $date, $blocktime, $amount)
  {
    $this->type = $type;
    $this->date = $date;
    $this->blocktime = $blocktime;
    $this->amount = $amount;
  }

  public function getTables()
  {
    $con = $this->getConnection();
    $query = $con->query("SELECT * FROM rtable") or die();

    if($query)
    {
      $c = 0;
      $reservedTables = $this->getReservedTables($this->date); // Erhalte alle Tische und Blocksätze die am Datum belegt sind

      foreach ($query as $table)
      {
        switch ($this->type)
        {
          case 'tischplan':
          if(in_array($table['tableName'],$this->tischplan))
          {
            continue 2;
          }
            break;
          case 'tischplan2':
          if(in_array($table['tableName'],$this->tischplan2))
          {
            continue 2;
          }
            break;
          default:
            return false;
            break;
        }
        $data[$c]['tableID'] = $table['tableID'];
        $data[$c]['tableName'] = $table['tableName'];
        $data[$c]['tableMax'] = $table['tableMax'];
        $data[$c]['tableMin'] = $table['tableMin'];
        $data[$c]['tableCode'] = $table['tableCode'];
        $data[$c]['tableActive'] = $table['tableActive'];
        $data[$c]['width'] = $table['tableWidth'];
        $data[$c]['height'] = $table['tableHeight'];
        $data[$c]['x'] = $table['tableX'];
        $data[$c]['y'] = $table['tableY'];


        $reservierungCount = 0;
        $blockReserved = 0;

        foreach ($reservedTables as $key) // Für jeden Tisch im AmpelArray
        {
          // Wenn Tisch Datenbank gleich Tisch aus getReservedTables (Reservierung vorhanden)
          if($key['table'] == $table['tableID'])
          {
            $reservierungCount++; // Reservierung Count für Tisch erhöhen
            if($key['block'] == $bs) // Wenn Blockzeit von Reservierung gleich Blockzeit Client ausgewählt
            {
              $blockReserved = 1; // Tisch als geblockt markieren
            }
          }
        }
        // Wenn ANZAHL Reservierung für Tag größer gleich 2 oder Tisch schon geblockt
        if($reservierungCount >= 2 || $blockReserved == 1)
        {
          $data[$c]['tableActive'] = "closed";
        }
        // Wenn MAX ANZAHL kleiner als AMOUNT oder MIN ANZAHL GRÖßER ALS AMOUNT = deaktivieren
        if($table['tableMax'] < $this->amount || $table['tableMin'] > $this->amount)
        {
          $data[$c]['tableActive'] = "closed";
        }

        $c++;
      }
      return $data;
    }
  }

  public function getReservedTables($date)
  {
    $con = $this->getConnection();
    $query = $con->query("SELECT tableID, reserveBlock FROM rreserve WHERE reserveDate LIKE '$date' AND (reserveState = 0 OR reserveState = 1 OR reserveState = 5)");
    if($query)
    {
      $row = 1;
      $arr = array();
      ini_set('display_errors','off');
      foreach ($query as $key) {
        // Wenn Reservierung mehrmals an einem Tag, nur einmal abspeichern in Array
        if(in_array($key['tableID'], $arr[$row]) == false){
          $arr[$row]['table'] = $key['tableID'];
          $arr[$row]['block'] = $key['reserveBlock'];
        } /*else {
          $arr[$row]['table'] = $key['tableID'];
          $arr[$row]['block'] = $key['reserveBlock'];
        }*/
        $row++;
      }
      ini_set('display_errors','on');
      return $arr;
    }
    return false;
  }

  private function checkDate($date)
  {
    // Überprüfe ob Datum größer als 6 Wochen in Zukunft
    $dateEnd = date('Y-m-d', strtotime('+6 week'));
    if($date > $dateEnd)
    {
      return false;
    }
  }

  public function getType()
  {
    return $this->type;
  }

  public function setType($type)
  {
    $this->type = $type;
  }
}
?>
