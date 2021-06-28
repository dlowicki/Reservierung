<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservierung | hubRaum Durlach</title>
	  <link rel="icon" type="image/png"  href="https://www.hubraum-durlach.de/wp-content/uploads/2015/08/Unbenannt-3.png">
    <!-- Style -->
    <link rel="stylesheet" href="css/change.css">
    <link rel="stylesheet" media="screen and (max-width: 700px)" href="css/reserve_small.css">
    <link rel="stylesheet" media="screen and (max-width: 1199px) and (min-width: 700px)" href="css/reserve_mid.css">
    <link rel="stylesheet" media="screen and (min-width: 1200px)" href="css/reserve.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <!-- Javascript -->
    <script src="script/md5.js"></script>
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="reserve.js" charset="utf-8"></script>
    <script src="js/script.calendar.js"> </script>
    <script src="js/script.login.js"> </script>
  </head>
  <body>
    <!--
    Reservierung
    Table für qrCode Registrierung: clientID, clientVorname, clientName, clientMail, clientAdresse, clientTNR, clientDate, clientConfirm, tableID
    Table für Tische: tableID, tableType, tableMax, tableMin, tableCode, tableActive
    Table für User: userID, userName, userPW, userIP, userActive
    Table für Reservierung: reserveID, tableID, clientID, reserveFrom, reserveTo, reserveAmount, reserve1, reserve2, reserve3, reserve4, reserve5, reserve6, reserve7, reserve8, reserve9, reserve10

    Tische müssen Händisch von Hubraum MA gesperrt werden können. (Geschlossenen Gesellschaft)
    Online Tische Reservieren & Corona Registrierung  mit Name und Adresse

    Icon für Personenanzahl
    DESIGN ändern 1120 pixel
    -->
    <div id="viewError"></div>
    <div id="viewTable"></div>
    <div id="viewLogin"></div>
    <div id="viewOverview"></div>
    <div id="viewCalendar"></div>

    <div class="container-header">
      <i class="far fa-calendar-alt fa-3x icon-calendar" style="padding: 0.5%;"></i>
      <i class="fa fa-user-circle fa-3x icon-user" style="padding: 0.5%;"></i>
    </div>

    <div class="container-reserve">
      <div class="container-tischplan"><svg width="1900" height="1080" xmlns="http://www.w3.org/2000/svg" id="tischplan-svg"></svg></div>
         <!--<g> <rect id="svg_15" height="90" width="195" y="5" x="615" stroke="#000" class="tisch-41" fill="rgba(60, 179, 113,0.5)"/> </g>-->
    </div>
  </body>
</html>
