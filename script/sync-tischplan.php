<?php
require_once('sync-admin.php');

if(isset($_POST['changeColor'])){
  $farbe = "";
  if(strlen($_POST['changeColor']) >= 1 && strlen($_POST['changeColor']) <= 25){ $farbe = $_POST['changeColor']; } else { echo '0'; return; }
  $db = new Overview();
  $con = $db->connectDatabase();
  $query = $con->query("UPDATE rsettings SET sWert = '$farbe' WHERE sEinstellung='farbe'");
  if($query === TRUE){ echo '1'; return; } echo '0'; return;
}
if(isset($_POST['updateTisch'])){
  $exp = explode(";",$_POST['updateTisch']);
  if(sizeof($exp) == 3){
    $db = new Overview(); $con = $db->connectDatabase();
    $x = $exp[0]; $y = $exp[1]; $id = $exp[2];
    $query = $con->query("UPDATE rtable SET tableX = '$x', tableY= '$y' WHERE tableID='$id'");
    if($query === TRUE){ echo '1'; return; } echo '0'; return;
   } echo '0'; return;
}
if(isset($_POST['saveTisch'])){
  $exp = explode(";",$_POST['saveTisch']);
  if(sizeof($exp) == 6){
    $db = new Overview(); $con = $db->connectDatabase();
    $id=$exp[0];$x=$exp[1];$y=$exp[2];$width=$exp[3];$height=$exp[4];$place=$exp[5];
    $query = $con->query("UPDATE rtable SET tableX = '$x', tableY= '$y', tableWidth='$width', tableHeight='$height', tablePlace='$place' WHERE tableID='$id'");
    if($query === TRUE){ echo '1'; return; } echo '0'; return;
  } echo '0'; return;
}

if(isset($_POST['deleteTisch'])){
  $id = $_POST['deleteTisch'];
  $db = new Overview(); $con = $db->connectDatabase();
  $query = $con->query("DELETE FROM rtable WHERE tableID = '$id'");
  if($query === TRUE){ echo '1'; return; } echo '0'; return;
}

if(isset($_POST['addTisch'])){
  $db = new Overview(); $con = $db->connectDatabase();
  $uniq = md5(uniqid());
  $query = $con->query("INSERT INTO rtable (tableID, tableType, tableMax, tableMin, tableCode, tablePlace, tableWidth, tableHeight, tableX, tableY, tableActive) VALUES (null,'','2','1','$uniq','','100','100','0','0','open')");
  if($query === TRUE){ echo '1'; return; } echo '0'; return;
}
?>
