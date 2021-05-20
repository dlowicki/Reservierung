<?php
  require('../sync.php');
  $con = connect();
  $query = $con->query('SELECT * FROM rTime WHERE timeActive = 1');
  if($query){
    $r=0; $arr=array();
    foreach ($query as $key) {
      $arr[$r]['id'] = $key['timeID'];
      $arr[$r]['start'] = $key['timeStart'];
      $arr[$r]['end'] = $key['timeEnd'];
      $arr[$r]['active'] = $key['timeActive'];
      $r++;
    }
    echo json_encode($arr);
  }
?>
