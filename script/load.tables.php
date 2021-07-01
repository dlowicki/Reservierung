<?php
  require_once('../class/class.tables.php');
  // JSON file mit allen Daten von Tischen
  // Daten sollen überprüft und angezeigt werden, welche Tische frei sind

  if(isset($_GET['date'])&&isset($_GET['bz'])&&isset($_GET['amount']))
  {
    $type = "";
    $date = $_GET['date'];
    $blockzeit = $_GET['bz'];
    $amount = $_GET['amount'];
    
    if($amount > 2)
    {
      $type = 'tischplan';
    } else
    {
      $type = 'tischplan2';
    }

    $tables = new Tables($type, $date, $blockzeit, $amount);
    echo json_encode($tables->getTables());
  }





?>
