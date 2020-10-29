<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <link href="reserve.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="reserve.js" charset="utf-8"></script>
    <script src="fetch.js" charset="utf-8"></script>
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

    <div class="container-reserve">
      <i class="fa fa-user-circle fa-3x icon-user"></i>
      <div class="reserve-top-left">
        <div class="table object-t-right" id="99">
          <h3 class="table-h3" id="99-h3">99</h3>
          <img src="img/t-right-transparent.png" id="99-img" width="100%">
        </div>
        <br>
        <div class="table pillar" id="98">
          <h3 class="pillar-h3" id="98-h3">98</h3>
          <img src="img/t-pillar-transparent.png" id="98-img" width="100%">
        </div>
      </div>







      <div class="reserve-top-middle">
        <div class="table pillar" id="21" style="float: left; margin-right: 20px;">
          <h3 class="pillar-h3" id="21-h3">21</h3>
          <img src="img/t-pillar-transparent.png" id="21-img" width="100%">
        </div>
        <div class="table pillar" id="22" style="float: left; margin-right: 20px;">
          <h3 class="pillar-h3" id="22-h3">22</h3>
          <img src="img/t-pillar-transparent.png" id="22-img" width="100%">
        </div>
        <div class="table pillar" id="23" style="float: left; margin-right: 20px;">
          <h3 class="pillar-h3" id="23-h3">23</h3>
          <img src="img/t-pillar-transparent.png" id="23-img" width="100%">
        </div>
        <div class="table pillar" id="24" style="float: left;">
          <h3 class="pillar-h3" id="24-h3">24</h3>
          <img src="img/t-pillar-transparent.png" id="24-img" width="100%">
        </div>
        <div class="table pillar" id="27" style="float: left; margin-left: 30px; margin-right: 25px; margin-top: 5%;">
          <h3 class="pillar-h3" id="27-h3">27</h3>
          <img src="img/t-one-transparent.png" id="27-img" width="100%">
        </div>
        <div class="table pillar" id="26" style="float: left; margin-right: 25px; margin-top: 5%;">
          <h3 class="pillar-h3" id="26-h3">26</h3>
          <img src="img/t-one-transparent.png" id="26-img" width="100%">
        </div>
        <div class="table pillar" id="25" style="float: left; margin-top: 5%;">
          <h3 class="pillar-h3" id="25-h3">25</h3>
          <img src="img/t-one-transparent.png" id="25-img" width="100%">
        </div>
      </div>







      <div class="reserve-bottom">

        <div class="reserve-bottom-left">
          <div class="reserve-bottom-left-row">
            <div class="table pillar" id="8" style="float: right;">
              <h3 class="pillar-h3" id="8-h3">8</h3>
              <img src="img/t-one-transparent.png" id="8-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-left-row">
            <div class="table row" id="9" style="float: right;">
              <h3 class="row-h3" id="9-h3">9</h3>
              <img src="img/t-row-transparent.png" id="9-img" width="100%">
            </div>
            <div class="table pillar" id="1">
              <h3 class="pillar-h3" id="1-h3">1</h3>
              <img src="img/t-one-transparent.png" id="1-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-left-row">
            <div class="table row" id="10" style="float: right;">
              <h3 class="row-h3" id="10-h3">10</h3>
              <img src="img/t-row-transparent.png" id="10-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-left-row">
            <div class="table row" id="11" style="float: right;">
              <h3 class="row-h3" id="11-h3">11</h3>
              <img src="img/t-row-transparent.png" id="11-img" width="100%">
            </div>
            <div class="table pillar" id="2">
              <h3 class="pillar-h3" id="2-h3">2</h3>
              <img src="img/open/t-one-transparent.png" id="2-img" width="100%">
            </div>
        </div>

          <div class="reserve-bottom-left-row">
            <div class="table row" id="12" style="float: right;">
              <h3 class="row-h3" id="12-h3">12</h3>
              <img src="img/closed/t-row-transparent.png" id="12-img" width="100%">
            </div>
            <div class="table pillar" id="3">
              <h3 class="pillar-h3" id="3-h3">3</h3>
              <img src="img/t-one-transparent.png" id="3-img" width="100%">
            </div>
          </div>
        </div>

        <div class="reserve-bottom-middle">
          <div class="reserve-bottom-middle-row">
            <div class="table pillar" id="31" style="float: left;">
              <h3 class="pillar-h3" id="31-h3">31</h3>
              <img src="img/t-one-transparent.png" id="31-img" width="100%">
            </div>
            <div class="table pillar" id="32" style="float: left;">
              <h3 class="pillar-h3" id="32-h3">32</h3>
              <img src="img/t-one-transparent.png" id="32-img" width="100%">
            </div>
            <div class="table pillar" id="33" style="float: left;">
              <h3 class="pillar-h3" id="33-h3">33</h3>
              <img src="img/t-one-transparent.png" id="33-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-middle-row">
            <div class="table object-t-right" id="37">
              <h3 class="table-h3" id="37-h3">37</h3>
              <img src="img/t-right-transparent.png" id="37-img" width="100%">
            </div>
            <div class="table object-t-left" id="34">
              <h3 class="table-h3" id="34-h3">34</h3>
              <img src="img/t-left-transparent.png" id="34-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-middle-row">
            <div class="table object-t-left" id="36" style="float: left;">
              <h3 class="bottom-h3 h3-bottom" id="36-h3">36</h3>
              <img src="img/t-left-transparent.png" id="36-img" width="100%" style="transform: scale(-1);">
            </div>
            <div class="table pillar" id="35" style="float: left;  margin-left: 25%;">
              <h3 class="pillar-h3" id="35-h3">35</h3>
              <img src="img/t-one-transparent.png" id="35-img" width="100%">
            </div>
          </div>

        </div>



        <div class="reserve-bottom-right">
          <div class="reserve-bottom-right-row">
            <div class="table pillar" id="41" style="float: left;">
              <h3 class="pillar-h3" id="41-h3">41</h3>
              <img src="img/t-pillar-transparent.png" id="41-img" width="100%">
            </div>
            <div class="table pillar" id="43" style="float: left;">
              <h3 class="pillar-h3" id="43-h3">43</h3>
              <img src="img/t-pillar-transparent.png" id="43-img" width="100%">
            </div>
            <div class="table pillar" id="45" style="float: left;">
              <h3 class="pillar-h3" id="45-h3">45</h3>
              <img src="img/t-pillar-transparent.png" id="45-img" width="100%">
            </div>
            <div class="table pillar" id="47" style="float: left;">
              <h3 class="pillar-h3" id="47-h3">47</h3>
              <img src="img/t-pillar-transparent.png" id="47-img" width="100%">
            </div>
            <div class="table pillar" id="49" style="float: left;">
              <h3 class="pillar-h3" id="49-h3">49</h3>
              <img src="img/t-pillar-transparent.png" id="49-img" width="100%">
            </div>
          </div>



          <div class="reserve-bottom-right-row">
            <div class="table pillar" id="42" style="float: left;">
              <h3 class="pillar-h3" id="42-h3">42</h3>
              <img src="img/t-pillar-transparent.png" id="42-img" width="100%">
            </div>
            <div class="table pillar" id="44" style="float: left;">
              <h3 class="pillar-h3" id="44-h3">44</h3>
              <img src="img/t-pillar-transparent.png" id="44-img" width="100%">
            </div>
            <div class="table pillar" id="46" style="float: left;">
              <h3 class="pillar-h3" id="46-h3">46</h3>
              <img src="img/t-pillar-transparent.png" id="46-img" width="100%">
            </div>
            <div class="table pillar" id="48" style="float: left;">
              <h3 class="pillar-h3" id="48-h3">48</h3>
              <img src="img/t-pillar-transparent.png" id="48-img" width="100%">
            </div>
            <div class="table pillar" id="50" style="float: left;">
              <h3 class="pillar-h3" id="50-h3">50</h3>
              <img src="img/t-pillar-transparent.png" id="50-img" width="100%">
            </div>
          </div>



          <div class="reserve-bottom-right-row">
            <div class="table pillar" id="51" style="float: left;">
              <h3 class="pillar-h3" id="51-h3">51</h3>
              <img src="img/t-one-transparent.png" id="51-img" width="100%">
            </div>
            <div class="table big" id="52" style="float: left;">
              <h3 class="big-h3" id="52-h3">52</h3>
              <img src="img/t-big-transparent.png" id="52-img" width="100%">
            </div>
            <div class="table pillar" id="53" style="float: left;">
              <h3 class="pillar-h3" id="53-h3">53</h3>
              <img src="img/t-one-transparent.png" id="53-img" width="100%">
            </div>
            <div class="table big" id="54" style="float: left;">
              <h3 class="big-h3" id="54-h3">54</h3>
              <img src="img/t-big-transparent.png" id="54-img" width="100%">
            </div>
          </div>

          <div class="reserve-bottom-right-row">
            <div class="table bottom" id="57" style="float: left;">
              <h3 class="bottom-h3" id="57-h3">57</h3>
              <img src="img/t-right-transparent.png" id="57-img" width="100%" style="transform: rotate(270deg);">
            </div>
            <div class="table big" id="56" style="float: left;">
              <h3 class="big-h3" id="56-h3">56</h3>
              <img src="img/t-big-transparent.png" id="56-img" width="100%">
            </div>
            <div class="table big" id="55" style="float: left;">
              <h3 class="big-h3" id="55-h3">55</h3>
              <img src="img/t-big-transparent.png" id="55-img" width="100%">
            </div>
          </div>


        </div>
      </div>

      <div id="viewAdminTable"></div>
      <div id="viewTable"></div>
      <div id="viewLogin"></div>
      <div id="viewOverview"></div>
    </div>
  </body>
</html>
