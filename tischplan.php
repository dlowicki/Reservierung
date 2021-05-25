<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservierung | Hubraum</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="reserve.js" charset="utf-8"></script>
    <script src="fetch.js" charset="utf-8"></script>
  </head>


  <body>

    <div class="container-reserve">
      <div id="viewError"></div>
      <div id="viewTable"></div>
      <div id="viewLogin"></div>
      <div id="viewOverview"></div>
      <div id="viewCalendar"></div>

      <div class="container-header">
        <i class="far fa-calendar-alt fa-3x icon-calendar" style="padding: 0.5%;"></i>
        <i class="fa fa-user-circle fa-3x icon-user" style="padding: 0.5%;"></i>
      </div>

      <div class="container-tischplan">
        <svg width="1900" height="1080" xmlns="http://www.w3.org/2000/svg">
         <g>
          <title>Layer 1</title>
          <rect id="svg_5" height="90" width="97" y="178" x="103" stroke="#000" class="tisch-44"  fill="none"/>
          <rect id="svg_6" height="90" width="100" y="5" x="100" stroke="#000" class="tisch-43" fill="none"/>
          <rect id="svg_7" height="90" width="100" y="5" x="294" stroke="#000" class="tisch-42" fill="none"/>
          <rect id="svg_8" height="90" width="100" y="175" x="488" stroke="#000" class="tisch-46" fill="none"/>
          <rect id="svg_10" height="36" width="80" y="204" x="305" stroke="#000" class="tisch-45" fill="none"/>
          <rect id="svg_11" height="36" width="80" y="203" x="693" stroke="#000" class="tisch-47" fill="none"/>
          <rect id="svg_12" height="35" width="40" y="204" x="854" stroke="#000" class="tisch-63" fill="none"/>
          <rect id="svg_15" height="90" width="195" y="5" x="615" stroke="#000" class="tisch-41" fill="none"/>

          <rect id="svg_9" height="90" width="100" y="220" x="1310" stroke="#000" class="tisch-32" fill="none"/>
          <rect id="svg_13" height="35" width="160" y="188" x="982" stroke="#000" class="tisch-34" fill="none"/>


          <rect id="svg_16" height="73" width="40" y="338" x="213" stroke="#000" class="tisch-54" fill="none"/>
          <rect id="svg_17" height="73" width="40" y="338" x="385" stroke="#000" class="tisch-53" fill="none"/>
          <rect id="svg_18" height="73" width="40" y="338" x="555" stroke="#000" class="tisch-52" fill="none"/>
          <rect id="svg_19" height="38" width="38" y="474" x="215" stroke="#000" class="tisch-55" fill="none"/>
          <rect id="svg_20" height="38" width="40" y="474" x="385" stroke="#000" class="tisch-56" fill="none"/>

          <rect id="svg_21" height="38" width="40" y="490" x="855" stroke="#000" class="tisch-61" fill="none"/>
          <rect id="svg_22" height="38" width="40" y="387" x="855" stroke="#000" class="tisch-62" fill="none"/>
          <rect id="svg_23" height="73" width="40" y="338" x="728" stroke="#000" class="tisch-51" fill="none"/>
          <rect id="svg_24" height="72" width="40" y="485" x="555" stroke="#000" class="tisch-57" fill="none"/>
          <rect id="svg_25" height="75" width="40" y="483" x="727" stroke="#000" class="tisch-58" fill="none"/>
          <rect id="svg_26" height="36" width="197" y="372" x="982" stroke="#000" class="tisch-35" fill="none"/>
          <rect id="svg_27" height="36" width="197" y="490" x="982" stroke="#000" class="tisch-36" fill="none"/>

          <rect id="svg_28" height="180" width="100" y="380" x="1310" stroke="#000" class="tisch-31" fill="none"/>
          <rect id="svg_29" height="87" width="200" y="50" x="1212" stroke="#000" class="tisch-33" fill="none"/>

          <rect id="svg_30" height="180" width="100" y="658" x="1775" stroke="#000" class="tisch-98" fill="none"/>
          <rect id="svg_31" height="180" width="100" y="895" x="1775" stroke="#000" class="tisch-99" fill="none"/>

          <rect id="svg_32" height="150" width="35" y="410" x="1444" stroke="#000" class="tisch-18" fill="none"/>
          <rect id="svg_33" height="32" width="150" y="374" x="1638" stroke="#000" class="tisch-11" fill="none"/>
          <rect id="svg_34" height="32" width="150" y="284" x="1638" stroke="#000" class="tisch-12" fill="none"/>
          <rect id="svg_35" height="32" width="150" y="257" x="1430" stroke="#000" class="tisch-17" fill="none"/>
          <rect id="svg_36" height="32" width="150" y="106" x="1430" stroke="#000" class="tisch-16" fill="none"/>
          <rect id="svg_37" height="32" width="150" y="106" x="1637" stroke="#000" class="tisch-13" fill="none"/>
          <rect id="svg_38" height="32" width="150" y="16" x="1637" stroke="#000" class="tisch-14" fill="none"/>
          <rect id="svg_39" height="32" width="150" y="16" x="1430" stroke="#000" class="tisch-15" fill="none"/>

          <rect id="svg_59" height="33" width="35" y="625" x="1133" stroke="#000" class="tisch-22" fill="none"/>
          <rect id="svg_58" height="33" width="35" y="625" x="1254" stroke="#000" class="tisch-21" fill="none"/>
          <rect id="svg_60" height="33" width="35" y="625" x="1010" stroke="#000" class="tisch-23" fill="none"/>

          <rect id="svg_57" height="98" width="37" y="682" x="1211" stroke="#000" class="tisch-27" fill="none"/>
          <rect id="svg_56" height="98" width="36" y="682" x="1092" stroke="#000" class="tisch-26" fill="none"/>
          <rect id="svg_55" height="98" width="34" y="682" x="974" stroke="#000" class="tisch-25" fill="none"/>
          <rect id="svg_54" height="98" width="34" y="682" x="854" stroke="#000" class="tisch-24" fill="none"/>

          <rect id="svg_40" height="32" width="59.99999" y="1031" x="1120" stroke="#000" class="tisch-77" fill="none"/>
          <rect id="svg_41" height="32" width="60" y="951" x="1120" stroke="#000" class="tisch-76" fill="none"/>
          <rect id="svg_42" height="32" width="34" y="1031" x="1035" stroke="#000" class="tisch-78" fill="none"/>
          <rect id="svg_43" height="32" width="34" y="951" x="1035" stroke="#000" class="tisch-79" fill="none"/>

          <rect id="svg_44" height="32" width="36" y="870" x="637" stroke="#000" class="tisch-84" fill="none"/>
          <rect id="svg_45" height="32" width="36" y="870" x="530" stroke="#000" class="tisch-85" fill="none"/>
          <rect id="svg_46" height="55" width="34" y="785" x="483" stroke="#000" class="tisch-81" fill="none"/>
          <rect id="svg_47" height="55" width="34" y="785" x="585" stroke="#000" class="tisch-82" fill="none"/>
          <rect id="svg_48" height="55" width="37" y="785" x="685" stroke="#000" class="tisch-83" fill="none"/>

          <rect id="svg_49" height="55" width="37" y="785" x="836" stroke="#000" class="tisch-71" fill="none"/>
          <rect id="svg_50" height="55" width="37" y="785" x="938" stroke="#000" class="tisch-72" fill="none"/>
          <rect id="svg_51" height="55" width="37" y="785" x="1040" stroke="#000" class="tisch-73" fill="none"/>
          <rect id="svg_52" height="55" width="38" y="785" x="1140" stroke="#000" class="tisch-74" fill="none"/>
          <rect id="svg_53" height="34" width="177" y="870" x="925" stroke="#000" class="tisch-75" fill="none"/>
         </g>
        </svg>
      </div>

    </div>
  </body>
</html>
