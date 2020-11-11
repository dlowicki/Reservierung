<?php
require_once("script/table.script.php");
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | HubRaum</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="jquery.min.js" charset="utf-8"></script>
  </head>
  <body>
    <div class="admin-container">
      <div class="sidebar">
        <ul>
          <li><a href="?overview=Day<?php echo "&day=".date("Y-m-d"); ?>">Übersicht</a></li>
          <li><a href="?noShow=HubRaum">NoShow Liste</a></li>
          <li><a href="?reservierungen=HubRaum">Reservierungen</a></li>
          <li><a href="index.php">Zurück</a></li>
        </ul>
      </div>
      <div class="main">
        <?php
        // Überprüfen ob Admin Cookie gesetzt
        if(!isset($_COOKIE['rSession'])){
          header("Location: index.php");
          return;
        }





        if(isset($_GET['overview'])){
          $type = $_GET['overview'];
          $duration = "";
          if($type == "Day" && isset($_GET['day'])){
            $duration = $_GET['day'];
          } elseif ($type == "Day" && isset($_GET['week'])) {
            $duration = $_GET['day'];
          }
          echo "<ul class='ow-nav'>";
            echo '<li>Tagesbericht<input type="date" id="oInputDate"></li>';
            echo '<li>Wochenbericht<input type="date" id="oInputDate"></li>';
          echo "</ul>";

          echo '<div id="ow-table-container">';
          echo '<table id="ow-table">';
          echo '<tr><th>Tisch ID</th><th>Name</th><th>Datum</th><th>Uhrzeit</th><th>Dauer</th><th>Anzahl</th><th>Telefon</th></tr>';
          echo '<tr><td><i class="fa fa-table fa-1x" onClick="viewAdminTable()"></i> Tisch 12</td><td>Lowicki</td><td>Test</td><td>10 Uhr</td><td>2:30</td><td>10</td><td>Nummer</td></tr>';
          echo '</table>';
          echo '</div>';


        } elseif(isset($_GET['noShow'])) {

        } elseif(isset($_GET['reservierungen'])) {

        }

        ?>
      </div>
    </div>
    <script type="text/javascript">

    </script>
  </body>
</html>
