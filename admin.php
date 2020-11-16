<?php
require_once("script/script.admin.php");
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

        $admin = new Overview();
        // Wenn overview gesetzt ist = Überischt Reservierungen
        if(isset($_GET['overview'])){
          // Wenn Anfangstag gesetzt ist
          if(isset($_GET['day'])){
            $type = $_GET['overview']; // $type = Day oder $type = Week
            $day = $_GET['day'];
            $data = $admin->getOverview($type, $day);
          }

          echo '<input type="date" id="oInputDate" value="'.$day.'">';
          echo "<ul class='ow-nav'>";
            if($type == "Day"){
              echo '<li class="ow-nav-current">Tagesbericht</li>';
              echo '<li>Wochenbericht</li>';
            } else {
              echo '<li>Tagesbericht</li>';
              echo '<li class="ow-nav-current">Wochenbericht</li>';
            }
          echo "</ul>";


          if($data != false && count($data) >= 1){
            echo '<div id="ow-table-container">';
            echo '<table id="ow-table">';
            echo '<tr><th>Tisch ID</th><th>Name</th><th>Datum</th><th>Uhrzeit</th><th>Dauer</th><th>Anzahl</th><th>Telefon</th></tr>';
            foreach ($data as $key) {
              echo '<tr>';
              echo '<td><i class="fa fa-table fa-1x" onClick="viewAdminTable()"></i> Tisch ' . $key["tID"] . '</td>';
              echo '<td>'.$key["cName"].'</td>';
              echo '<td>'.$key["rDate"].'</td>';
              echo '<td>'.$key["rStart"].' - '.$key["rEnd"].'</td>';
              echo '<td>'.$key["rDuration"].'</td>';
              echo '<td>'.$key["rA"].'</td>';
              echo '<td>'.$key["cTNR"].'</td>';
              echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
          } else {
            echo "<h2 style='width: 100%; text-align: center;'>Keine Daten vorhanden</h2>";
          }

        } elseif(isset($_GET['noShow'])) {
          echo '<div class="noShow-container">';
            echo '<div class="noShow-top">';
              echo '<h2>NoShow-Liste</h2>';
              echo '<i class="fas fa-user-plus fa-2x"></i>';
            echo '</div>';

            echo '<ul class="noShow-content">';
              $data = $admin->getNoShow();
              foreach ($data as $key) {
                echo '<li id="ns-list'.$key["id"].'">';
                  echo '<p class="ns-amount">'.$key["amount"].'</p>';
                  echo '<p>'.$key["client"].'</p>';
                  echo '<p class="ns-mail">'.$key["mail"].'</p>';
                  echo '<p class="ns-time">'.$key["time"].'</p>';
                echo '</li>';
              }
            echo '</ul>';
          echo '</div>';

        } elseif(isset($_GET['reservierungen'])) {

        }

        ?>
      </div>
    </div>
    <script type="text/javascript">
    $(document).on("change","#oInputDate", function(){
      var date = $(this).val();
      var ow = getOverviewParameter()
      if(ow != false){
        window.location.href = "admin.php?overview="+ow+"&day="+date;
      }
    });

    $(document).on("click",".ow-nav li", function(){
      var type = $(this).text();
      if(type == "Tagesbericht"){
        window.location.href = "admin.php?overview=Day&day="+$('#oInputDate').val();
      } else {
        window.location.href = "admin.php?overview=Week&day="+$('#oInputDate').val();
      }
    });

    $(document).on("click",".noShow-content li",function(){
      var id = $(this).attr("id");
      if($('.noShow-edit-container').length<=0){
        var idAmount = $('#ns-list14 .ns-amount').val();
        var idMail = $('#'+id+' #ns-mail').val();
        var idTime = $('#'+id+' #ns-time').val();
        alert(idAmount);
        $('#'+id).append('<div class="noShow-edit-container"></div>');
        $('.noShow-edit-container').append('<div class="edit-form"><input type="text" id="ns-amount" value="'+idAmount+'"><input type="text" id="ns-mail" value="'+idMail+'"><input type="text" id="ns-time" value="'+idTime+'"></div>');
        $('.edit-form').append('<div class="edit-container-bottom"></div>');
        $('.edit-container-bottom').append('<button class="edit-button" onClick="closeEdit()">Schließen</button><button class="edit-button"id="ns-submit">Speichern</button>');
      }
    });

    $('.edit-form').submit(function(event){
      event.preventDefault();
      var amount = $('#ns-amount').val();
      var mail = $('#ns-mail').val();
      var time = $('#ns-time').val();
      if(amount.length >= 1 && mail.length >= 3 && time.length >= 3){
        $.ajax({
          url: "sync.php",
          method: "POST",
          data: { getTime: t, sndDate: dt},
          success: function(result) {

          }
        });
      }
      return false;
    });

    function getOverviewParameter() {
      var url = new URL(window.location.href);
      var c = url.searchParams.get("overview");
      if(c != null){
        return c.split("&")[0];
      }
      return false;
    }

    function closeEdit() {
      $('.noShow-edit-container').empty();
      $('.noShow-edit-container').remove();
    }
    </script>
  </body>
</html>
