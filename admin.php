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
          <li><a href="?liste=No-Show">Listen</a></li>
          <li><a href="?reservierungen=HubRaum">Reservierungen</a></li>
          <li><a href="?tische=HubRaum">Tischplan</a></li>
          <li><a href="?zeit=HubRaum">Öffnungszeiten</a></li>
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
            echo '<tr><th>Tisch ID</th><th>Name</th><th>Datum</th><th>Anzahl</th><th>Telefon</th></tr>';
            foreach ($data as $key) {
              echo '<tr>';
              $var = "'".$key['tID']."','".$key["rDate"]."'";
              echo '<td><i class="fa fa-table fa-1x" onClick="redirectAdminReservierungen('.$var.')"></i> Tisch ' . $key["tID"] . '</td>';
              echo '<td>'.$key["cName"].'</td>';
              echo '<td>'.$key["rDate"].'</td>';
              echo '<td>'.$key["rA"].'</td>';
              echo '<td>'.$key["cTNR"].'</td>';
              echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
          } else {
            echo "<h2 style='width: 100%; text-align: center;'>Keine Daten vorhanden</h2>";
          }

        } elseif(isset($_GET['liste'])) {
			$noshow = "'admin.php?liste=No-Show'"; $abwAnzahl = "'admin.php?liste=abwAnzahl'";
			echo '<div class="liste-nav"><button onClick="window.location.href='.$noshow.'">No-Show</button><button onClick="window.location.href='.$abwAnzahl.'">Abw. Anzahl</button></div>';
			$liste = $_GET['liste'];

			echo '<div class="liste-container">';
		    echo '<div class="liste-top">';
				if($liste == 'No-Show'){echo '<h2 id="ns">'.$liste.'</h2><i class="fas fa-user-plus fa-2x"></i>';}elseif($liste=='abwAnzahl'){echo '<h2 id="b">'.$liste.'</h2><i class="fas fa-user-plus fa-2x"></i>';}
        echo '</div>';

				echo '<ul class="liste-content">';
				if($liste == 'No-Show'){
					$data = $admin->getNoShow();
					foreach ($data as $key) {
						echo '<li id="ns-'.$key["id"].'">';
						echo '<p class="ns-mail">'.$key["mail"].'</p>';
						echo '<p class="ns-tnr">'.$key["tnr"].'</p>';
						echo '<p class="ns-amount">'.$key["amount"].'</p>';
						//echo '<p class="ns-time">'.$key["time"].'</p>';
						echo '</li>';
					}
				} elseif($liste == 'abwAnzahl') {
					$data = $admin->getBlacklist();
					foreach ($data as $key) {
						echo '<li id="b-'.$key["id"].'">';
						echo '<p class="b-mail">'.$key["mail"].'</p>';
						echo '<p class="b-tnr">'.$key["tnr"].'</p>';
						echo '<p class="b-amount">'.$key["amount"].'</p>';
						echo '</li>';
					}
				}
				echo '</ul>';
			echo '</div>';



        } elseif(isset($_GET['reservierungen'])) {
          $table = 0; $day = date('Y-m-d');
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
                        foreach ($rsList as $key) {
                          echo '<li id="'.$key["rID"].'" class="state'.$key["rState"].'">'.$key["rTime"].'</li>';
                        }
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
                            echo '<h3>Tisch aktiv</h3>';
                            echo '<label class="switch"><input type="checkbox" id="switch-table"><span class="slider round"></span></label>';
                          echo '</div>';

                          echo '<div class="list-edit-hh">';
                            echo '<i class="fas fa-user-minus fa-2x" id="deleteClient"></i><input type="number" id="hh-number" value="0" min="0" max="19">';
                          echo '</div>';

                      echo '</div>';
                    echo '</div>';


                  echo '</div>';
                echo '</div>';
              }

            } else {
              echo '<div class="rs-input-table"><h3>Bitte Tisch auswählen</h3><input type="text" placeholder="Tisch Nummer" id="rs-table"><input type="date" value="'.$day.'" id="rs-date"></div>';
            }


        } elseif(isset($_GET['tische'])){
          $overview = new Overview();
          $tables = $overview->loadTables();
          echo '<div class="tische-container">';

          echo '<div id="tische-panel">';
            echo '<h2>Tisch Panel</h2>';
            $tAll = 'checked'; $standorte = array();
            foreach ($tables as $key) { if($key['tableActive'] == 'closed'){ $tAll = ''; } if(!in_array($key['tablePlace'],$standorte)){ array_push($standorte,$key['tablePlace']); } }
            echo '<div class="t-panel"><h3>Alle Tische</h3><label class="switch"><input type="checkbox" id="switch-all" '.$tAll.'><span class="slider round"></span></label></div>';
            echo '<div class="t-panel">';
              echo '<select id="t-standort">';
              for ($i=0; $i < sizeof($standorte); $i++) { echo '<option value="'.$standorte[$i].'">'.$standorte[$i].'</option>'; }
              echo '</select>';
              $checkPlace = $overview->checkPlaceActive($standorte[0]);
              if($checkPlace == 'open'){
                echo '<label class="switch"><input type="checkbox" id="switch-standort" checked><span class="slider round"></span></label>';
              } else {
                echo '<label class="switch"><input type="checkbox" id="switch-standort"><span class="slider round"></span></label>';
              }

            echo '</div>';
            echo '<div class="t-panel">';
            $link = "'tischplan.php'";
            echo '<button onClick="window.location.href='.$link.'">Tischplan</button><button id="t-speichern">Speichern</button>';
            echo '</div>';
          echo '</div>';


          echo '<table>';
          echo '<tr><th>TischID</th><th>Min. Anzahl</th><th>Max. Anzahl</th><th>Standort</th></tr>';
          foreach ($tables as $key) {
            echo '<tr class="tische-row" id="'.$key["tableID"].'">';
              echo '<td class="tische-label"><input type="text" id="tische-id" value="'.$key["tableID"].'"></td>';
              echo '<td class="tische-label td-number"><input type="number" id="tische-min" value="'.$key["tableMin"].'"></td>';
              echo '<td class="tische-label td-number"><input type="number" id="tische-max" value="'.$key["tableMax"].'"></td>';
              echo '<td class="tische-label"><input type="text" id="tische-place" value="'.$key["tablePlace"].'"></td>';
              if($key['tableActive'] == "open"){ echo '<td><label class="switch" id="'.$key["tableID"].'"><input type="checkbox" id="switch-table" checked><span class="slider round"></span></label></td>'; }
              else { echo '<td><label class="switch" id="'.$key["tableID"].'"><input type="checkbox" id="switch-table"><span class="slider round"></span></label></td>'; }
              echo '<td class="tische-label"><button>Speichern</button></td>';
            echo '</tr>';
          }
          echo '</table>';
          echo '</div>';
        } elseif(isset($_GET['zeit'])){
          echo '<div class="zeit-container">';
            echo '<div class="arbeitstage-container">';
              echo '<h2>Öffnungszeiten</h2>';
              $overview = new Overview();
              $days = $overview->loadDays();
              if($days){
                for ($i=0; $i < 7; $i++) {
                  echo '<div class="arbeitstag">';
                    $time = explode('-',$days[$i]["time"]);
                    echo '<h3>'.$days[$i]["day"].'</h3><input type="time" id="arbeitstag-von-'.$days[$i]["id"].'" value="'.$time[0].'"> - <input type="time" id="arbeitstag-bis-'.$days[$i]["id"].'" value="'.$time[1].'">';
                    if($days[$i]['active'] == false){
                      echo '<label class="switch"><input type="checkbox" id="switch-arbeitstag-'.$days[$i]["id"].'"><span class="slider round"></span></label>';
                    } else {
                      echo '<label class="switch"><input type="checkbox" id="switch-arbeitstag-'.$days[$i]["id"].'" checked><span class="slider round"></span></label>';
                    }
                  echo '</div>';
                }
              }
              echo '</div>';
              echo '<div class="feiertage-container">';
                echo '<div class="feiertage-filter">';
                  echo '<input type="text" id="ft-name" placeholder="Beschreibung">';
                  echo '<input type="date" id="ft-date">';
                  echo '<select id="ft-select"><option>Hochzeit</option><option>Party</option><option>Auftritt</option></select>';
                  echo '<i class="fas fa-calendar-plus fa-2x"></i>';
                echo '</div>';

                echo '<div class="feiertage-data">';
                $specials = $overview->loadSpecialDays();
                  if($specials){
                    foreach ($specials as $key) {
                      echo '<div class="feiertag"><p>'.$key["type"].'</p><p>'.$key["date"].'</p><button id="ft-entfernen" class="ft-'.$key["id"].'">Entfernen</button></div>';
                    }
                  }
                echo '</div>';
              echo '</div>';
            echo '</div>';
        }

        ?>
      </div>
    </div>
    <script type="text/javascript">

    /* ZEITT */
    $(document).on('change','.arbeitstag input', function(){
      var id = $(this).attr('id').split('-')[2];
      const valVON = $('#arbeitstag-von-'+id).val(); const valBIS = $('#arbeitstag-bis-'+id).val();
      var check = $('#switch-arbeitstag-'+id).prop("checked");
    $.ajax({
      url: "script/sync-admin.php",
      method: "POST",
      data: { arbeitstag: id+';'+valVON+'-'+valBIS+';'+check},
      success: function(result) {
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \n" + result); } return;
      }
    });
  });
  $(document).on('change','#switch-arbeitstag',()=>{
    var check = $(this).prop("checked");
    const valVON = $('#arbeitstag-von-'+id).val(); const valBIS = $('#arbeitstag-bis-'+id).val();
    var id = $(this).attr('id').split('-')[2];
    $.ajax({
      url: "script/sync-admin.php",
      method: "POST",
      data: { arbeitstag: id+';'+valVON+'-'+valBIS+";"+check},
      success: function(result) {
        console.log(result);
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \n" + result); } return;
      }
    });
  });
  /* SPECIAL DAYS */
  $('.feiertage-filter i').click(()=>{
    var beschreibung = $('#ft-name').val(); if(beschreibung.length <= 0){ return false; }
    var date = $('#ft-date').val(); if(date.length < 10 || date == 'tt.mm.jjjj' || date == null){ return false; }
    var type = $('#ft-select').val(); if(type.length <= 0){ return false; }
    console.log(beschreibung+";"+date+";"+type);
    $.ajax({
      url: "script/sync-admin.php",
      method: "POST",
      data: { specialDay: beschreibung+";"+date+";"+type},
      success: function(result) {
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \n" + result); } else { window.location.href='admin.php?zeit=HubRaum' } return;
      }
    });
  });
  $('.feiertag').click(()=>{
    if(confirm('Möchten Sie das Event wirklich löschen?')){
      var cl = $('#'+event.target.id).attr('class');
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { specialDayDelete: cl},
        success: function(result) {
          if(result!="1"){ alert("Ein Fehler ist aufgetreten \n" + result); } else { window.location.href='admin.php?zeit=HubRaum' } return;
        }
      });
    }
  });



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

    function redirectAdminReservierungen(table, date) { window.location.href = "admin.php?reservierungen&table="+table+"&day="+date; }

    /* NO SHOW CONTENT */
    $(document).on("click",".liste-content li",function(){
      var id = $(this).attr("id").split('-');
      if($('.liste-edit-container').length<=0){
        var idAmount = $('#'+id[0]+'-'+id[1]).children("."+id[0]+"-amount").text();
        var idTNR = $('#'+id[0]+'-'+id[1]).children("."+id[0]+"-tnr").text();
        var idMail = $('#'+id[0]+'-'+id[1]).children("."+id[0]+"-mail").text();
        //var idTime = $('#'+id).children(".ns-time").text();
        $('#'+id[0]+'-'+id[1]).append('<div class="liste-edit-container"></div>');
        $('.liste-edit-container').append('<div class="edit-form"><input type="hidden" id="liste-id" value="'+id[0]+'-'+id[1]+'"><input type="text" id="liste-mail" value="'+idMail+'"><input type="text" id="liste-tnr" value="'+idTNR+'"><input type="number" id="liste-amount" value="'+idAmount+'"></div>');
        $('.edit-form').append('<div class="edit-container-bottom"></div>');
        $('.edit-container-bottom').append('<button class="edit-button" onClick="closeEdit()">Schließen</button><button class="edit-button" id="delete-liste">Entfernen</button><button class="edit-button"id="liste-submit">Speichern</button>');
      }
    });

    $(document).on("click","#liste-submit",function(){
		var id = $('#liste-id').val().split('-');
		var amount = $('#liste-amount').val();
		var mail = $('#liste-mail').val();
		var tnr = $('#liste-tnr').val();
      if(amount.length >= 1 && mail.length >= 3 && tnr.length >= 3){
        $.ajax({
          url: "script/sync-admin.php",
          method: "POST",
          data: { updateListen: id[1]+";"+amount+";"+mail+";"+tnr+";"+id[0]},
          success: function(result) {
            if(result=="1"){
              $(".liste-content").load(" .liste-content > *");
              return;
            }
            alert("Ein Fehler ist aufgetreten \n" + result);
          }
        });
      }
      return false;
    });

    $('.fa-user-plus').click(function(){
		var id = $('.liste-top h2').attr('id');
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { listeEdit: "Create", listeType: id},
        success: function(result) {
          if(result=="1"){$(".liste-content").load(" .liste-content > *"); return; }
          alert("Ein Fehler ist aufgetreten \n" +result);
        }
      });
    });

    $(document).on("click","#delete-liste",function(){
      var id = $('#liste-id').val().split('-');
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { listeEdit: id[1], listeType: id[0]},
        success: function(result) {
          if(result=="1"){
            $(".liste-content").load(" .liste-content > *");
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

      //$('.rs-reservierung-list ul').css("width: 50%;");
      $('#hh-number').val("0");
      var rID = $(this).attr("id");

      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { rsLoad: rID},
        success: function(result) {
          console.log(result);
          if(result!="0"){
            $(".rs-list-edit-container").removeClass("css-animation-right");
            $(".rs-list-edit-container").width(); // trigger a DOM reflow
            $(".rs-list-edit-container").addClass("css-animation-right");
            $('.rs-list-edit-container').css("display","flex");
            var d = JSON.parse(result);

            // TABLE DATA
            if(d[0]['tableActive'] == "open"){ $('#switch-table').attr("checked","true"); $('.edit-bottom-table h3').css('color','#006400'); }
            else { $('.edit-bottom-table h3').css('color','#f94144'); $('.edit-bottom-table h3').text('Tisch gesperrt'); }
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
          console.log(result);
          if(result){
            if(check == false){ $('.edit-bottom-table h3').text('Tisch gesperrt'); $('.edit-bottom-table h3').css('color','#f94144');
          } else { $('.edit-bottom-table h3').text('Tisch aktiv'); $('.edit-bottom-table h3').css('color','#006400'); }
            return;
          }
          }
      });
    });

    $('#hh-number').change(function(){ $('.edit-hh').css("display","none"); $('#hh-'+$(this).val()).css("display","block"); });

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
        temp[5] = $('#hh-'+i+' .hh-tnr').val();
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
      var tID = $('#rs-table').val(); var tDate = $('#rs-date').val();
	  console.log(tID + " - " + tDate);
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { createReserveButton: 'true', table: tID, date: tDate },
        success: function(result) {
			console.log(result);
          if(result!="0"){ location.reload(); return; }
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

    /* TISCHE */
    $('.tische-row button').click(function(){
      var tableID = $(this).parent().parent().attr('id');
      var newTableID = $('#'+tableID+" #tische-id").val();
      var newMin = $('#'+tableID+" #tische-min").val();
      var newMax = $('#'+tableID+" #tische-max").val();
      var newPlace = $('#'+tableID+" #tische-place").val();
      var newCheck = $('#'+tableID+" #switch-table").prop("checked");
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { updateAdminTables: tableID+";"+newTableID+";"+newMin+";"+newMax+";"+newPlace+";"+newCheck},
        success: function(result) { if(result == "0"){ alert('Ein Fehler ist aufgetreten! \nBitte Daten überprüfen bei Tisch '+tableID); } return; }
      });
    });
    $('#t-speichern').click(()=>{
      var all = $('#switch-all').prop("checked"); var stCheck = $('#switch-standort').prop("checked"); var st = $('#t-standort').val();
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { tischPanel: all+";"+stCheck+";"+st},
        success: function(result) { if(result == "0"){ alert('Ein Fehler ist aufgetreten! \nBitte Daten überprüfen bei Tisch '+tableID); } location.reload(); return; }
      });
    });
    $(document).on('change','#t-standort',function(event){
      var place = event.target.value;
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { loadStandort: place},
        success: function(result) {
          switch (result) {
            case 'open':
              $('#switch-standort').prop('checked', true);
              break;
            case 'closed':
              $('#switch-standort').prop('checked', false);
              break;
            default:
              alert('Ein Fehler ist aufgetreten! \nBitte Daten überprüfen bei Tisch '+tableID);
          }
          return;
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
      $('.liste-edit-container').empty();
      $('.liste-edit-container').remove();
    }
    </script>
  </body>
</html>
