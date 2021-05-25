<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservierung | Hubraum</title>
    <!-- <link rel="stylesheet" href="css/table.css"> -->
    <link rel="stylesheet" href="css/change.css">
    <link rel="stylesheet" media="screen and (max-width: 700px)" href="css/reserve_small.css">
    <link rel="stylesheet" media="screen and (max-width: 1199px) and (min-width: 700px)" href="css/reserve_mid.css">
    <link rel="stylesheet" media="screen and (min-width: 1200px)" href="css/reserve.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="script/md5.js"></script>
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="reserve.js" charset="utf-8"></script>
    <script src="fetch.js" charset="utf-8"></script>

    <style media="screen">

    </style>

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

    <?php
    if(isset($_GET['change'])){
      $change = $_GET['change'];
      if(strlen($change) >= 1 && strlen($change) <= 15){
        echo "<div id='viewChangeReserve' class='$change'> </div>";
        return;
      }
    }
    ?>

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
          <?php
          /*$con = new mysqli("localhost", "shop", "123456", "reservierung"); // Create connection
          if ($con->connect_error) {  die("Connection failed: " . $con->connect_error); return; }

          $query = $con->query('SELECT tableID, tableWidth, tableHeight, tableX, tableY, tableActive FROM rTable');
          if($query){
            foreach ($query as $key) {
              echo '<rect id="tisch-'.$key["tableID"].'" width="'.$key["tableWidth"].'" height="'.$key["tableHeight"].'" y="'.$key["tableY"].'" x="'.$key["tableX"].'" ';
              if($key['tableActive'] == 'open'){ echo 'fill="rgba(60, 179, 113,0.5)"'; } else { echo 'fill="rgba(255, 0, 0,0.5)"'; }
              echo '/>';
            }
          }*/
          ?>


         <!--<g>

    <rect id="svg_15" height="90" width="195" y="5" x="615" stroke="#000" class="tisch-41" fill="rgba(60, 179, 113,0.5)"/>
    <rect id="svg_7" height="90" width="100" y="5" x="294" stroke="#000" class="tisch-42" fill="transparent"/>
    <rect id="svg_6" height="90" width="100" y="5" x="100" stroke="#000" class="tisch-43" fill="transparent"/>
    <rect id="svg_5" height="90" width="97" y="178" x="103" stroke="#000" class="tisch-44"  fill="transparent"/>
    <rect id="svg_10" height="36" width="80" y="204" x="305" stroke="#000" class="tisch-45" fill="rgba(60, 179, 113,0.5)"/>
    <rect id="svg_8" height="90" width="100" y="175" x="488" stroke="#000" class="tisch-46" fill="transparent"/>
    <rect id="svg_11" height="36" width="80" y="203" x="693" stroke="#000" class="tisch-47" fill="transparent"/>



    <rect id="svg_28" height="180" width="100" y="380" x="1310" stroke="#000" class="tisch-31" fill="transparent"/>
    <rect id="svg_9" height="90" width="100" y="220" x="1310" stroke="#000" class="tisch-32" fill="transparent"/>
    <rect id="svg_29" height="87" width="200" y="50" x="1212" stroke="#000" class="tisch-33" fill="transparent"/>
    <rect id="svg_13" height="35" width="160" y="188" x="982" stroke="#000" class="tisch-34" fill="transparent"/>
    <rect id="svg_26" height="36" width="197" y="372" x="982" stroke="#000" class="tisch-35" fill="transparent"/>
    <rect id="svg_27" height="36" width="197" y="490" x="982" stroke="#000" class="tisch-36" fill="transparent"/>

    <rect id="svg_23" height="73" width="40" y="338" x="728" stroke="#000" class="tisch-51" fill="transparent"/>
    <rect id="svg_18" height="73" width="40" y="338" x="555" stroke="#000" class="tisch-52" fill="transparent"/>
    <rect id="svg_17" height="73" width="40" y="338" x="385" stroke="#000" class="tisch-53" fill="transparent"/>
    <rect id="svg_16" height="73" width="40" y="338" x="213" stroke="#000" class="tisch-54" fill="transparent"/>
    <rect id="svg_19" height="38" width="38" y="474" x="215" stroke="#000" class="tisch-55" fill="transparent"/>
    <rect id="svg_20" height="38" width="40" y="474" x="385" stroke="#000" class="tisch-56" fill="transparent"/>
    <rect id="svg_24" height="72" width="40" y="485" x="555" stroke="#000" class="tisch-57" fill="transparent"/>
    <rect id="svg_25" height="75" width="40" y="483" x="727" stroke="#000" class="tisch-58" fill="transparent"/>

    <rect id="svg_21" height="38" width="40" y="490" x="855" stroke="#000" class="tisch-61" fill="transparent"/>
    <rect id="svg_22" height="38" width="40" y="387" x="855" stroke="#000" class="tisch-62" fill="transparent"/>
    <rect id="svg_12" height="35" width="40" y="204" x="854" stroke="#000" class="tisch-63" fill="transparent"/>




          <rect id="svg_30" height="180" width="100" y="658" x="1775" stroke="#000" class="tisch-98" fill="transparent"/>
          <rect id="svg_31" height="180" width="100" y="895" x="1775" stroke="#000" class="tisch-99" fill="transparent"/>


    <rect id="svg_33" height="32" width="150" y="374" x="1638" stroke="#000" class="tisch-11" fill="transparent"/>
    <rect id="svg_34" height="32" width="150" y="284" x="1638" stroke="#000" class="tisch-12" fill="transparent"/>
    <rect id="svg_37" height="32" width="150" y="106" x="1637" stroke="#000" class="tisch-13" fill="transparent"/>
    <rect id="svg_38" height="32" width="150" y="16" x="1637" stroke="#000" class="tisch-14" fill="transparent"/>
    <rect id="svg_39" height="32" width="150" y="16" x="1430" stroke="#000" class="tisch-15" fill="transparent"/>
    <rect id="svg_36" height="32" width="150" y="106" x="1430" stroke="#000" class="tisch-16" fill="transparent"/>
    <rect id="svg_35" height="32" width="150" y="257" x="1430" stroke="#000" class="tisch-17" fill="transparent"/>
    <rect id="svg_32" height="150" width="35" y="410" x="1444" stroke="#000" class="tisch-18" fill="transparent"/>


    <rect id="svg_58" height="33" width="35" y="625" x="1254" stroke="#000" class="tisch-21" fill="transparent"/>
    <rect id="svg_59" height="33" width="35" y="625" x="1133" stroke="#000" class="tisch-22" fill="transparent"/>
    <rect id="svg_60" height="33" width="35" y="625" x="1010" stroke="#000" class="tisch-23" fill="transparent"/>



    <rect id="svg_54" height="98" width="34" y="682" x="854" stroke="#000" class="tisch-24" fill="transparent"/>
    <rect id="svg_55" height="98" width="34" y="682" x="974" stroke="#000" class="tisch-25" fill="transparent"/>
    <rect id="svg_56" height="98" width="36" y="682" x="1092" stroke="#000" class="tisch-26" fill="transparent"/>
    <rect id="svg_57" height="98" width="37" y="682" x="1211" stroke="#000" class="tisch-27" fill="transparent"/>


    <rect id="svg_41" height="32" width="60" y="951" x="1120" stroke="#000" class="tisch-76" fill="transparent"/>
    <rect id="svg_40" height="32" width="60" y="1031" x="1120" stroke="#000" class="tisch-77" fill="transparent"/>
    <rect id="svg_42" height="32" width="34" y="1031" x="1035" stroke="#000" class="tisch-78" fill="transparent"/>
    <rect id="svg_43" height="32" width="34" y="951" x="1035" stroke="#000" class="tisch-79" fill="transparent"/>

          <rect id="svg_46" height="55" width="34" y="785" x="483" stroke="#000" class="tisch-81" fill="transparent"/>
          <rect id="svg_47" height="55" width="34" y="785" x="585" stroke="#000" class="tisch-82" fill="transparent"/>
          <rect id="svg_48" height="55" width="37" y="785" x="685" stroke="#000" class="tisch-83" fill="transparent"/>
          <rect id="svg_44" height="32" width="36" y="870" x="637" stroke="#000" class="tisch-84" fill="transparent"/>
          <rect id="svg_45" height="32" width="36" y="870" x="530" stroke="#000" class="tisch-85" fill="transparent"/>

    <rect id="svg_49" height="55" width="37" y="785" x="836" stroke="#000" class="tisch-71" fill="transparent"/>
    <rect id="svg_50" height="55" width="37" y="785" x="938" stroke="#000" class="tisch-72" fill="transparent"/>
    <rect id="svg_51" height="55" width="37" y="785" x="1040" stroke="#000" class="tisch-73" fill="transparent"/>
    <rect id="svg_52" height="55" width="38" y="785" x="1140" stroke="#000" class="tisch-74" fill="transparent"/>
    <rect id="svg_53" height="34" width="177" y="870" x="925" stroke="#000" class="tisch-75" fill="transparent"/>
  </g>-->



    </div>
  </body>
</html>
