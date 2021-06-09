<?php
require_once("script.admin.php");
require_once("sync-admin.php");

if(!isset($_COOKIE['rSession'])){ return; }
if(isAdmin() != true){ return; }


function getAnalyseBlockzeiten($type){
  $db = new Overview();
  $con = $db->connectDatabase();
  $sql = "";
  switch ($type) {
    case 'Heute':
      $dt = date("Y-m-d");
      $sql = "SELECT reserveBlock FROM rreserve WHERE reserveDate = '$dt'";
      break;
    case '1 Woche':
      $dt = date("Y-m-d");
      $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
      $sql = "SELECT reserveBlock FROM rreserve WHERE reserveDate < '$dt' and reserveDate > '$date'";
      break;
    case '1 Monat':
      $dt = date("Y-m-d");
      $date = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
      $sql = "SELECT reserveBlock FROM rreserve WHERE reserveDate < '$dt' and reserveDate > '$date'";
      break;
    default:
      $sql = "SELECT reserveBlock FROM rreserve WHERE reserveDate = '$type'";
      break;
  }
  $query = $con->query($sql);
  if($query){
    $arr=array();$block1=1;$block2=1;
    foreach ($query as $key) {
      if($key['reserveBlock'] == '1'){ $arr[0] = $block1; $block1++; } else { $arr[1] = $block2; $block2++; }
    }
    return $arr;
  }
  return false;
}



function getAnalyseReservierungen(){
  $db = new Overview();
  $con = $db->connectDatabase();
  $year = date("Y");
  $sql = "SELECT reserveDate FROM rreserve WHERE reserveDate LIKE '$year%'";
  $query = $con->query($sql);

  if($query){
    $a=array();$ja=0;$fe=0;$mae=0;$apr=0;$mai=0;$jun=0;$jul=0;$aug=0;$sep=0;$okt=0;$nov=0;$dez=0;
    foreach ($query as $key) {
      switch (explode('-',$key['reserveDate'])[1]) {
        case '01':
          $ja++; $a[0]=$ja;
          break;
        case '02':
          $fe++; $a[1]=$fe;
          break;
        case '03':
          $mae++; $a[2]=$mae;
          break;
        case '04':
          $apr++; $a[3]=$apr;
          break;
        case '05':
          $mai++; $a[4]=$mai;
          break;
        case '06':
          $jun++; $a[5]=$jun;
          break;
        case '07':
          $jul++; $a[6]=$jul;
          break;
        case '08':
          $aug++; $a[7]=$aug;
          break;
        case '09':
          $sep++; $a[8]=$sep;
          break;
        case '10':
          $okt++; $a[9]=$okt;
          break;
        case '11':
          $nov++; $a[10]=$nov;
          break;
        case '12':
          $dez++; $a[11]=$dez;
          break;
      }
    }
    return $a;
  }
  return false;
}

function getAnalyseTage($type){
  $db = new Overview();
  $con = $db->connectDatabase();
  $sql = "";
  switch ($type) {
    case 'Heute':
      return false;
      break;
    case '1 Woche':
      $dt = date("Y-m-d");
      $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
      $sql = "SELECT reserveDate FROM rreserve WHERE reserveDate < '$dt' and reserveDate > '$date'";
      break;
    case '1 Monat':
      $dt = date("Y-m-d");
      $date = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
      $sql = "SELECT reserveDate FROM rreserve WHERE reserveDate < '$dt' and reserveDate > '$date'";
      break;
  }
  $query = $con->query($sql);

  if($query){
    $a=array(); $m0=0;$m1=0;$m2=0;$m3=0;$m4=0;$m5=0;$m6=0;
    foreach ($query as $key) {
      $wochentag = date('w', strtotime($key['reserveDate']));
      switch ($wochentag) {
        case '0':
        $a[0]=$m0++;
        break;
        case '1':
        $a[1]=$m1++;
        break;
        case '2':
        $a[2]=$m2++;
        break;
        case '3':
        $a[3]=$m3++;
        break;
        case '4':
        $a[4]=$m4++;
        break;
        case '5':
        $a[5]=$m5++;
        break;
        case '6':
        $a[6]=$m6++;
        break;
      }
    }
    return $a;
  }
  return false;
}

?>
