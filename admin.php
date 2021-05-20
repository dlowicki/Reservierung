<?php
require_once("script/script.admin.php");
require_once("script/script.reservierung.php");
require_once("script/sync-admin.php");
require_once('sync.php');


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
          <li><a href="?analyse=HubRaum">Analyse</a></li>
          <li><a href="?noShow=HubRaum">NoShow Liste</a></li>
          <li><a href="?abweichendeAnzahl=HubRaum">Black Liste</a></li>
          <li><a href="?reservierungen=HubRaum">Reservierungen</a></li>
          <li><a href="?tische=HubRaum">Tische</a></li>
          <li><a href="index.php">Verlassen</a></li>
        </ul>
      </div>
      <div class="main">
        <?php
        // Überprüfen ob Admin Cookie gesetzt
        if(!isset($_COOKIE['rSession'])){ header("Location: index.php"); }
        if(isAdmin() != true){ header("Location: index.php"); }

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
              $var = "'".$key['tID']."','".$key["rDate"]."'";
              echo '<td><i class="fa fa-table fa-1x" onClick="redirectAdminReservierungen('.$var.')"></i> Tisch ' . $key["tID"] . '</td>';
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
                echo '<li id="ns-'.$key["id"].'">';
                  echo '<p class="ns-amount">'.$key["amount"].'</p>';
                  echo '<p class="ns-mail">'.$key["mail"].'</p>';
                  echo '<p class="ns-tnr">'.$key["tnr"].'</p>';
                  echo '<p class="ns-time">'.$key["time"].'</p>';
                echo '</li>';
              }
            echo '</ul>';
          echo '</div>';

        } elseif(isset($_GET['reservierungen'])) {
          $table = 0;
          $day = date('Y-m-d');
          if(isset($_GET['table'])){ $table = $_GET['table'];}
          if(isset($_GET['day'])){ $day = $_GET['day'];}
          echo '<div class="rs-container">';
            if($table != 0 && $day != 0){
              echo '<div class="rs-input-table"><h3>Bitte Tisch und Datum auswählen</h3><input type="text" value="'.$table.'" id="rs-table"><input type="date" value="'.$day.'" id="rs-date"></div>';

              $reservierung = new Reservierung($table, $day);
              if($reservierung->tableExists()){
                  $rsList = $reservierung->loadReservierungenList();

                  echo '<div class="rs-reservierung-list">';
                    echo '<ul>';
                      echo '<button id="createReserveButton">Neue Reservierung</button>';
                      if($rsList != false){
                        foreach ($rsList as $key) { echo '<li id="'.$key["rID"].'" class="state'.$key["rState"].'">'.$key["rTime"].'</li>'; }
                      } else {
                        echo '<p style="color: white; font-size: 1.4rem; display:block; text-align:center; padding: 1%;">Keine Reservierung vorhanden</p>';
                      }
                    echo '</ul>';

                    echo '<div class="rs-list-edit-container">';
                      echo '<div class="list-edit-reservierung">';
                        echo '<div class="list-edit-top">';
                          echo '<button id="bt-eingetroffen"><i class="fas fa-check"></i> Eingetroffen</button>';
                          echo '<button id="bt-freigeben"><i class="fas fa-unlock"></i> Wieder freigeben</button>';
                          echo '<button id="bt-abgesagt"><i class="fas fa-user-slash"></i> Abgesagt</button>';
                          echo '<button id="bt-noShow"><i class="fas fa-user-times"></i> No-Show</button>';
                          echo '<button id="bt-abwAnzahl"><i class="fas fa-id-card-alt"></i> Abw. Anzahl</button>';
                        echo '</div>';

                        echo '<div class="list-edit-bottom">';
                          echo '<select id="rs-block">';
                          $block = getTimeBlocks();
                          foreach ($block as $key) { echo '<option value="'.$key["id"].'">'.$key["start"].' - '.$key["end"].'</option>'; }
                          echo '</select>';
                          echo '<input type="number" id="rs-amount" min="0" max="20">';
                          echo '<div class="edit-bottom-table">';
                            echo '<img src="img/open/t-right-transparent.png">';
                            echo '<label class="switch"><input type="checkbox" id="switch-table"><span class="slider round"></span></label>';
                          echo '</div>';
                      echo '</div>';
                    echo '</div>';

                    echo '<div class="list-edit-hh">';
                      echo '<i class="fas fa-user-minus fa-1x" id="deleteClient"></i><input type="number" id="hh-number" value="0" min="0" max="19">';
                    echo '</div>';
                  echo '</div>';
                echo '</div>';
              }







            } else {
              echo '<div class="rs-input-table"><h3>Bitte Tisch auswählen</h3><input type="text" placeholder="Tisch Nummer" id="rs-table"><input type="date" value="'.$day.'" id="rs-date"></div>';
            }


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

    function redirectAdminReservierungen(table, date) {
      window.location.href = "admin.php?reservierungen&table="+table+"&day="+date;
    }

    /* NO SHOW CONTENT */

    $(document).on("click",".noShow-content li",function(){
      var id = $(this).attr("id");
      if($('.noShow-edit-container').length<=0){
        var idAmount = $('#'+id).children(".ns-amount").text();
        var idTNR = $('#'+id).children(".ns-tnr").text();
        var idMail = $('#'+id).children(".ns-mail").text();
        var idTime = $('#'+id).children(".ns-time").text();

        $('#'+id).append('<div class="noShow-edit-container"></div>');
        $('.noShow-edit-container').append('<div class="edit-form"><input type="hidden" id="ns-id" value="'+id+'"><input type="number" id="ns-amount" value="'+idAmount+'"><input type="text" id="ns-mail" value="'+idMail+'"><input type="text" id="ns-tnr" value="'+idTNR+'"><input type="text" id="ns-time" value="'+idTime+'"></div>');
        $('.edit-form').append('<div class="edit-container-bottom"></div>');
        $('.edit-container-bottom').append('<button class="edit-button" onClick="closeEdit()">Schließen</button><button class="edit-button" id="delete-noShow">Entfernen</button><button class="edit-button"id="ns-submit">Speichern</button>');
      }
    });

    $(document).on("click","#ns-submit",function(){
      var amount = $('#ns-amount').val();
      var mail = $('#ns-mail').val();
      var tnr = $('#ns-tnr').val();
      var time = $('#ns-time').val();
      var id = $('#ns-id').val().split('-')[1];
      if(amount.length >= 1 && mail.length >= 3 && time.length >= 3 && tnr.length >= 3){
        $.ajax({
          url: "script/sync-admin.php",
          method: "POST",
          data: { nsID: id, nsAmount: amount, nsMail: mail, nsTNR: tnr, nsTime: time},
          success: function(result) {
            if(result=="1"){
              $(".noShow-content").load(" .noShow-content > *");
              return;
            }
            alert("Ein Fehler ist aufgetreten \n" + result);
          }
        });
      }
      return false;
    });

    $('.fa-user-plus').click(function(){
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { nsEdit: "Create"},
        success: function(result) {
          if(result=="1"){
            $(".noShow-content").load(" .noShow-content > *");
            return;
          }
          alert("Ein Fehler ist aufgetreten \n" +result);
        }
      });
    });

    $(document).on("click","#delete-noShow",function(){
      var id = $('#ns-id').val().split('-')[1];
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { nsEdit: id},
        success: function(result) {
          if(result=="1"){
            $(".noShow-content").load(" .noShow-content > *");
            return;
          }
          alert("Ein Fehler ist aufgetreten \n" +result);
        }
      });
    });

    $('#rs-table').keypress(function(e){
      if(e.which == 13) {
        var input = $(this).val(); var day = $("#rs-date").val();
        if(input.length >= 1 && input.length <= 3 && day.length <= 10){
          window.location.href = "admin.php?reservierungen&table="+input+"&day="+day;
        }
      }
    });
    $('#rs-date').change(function(e){
      var input = $('#rs-table').val(); var day = $(this).val();
      if(input.length >= 1 && input.length <= 3 && day.length <= 10){
        window.location.href = "admin.php?reservierungen&table="+input+"&day="+day;
      }
    });



    /* Reservierung RS Liste */

    $('.rs-reservierung-list li').click(function(){
      $('.rs-reservierung-list>ul>li.reservierung-list-current').removeClass('reservierung-list-current');
      $(this).addClass('reservierung-list-current');

      $('.rs-reservierung-list ul').css("margin-left: 0%; margin-right: 0%; width: 15%;");
      $('#hh-number').val("0");

      var rID = $(this).attr("id");
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { rsLoad: rID},
        success: function(result) {
          if(result!="0"){
            $(".rs-list-edit-container").removeClass("css-animation-right");
            $(".rs-list-edit-container").width(); // trigger a DOM reflow
            $(".rs-list-edit-container").addClass("css-animation-right");
            $('.rs-list-edit-container').css("display","flex");
            var d = JSON.parse(result);

            // TABLE DATA
            $('.edit-bottom-table img').attr("src","img/"+d[0]['tableActive']+"/t-"+d[0]['tableType']+"-transparent.png");
            if(d[0]['tableActive'] == "open"){$('#switch-table').attr("checked","true");}
            $('.switch').attr("id",d[0]['tableID']);

            // FRONT DATA
            //$('#rs-time').val(d[0]['reserveStart']);
            //$('#rs-block').val(d[0]['reserveDuration']);
            $('#rs-amount').val(parseInt(d[0]['reserveAmount']));

            // Client
            $('.edit-hh').remove();
            $('#submit-clients').remove();
            for (var i=0; i < 20; i++) {
              var uniqid = "<?php echo uniqid(); ?>";
              $('.list-edit-hh').append('<div id="hh-'+i+'" class="edit-hh" style="display: none;"></div>');
              $('#hh-'+i).append('<input type="hidden" class="hh-id" value="'+uniqid+'">');
              $('#hh-'+i).append('<input type="text" placeholder="Name..." class="hh-name">');
              $('#hh-'+i).append('<input type="text" placeholder="Vorname..." class="hh-vorname">');
              $('#hh-'+i).append('<input type="text" placeholder="E-Mail..." class="hh-mail">');
              $('#hh-'+i).append('<input type="text" placeholder="Telefon..." class="hh-tnr">');
            }

            var count = 0;
            d.forEach((item, i) => {
              $('#hh-'+count+' .hh-id').val(item['clientID']);
              $('#hh-'+count+' .hh-name').val(item['clientName']);
              $('#hh-'+count+' .hh-vorname').val(item['clientVorname']);
              $('#hh-'+count+' .hh-mail').val(item['clientMail']);
              $('#hh-'+count+' .hh-tnr').val(item['clientTNR']);
              count++;
            });
            $('#hh-0').css("display","block");
            $('.list-edit-hh').append('<button id="submit-clients">Speichern</button>');
            return;
          }
          alert("Ein Fehler ist aufgetreten \n" +result);
        }
      });

    });

    $('#switch-table').click(function(){
      var check = $(this).prop("checked");
      var id = $('.switch').attr("id");

      $.ajax({
        url: "sync.php",
        method: "POST",
        data: { setTableActive: id, value: check},
        success: function(result) {
          var src = $('.edit-bottom-table img').attr("src").split("/");
          if(result){
            if(check == false){ $('.edit-bottom-table img').attr("src",src[0] + "/closed/" + src[2]);
          } else {$('.edit-bottom-table img').attr("src",src[0] + "/open/" + src[2]); }
            return;
          }
          }
      });
    });

    $('#hh-number').change(function(){
      $('.edit-hh').css("display","none");
      $('#hh-'+$(this).val()).css("display","block");
    });

    $(document).on("click",".list-edit-top button",function(){
      // Erhalte Reservierung ID von list-current button
      var reserveID = $('.reservierung-list-current').attr('id');
      if(!reserveID){ alert("Sie müssen vorher eine Reservierung auswählen!");return;}

      var buttonType = $(this).attr("id");
      var dataType,color;

      if($('.reservierung-list-current').css('background-color') == "rgb(0, 109, 119)" && buttonType == "bt-noShow"){
        alert("No Show wurde für die Reservierung bereits eingetragen!");
        return;
      }

      switch (buttonType) {
        case "bt-eingetroffen":
          dataType = "1";color = "#2b9348";
          break;
        case "bt-freigeben":
          dataType = "2";color = "#ee6c4d";
          break;
        case "bt-abgesagt":
          dataType = "3";color = "#ba181b";
          break;
        case "bt-noShow":
          dataType = "4";color = "#006d77";
          break;
        case "bt-abwAnzahl":
          dataType = "5";color = "#7400b8";
          break;
      }

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { acpButton: dataType, acpReserveID: reserveID, acpDate: $('#rs-date').val()},
        success: function(result) {
          console.log(result);
          if(result){$('#'+reserveID).css("background-color",color);return;}
          alert("Fehler: Tisch konnte nicht bearbeitet werden!");
        }
      });
    });

    $(document).on("click","#submit-clients",function(){
      var dataClients = [];
      for (var i = 0; i < 20; i++) {
        var name = $('#hh-'+i+' .hh-name').val().toString();
        var rID = $('.reservierung-list-current').attr('id');
        if(name.length <= 2){
          continue;
        }
        var temp = [];
        temp[0] = $('#hh-'+i+' .hh-id').val();
        temp[1] = rID;
        temp[2] = name;
        temp[3] = $('#hh-'+i+' .hh-vorname').val();
        temp[4] = $('#hh-'+i+' .hh-mail').val();
        temp[5] = $('#hh-'+i+' .hh-adresse').val();
        temp[6] = $('#hh-'+i+' .hh-tnr').val();
        dataClients[i] = temp;
      }

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { submitClients: dataClients },
        success: function(result) {
          if(result == "1"){
            $('#submit-clients').css("color","green");
            $('#submit-clients').css("border","1px solid green");
            setTimeout(function(){
              $('#submit-clients').css("color","white");
              $('#submit-clients').css("border","1px solid white");
            }, 5000);
              return;
          }
          alert("Ein Fehler ist aufgetreten \n" +result);
        }
      });
    });

    $('#deleteClient').click(function(){
      var hhNumber = $('#hh-number').val();
      var hhID = $('#hh-'+hhNumber+' .hh-id').val();
      if(hhNumber=="0"){alert("Client 0 kann nicht gelöscht werden!"); return; }
      var r = confirm("Möchten Sie den Client "+$('#hh-'+hhNumber+' .hh-name').val() + " mit der ID ["+hhID+"] wirklich löschen?");
      if (r == false) {return;} // User möchte Client nicht löschen!

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { deleteClient: hhID },
        success: function(result) {
          if(result!="0"){
            var uniqid = '<?php echo uniqid(); ?>';
            $('#hh-'+hhNumber+' .hh-id').val(uniqid);
            $('#hh-'+hhNumber+' .hh-name').val("");
            $('#hh-'+hhNumber+' .hh-vorname').val("");
            $('#hh-'+hhNumber+' .hh-mail').val("");
            $('#hh-'+hhNumber+' .hh-adresse').val("");
            $('#hh-'+hhNumber+' .hh-tnr').val("");
            return;
          }
          alert("Ein Fehler ist aufgetreten\n"+result);
          return;
        }
      });
    });

    $('#createReserveButton').click(function(){
      var tID = $('#rs-table').val();
      var tDate = $('#rs-date').val();
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { createReserveButton: 'true', table: tID, date: tDate },
        success: function(result) {
          if(result!="0"){
            location.reload();
            return;
          }
          alert("Ein Fehler ist aufgetreten\n"+result);
          return;
        }
      });
    });


    /* Change Buttons FRONT DATA */

    $('#rs-time').change(function(){
      var rTime = $(this).val()+":00";
      var rDuration = $('#rs-duration').val();
      var rID = $('.reservierung-list-current').attr('id');

      var r = "22:00:00";
      if(rDuration == "2:30"){
        var eTime = new Date($('#rs-date').val() + " " + $('#rs-time').val());
        r = eTime.getHours()+2 + ":"+(eTime.getMinutes()+30)+":00";
        if(eTime.getMinutes()+30 >= 60){r = eTime.getHours()+3+":"+((eTime.getMinutes()+30)-60)+":00";}
      }

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { updateReserveStart: rID, startTime: rTime, endTime: r },
        success: function(result) {
          if(result=="1"){
            $(".reservierung-list-current").text(rTime + " - "+r);
            return;
          }
          alert("Ein Fehler ist aufgetreten\n"+result);
          return;
        }
      });
    });

    $('#rs-duration').change(function(){
      var rDuration = $(this).val();
      var rID = $('.reservierung-list-current').attr('id');
      var r = "22:00:00";
      if(rDuration == "2:30"){
        var eTime = new Date($('#rs-date').val() + " " + $('#rs-time').val());
        r = eTime.getHours()+2 + ":"+(eTime.getMinutes()+30)+":00";
        if(eTime.getMinutes()+30 >= 60){r = eTime.getHours()+3+":"+((eTime.getMinutes()+30)-60)+":00";}
      }

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { updateReserveDuration: rID, duration: rDuration, endTime: r },
        success: function(result) {

          if(result == "1"){
            var startTime = $(".reservierung-list-current").text().split(" - ")[0];
            $(".reservierung-list-current").text(startTime + " - "+r);
            return;
          }
          alert("Ein Fehler ist aufgetreten\n"+result);
          return;
        }
      });
    });

    $('#rs-amount').change(function(){
      var rAmount = $(this).val();
      var rID = $('.reservierung-list-current').attr('id');

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { updateReserveAmount: rID, amount: rAmount },
        success: function(result) {
          if(result == "0"){
            alert("Ein Fehler ist aufgetreten\n"+result);
            return;
          }
        }
      });
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
