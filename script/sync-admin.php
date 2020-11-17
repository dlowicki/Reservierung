<?php
require_once("script.admin.php");
/*
* Created 17.11.2020
* Mail und ID = Unique
*
*/
if(isset($_POST['nsAmount']) && isset($_POST['nsMail']) && isset($_POST['nsTime'])&& isset($_POST['nsID'])){
  $id = trim($_POST['nsID']); // Erhalte ID von POST (trim)
  $amount = trim($_POST['nsAmount']); // Erhalte Anzahl von POST (trim)
  $mail = trim($_POST['nsMail']); // Erhalte neue Mail von POST (trim)
  $time = trim($_POST['nsTime']); // Erhalte neue Zeit von POST (trim)
  // Wenn Alle Variablen gesetzt sind
  if(strlen($id) >= 1 && strlen($amount) >= 1 && strlen($mail) >= 3 && strlen($time) >= 3){
    $overview = new Overview();
    $update = $overview->updateNoShow($id,$amount,$mail,$time);
    if($update){
      echo "1";
      return;
    }
  }
  echo "0";
  return;
}

if(isset($_POST['nsEdit'])){
  $edit = trim($_POST['nsEdit']);
  if(strlen($edit) >= 1){
    $overview = new Overview();
    $result;
    if($edit != 'Create'){
      $result = $overview->deleteNoShow($edit);
    } else {
      $result = $overview->createNoShow();
    }

    if($result){ echo "1"; return;}
    echo "0"; return;
  }
}

?>
