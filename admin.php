<?php
require_once("script/script.admin.php");
require_once("script/script.reservierung.php");
require_once("script/sync-admin.php");
require_once('sync.php');
require_once('script/script.analyse.php');


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
    <script src="js/chart.min.js" charset="utf-8"></script>
    <script src="js/script.analyse.js" charset="utf-8"></script>
  </head>
  <body>
    <div class="admin-container">
      <div class="sidebar">
        <ul>
          <li><a href="?overview=Day<?php echo "&day=".date("Y-m-d"); ?>">Übersicht</a></li>
          <li><a href="?analyse=1%20Monat">Analyse</a></li>
          <li><a href="?liste=No-Show">Listen</a></li>
          <li><a href="?reservierungen=HubRaum">Reservierungen</a></li>
          <li><a href="?tische=HubRaum">Tischplan</a></li>
          <li><a href="?zeit=HubRaum">Öffnungszeiten</a></li>
          <li><a href="?rechte=HubRaum">Rechte</a></li>
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
            $type = $_GET['overview']; // $type = Day oder $type = Week oder $type = full
            $day = $_GET['day'];
            $data = $admin->getOverview($type, $day);
          }

          echo '<input type="date" id="oInputDate" value="'.$day.'">';
          echo "<ul class='ow-nav'>";
            switch ($type) {
              case 'Week':
                echo '<li>Tagesbericht</li>';
                echo '<li class="ow-nav-current">Wochenbericht</li>';
                echo '<li>Ganzer Bericht</li>';
                  break;
              case 'full':
                echo '<li>Tagesbericht</li>';
                echo '<li>Wochenbericht</li>';
                echo '<li class="ow-nav-current">Ganzer Bericht</li>';
                  break;
              case 'Day':
                echo '<li class="ow-nav-current">Tagesbericht</li>';
                echo '<li>Wochenbericht</li>';
                echo '<li>Ganzer Bericht</li>';
                  break;
              default:
              echo '<li class="ow-nav-current">Tagesbericht</li>';
              echo '<li>Wochenbericht</li>';
              echo '<li>Ganzer Bericht</li>';
                break;
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

        } elseif(isset($_GET['analyse'])){
          echo '<div class="analyse-container">';
            echo '<ul class="analyse-zeit">';
            $time = $_GET['analyse'];
            if($time == 'Heute'){echo '<li class="analyse-zeit-current">Heute</li>';}else{echo '<li>Heute</li>';}
            if($time == '1 Woche'){echo '<li class="analyse-zeit-current">1 Woche</li>';}else{echo '<li>1 Woche</li>';}
            if($time == '1 Monat'){echo '<li class="analyse-zeit-current">1 Monat</li>';}else{echo '<li>1 Monat</li>';}
            /*if($time != 'Heute' && $time != '1 Woche' && $time != '1 Monat'){
              echo '<li class="analyse-zeit-current"><input type="date" value="'.$time.'" id="analyse-date-from">';
              echo '<input type="date" id="analyse-date-to"></li>';
            }else{
              echo '<li><input type="date" id="analyse-date-from"><input type="date" id="analyse-date-to"></li>';
            }*/
            echo '</ul>';

            echo '<div class="analyse-data">';
              echo '<div id="chart-blockzeit" class="analyse-box">';
                $blockzeiten = getAnalyseBlockzeiten($time);
                if($blockzeiten != false){
                  echo '<h2>Blockzeit - '.$time.'</h2>';
                  echo '<canvas id="ChartBlockzeit"></canvas>';
                } else {
                  echo '<h2>Blockzeit - Keine Daten gefunden</h2>';

                }
              echo '</div>';

              echo '<div id="chart-reservierungen" class="analyse-box">';
              $r = getAnalyseReservierungen();
              if($r != false){
                if(isset($r[0])){$m0=$r[0];}else{$m0=0;}
                if(isset($r[1])){$m1=$r[1];}else{$m1=0;}
                if(isset($r[2])){$m2=$r[2];}else{$m2=0;}
                if(isset($r[3])){$m3=$r[3];}else{$m3=0;}
                if(isset($r[4])){$m4=$r[4];}else{$m4=0;}
                if(isset($r[5])){$m5=$r[5];}else{$m5=0;}
                if(isset($r[6])){$m6=$r[6];}else{$m6=0;}
                if(isset($r[7])){$m7=$r[7];}else{$m7=0;}
                if(isset($r[8])){$m8=$r[8];}else{$m8=0;}
                if(isset($r[9])){$m9=$r[9];}else{$m9=0;}
                if(isset($r[10])){$m10=$r[10];}else{$m10=0;}
                if(isset($r[11])){$m11=$r[11];}else{$m11=0;}
                echo '<h2>Reservierungen - Jahr</h2>';
                echo '<canvas id="ChartReservierungen"></canvas>';
              }
              echo '</div>';
              echo '<div id="chart-wochentage" class="analyse-box">';
                $tage = getAnalyseTage($time);
                if(isset($tage[0])){$t0=$tage[0];}else{$t0=0;}
                if(isset($tage[1])){$t1=$tage[1];}else{$t1=0;}
                if(isset($tage[2])){$t2=$tage[2];}else{$t2=0;}
                if(isset($tage[3])){$t3=$tage[3];}else{$t3=0;}
                if(isset($tage[4])){$t4=$tage[4];}else{$t4=0;}
                if(isset($tage[5])){$t5=$tage[5];}else{$t5=0;}
                if(isset($tage[6])){$t6=$tage[6];}else{$t6=0;}
                if($tage != false){
                  echo '<h2>Wochentage - '.$time.'</h2>';
                  echo '<canvas id="ChartWochentage"></canvas>';
                } else {
                  echo '<h2>Wochentage - Keine Daten gefunden</h2>';
                }
              echo '</div>';

              echo '<div id="table-events" class="analyse-box">';
                $events = getAnalyseEvents($time);
                if($events != false){
                  echo '<h2>Events - '.$time.'</h2>';
                  echo '<h3 class="events-counter">';
                  if(isset($events[3])){echo $events[3]; }else{echo '0'; }
                  echo '</h3>';
                  echo '<div class="events-data">';
                    echo '<label>Hochzeiten <p class="events-counter">';
                    if(isset($events[0])){echo $events[0]; }else{echo '0'; }
                    echo '</p></label>';
                    echo '<label>Auftritte <p class="events-counter">';
                    if(isset($events[2])){echo $events[2]; }else{echo '0'; }
                    echo '</p></label>';
                    echo '<label>Partys <p class="events-counter">';
                    if(isset($events[1])){echo $events[1]; }else{echo '0'; }
                    echo '</p></label>';
                  echo '</div>';
                } else {
                  echo '<h2>Events - Keine Daten gefunden</h2>';
                }
              echo '</div>';

              echo '<div id="chart-buttons" class="analyse-box">';
                $buttons = getAnalyseButtons($time);
                if(isset($buttons[0])){$b0=$buttons[0];}else{$b0=0;}
                if(isset($buttons[1])){$b1=$buttons[1];}else{$b1=0;}
                if(isset($buttons[2])){$b2=$buttons[2];}else{$b2=0;}
                if(isset($buttons[3])){$b3=$buttons[3];}else{$b3=0;}
                if(isset($buttons[4])){$b4=$buttons[4];}else{$b4=0;}
                if($buttons != false){
                  echo '<h2>Buttons - '.$time.'</h2>';
                  echo '<canvas id="ChartButtons"></canvas>';
                } else {
                    echo '<h2>Buttons - Keine Daten gefunden</h2>';
                }
              echo '</div>';
            echo '</div>';
          echo '</div>';

          echo '<script>';
          if($blockzeiten != false){
            echo "if(document.getElementById('ChartBlockzeit')){
              var ctx = document.getElementById('ChartBlockzeit').getContext('2d');
              new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: ['17:00 - 19:30', '19:30 - 22:00'],
                  datasets: [{
                    label: 'Blockzeiten',
                    data: [".$blockzeiten[0].", ".$blockzeiten[1]."],
                    backgroundColor: [ 'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)' ],
                    borderColor: [ 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)' ],
                    borderWidth: 1
                  }]
                }, options: { scales: { y: { beginAtZero: true } }, indexAxis: 'y' }
              });
            }";
          }

          echo "if(document.getElementById('ChartReservierungen')){
            var ctx = document.getElementById('ChartReservierungen').getContext('2d');
            new Chart(ctx, {
              type: 'line',
              data: {
                labels: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
                datasets: [{
                  label: 'Reservierungen',
                  data: [$m0,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10,$m11],
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.5)','rgba(54, 162, 235, 0.5)','rgba(94, 62, 235, 0.5)','rgba(255, 159, 28, 0.5)','rgba(63, 55, 201, 0.5)','rgba(174, 217, 224, 0.5)',
                    'rgba(247, 37, 133, 0.5)','rgba(15, 76, 92, 0.5)','rgba(222, 170, 255, 0.5)','rgba(46, 196, 182, 0.5)','rgba(170, 204, 0, 0.5)','rgba(0, 127, 95, 0.5)'
                  ],
                  borderColor: [
                    'rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(94, 62, 235, 1)','rgba(255, 159, 28, 1)','rgba(63, 55, 201, 1)','rgba(174, 217, 224, 1)',
                    'rgba(247, 37, 133, 1)','rgba(15, 76, 92, 1)','rgba(222, 170, 255, 1)','rgba(46, 196, 182, 1)','rgba(170, 204, 0, 1)','rgba(0, 127, 95, 1)'
                  ],
                  borderWidth: 1
                }]
              },
              options: {
                scales: {
                  y: {
                    beginAtZero: true
                  }
                }
              }
            });
          }";
          if($tage != false){
            echo "var ctx2 = document.getElementById('ChartWochentage').getContext('2d');
            new Chart(ctx2, {
              type: 'doughnut',
              data: {
                labels: ['Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag'],
                datasets: [{
                  label: '# of Votes',
                  data: [$t0,$t1,$t2,$t3,$t4,$t5,$t6],
                  backgroundColor: [
                    'rgba(43, 147, 72, 0.5)', 'rgba(238, 108, 77, 0.5)', 'rgba(186, 24, 27, 0.5)',
                    'rgba(0, 109, 119, 0.5)', 'rgba(116, 0, 184, 0.5)', 'rgba(238, 130, 238,0.5)', 'rgba(255, 255, 71, 0.5)'
                  ],
                  borderColor: [
                    'rgba(43, 147, 72, 1)', 'rgba(238, 108, 77, 1)', 'rgba(186, 24, 27, 0.5)',
                    'rgba(0, 109, 119, 1)', 'rgba(116, 0, 184, 1)','rgba(238, 130, 238,1)','rgba(255, 255, 71, 1)'
                  ],
                  borderWidth: 1
                }]
              }
            });";
          }

          if($events != false){
            echo 'const animationDuration=2000; const frameDuration=1000/60;
            const totalFrames=Math.round(animationDuration/frameDuration);
            const eoq = t => t*(2-t);
            const animateCountUp = el => {
                let frame = 0;
                const countTo = parseInt( el.innerHTML, 10 );
                const counter = setInterval( () => {
                  frame++;
                  const progress = eoq( frame / totalFrames );
                  const currentCount = Math.round( countTo * progress );
                  if ( parseInt( el.innerHTML, 10 ) !== currentCount ) { el.innerHTML = currentCount; }
                  if ( frame === totalFrames ) {clearInterval( counter );}
                }, frameDuration );
              };
              const countupEls = document.querySelectorAll( ".events-counter" );
              countupEls.forEach( animateCountUp ); ';
          }

          if($buttons != false){
            echo "var ctx = document.getElementById('ChartButtons').getContext('2d');
            new Chart(ctx, {
              type: 'bar',
              data: {
                labels: ['Eingetroffen', 'Freigegeben', 'Abgesagt', 'No-Show', 'Abw. Anzahl'],
                datasets: [{
                  label: 'Buttons',
                  data: [$b0, $b1, $b2, $b3, $b4],
                  backgroundColor: [
                    'rgba(43, 147, 72, 0.5)', 'rgba(238, 108, 77, 0.5)', 'rgba(186, 24, 27, 0.5)',
                    'rgba(0, 109, 119, 0.5)', 'rgba(116, 0, 184, 0.5)'
                  ],
                  borderColor: [
                    'rgba(43, 147, 72, 1)', 'rgba(238, 108, 77, 1)', 'rgba(186, 24, 27, 0.5)',
                    'rgba(0, 109, 119, 1)', 'rgba(116, 0, 184, 1)'
                  ],
                  borderWidth: 1
                }]
              },
              options: {
                scales: {
                  y: {
                    beginAtZero: true
                  }
                }
              }
            });";
          }


          echo '</script>';

        } elseif(isset($_GET['liste'])) {
			$noshow = "'admin.php?liste=No-Show'"; $abwAnzahl = "'admin.php?liste=abwAnzahl'"; $liste = $_GET['liste'];
      echo '<div class="liste-nav">';
      if($liste == 'No-Show'){
        echo '<button style="background-color: #006d77; color: white; border: 1px solid white;" onClick="window.location.href='.$noshow.'">No-Show</button><button onClick="window.location.href='.$abwAnzahl.'">Abw. Anzahl</button>';
      } else {
        echo '<button onClick="window.location.href='.$noshow.'">No-Show</button><button style="background-color: #006d77; color: white; border: 1px solid white;" onClick="window.location.href='.$abwAnzahl.'">Abw. Anzahl</button>';
      }
      echo '</div>';


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
                          foreach ($block as $key) { echo '<option value="block-'.$key["id"].'" id="block-'.$key["id"].'">'.$key["start"].' - '.$key["end"].'</option>'; }
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
            $tAll = ''; $standorte = array();
            foreach ($tables as $key) { if($key['tableActive'] == 'open'){ $tAll = 'checked'; } if(!in_array($key['tablePlace'],$standorte)){ array_push($standorte,$key['tablePlace']); } }
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
        } elseif(isset($_GET['rechte'])){
          echo '<div class="rechte-container">';
            $overview = new Overview();
            $users = $overview->getUserData();

            foreach ($users as $key) {
              echo '<div class="rechte-data">';
                echo '<p id="name-'.$key["userID"].'">'.$key["userName"].'</p>';
                echo '<p>'.$key["userIP"].'</p>';
                if($key['userActive'] == '1'){
                  echo '<label class="switch"><input type="checkbox" class="switch-user" id="switch-user-'.$key["userID"].'" checked><span class="slider round"></span></label>';
                } else {
                  echo '<label class="switch"><input type="checkbox" class="switch-user" id="switch-user-'.$key["userID"].'"><span class="slider round"></span></label>';
                }
                echo '<div class="rechte-buttons">';
                  echo '<button id="button-bearbeiten" class="'.$key["userID"].'">Bearbeiten</button>';
                  echo '<button id="button-abmelden" class="'.$key["userID"].'">User abmelden</button>';
                  echo '<button id="button-delete" class="'.$key["userID"].'">Löschen</button>';
                echo '</div>';
                echo '</div>';
            }
            echo '<button id="button-useradd">Neuer User</button>';
          echo '</div>';
        }

        ?>
      </div>
    </div>
    <script type="text/javascript">
    /* RECHTE USER */
    $(document).on('change','.switch-user',function(){
      var check = $(this).prop("checked"); var id = $(this).attr('id').split('-')[2];
      if(check == false) { check = 0; } else { check=1; }
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { userSwitch: id+';'+check},
        success: function(result) {
          if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } return;
        }
      });
    });
    $(document).on('click','#button-abmelden',function(){
      var id = $(this).attr('class');
      $.ajax({
        url: "script/sync-admin.php", method: "POST", data: { userAbmelden: id},
        success: function(result) { if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } location.reload(); return; }
      });
    });
    $(document).on('click','#button-bearbeiten',function(){
      if($('#viewBearbeiten').length >= 1){ return; }
      const id = $(this).attr('class'); var name = $('#name-'+id).text();
      $('.rechte-container').append('<div id="viewBearbeiten"></div>');
      $('#viewBearbeiten').append('<i class="fa fa-times fa-2x" onClick="closeBearbeiten()"></i>');
      $('#viewBearbeiten').append('<input type="text" id="bearbeiten-name-'+id+'" value="'+name+'" placeholder="Name...">');
      $('#viewBearbeiten').append('<input type="password" id="bearbeiten-old-'+id+'" placeholder="Old Password">');
      $('#viewBearbeiten').append('<input type="password" id="bearbeiten-new-'+id+'" placeholder="New Password">');
      $('#viewBearbeiten').append('<button id="bearbeiten-save-'+id+'">Speichern</button>');
      $('#viewBearbeiten').css('display','flex');
    });
    function closeBearbeiten() { $('#viewBearbeiten').remove(); $('#viewBearbeiten').css('display','none'); }
    $(document).on('click','#viewBearbeiten button', function(){
      var id = $(this).attr('id').split('-')[2];
      var name = $('#bearbeiten-name-'+id).val(); var pw_old = $('#bearbeiten-old-'+id).val(); var pw_new = $('#bearbeiten-new-'+id).val();
      $.ajax({
        url: "script/sync-admin.php", method: "POST", data: { userBearbeiten: id+";"+name+";"+pw_old+";"+pw_new},
        success: function(result) { if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } location.reload(); return; }
      });
    });
    $(document).on('click','#button-useradd', function(){
      $.ajax({
        url: "script/sync-admin.php", method: "POST", data: { userAdd: 'true' },
        success: function(result) { if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } location.reload(); return; }
      });
    });
    $(document).on('click','#button-delete', function(){
      var id = $(this).attr('class');
      if(confirm('Möchten Sie den Benutzer '+$('#name-'+id).text()+' wirklich löschen?')){
        $.ajax({
          url: "script/sync-admin.php", method: "POST", data: { userDelete: id },
          success: function(result) { if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } location.reload(); return; }
        });
      }
    });





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
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } return;
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
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } return;
      }
    });
  });
  $(document).on('click','#bearbeiten-save',function(){

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
        if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } else { window.location.href='admin.php?zeit=HubRaum' } return;
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
          if(result!="1"){ alert("Ein Fehler ist aufgetreten \nFehlercode: " + result); } else { window.location.href='admin.php?zeit=HubRaum' } return;
        }
      });
    }
  });



    $(document).on("change","#oInputDate", function(){
      var date = $(this).val();
      var ow = getOverviewParameter();
      if(ow != false){
        window.location.href = "admin.php?overview="+ow+"&day="+date;
      }
    });

    $(document).on("click",".ow-nav li", function(){
      var type = $(this).text();
      if(type == "Tagesbericht"){
        window.location.href = "admin.php?overview=Day&day="+$('#oInputDate').val();
      } else if(type == "Wochenbericht") {
        window.location.href = "admin.php?overview=Week&day="+$('#oInputDate').val();
      } else {
        window.location.href = "admin.php?overview=full&day="+$('#oInputDate').val();
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
            alert("Ein Fehler ist aufgetreten \nFehlercode: " + result);
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
            $('#rs-amount').val(parseInt(d[0]['reserveAmount']));
            $('#rs-block').val('block-'+d[0]['reserveBlock']);
            $('#rs-block').change();

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

    $('#rs-block').change(function(){
      var rsBlock = $(this).val().split('-')[1]; var rsTime = $('#block-'+rsBlock).text();
      var rID = $('.reservierung-list-current').attr('id');
      $.ajax({
        url: "script/sync-admin.php",
        method: "POST",
        data: { updateReserveBlock: rID+";"+rsBlock+";"+rsTime },
        success: function(result) {
          console.log(result);
          if(result=="1"){ $(".reservierung-list-current").text(rsTime); return; }
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
