<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservierung | Hubraum</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="script/md5.js"></script>
    <script src="js/script.login.js" charset="utf-8"></script>

    <style media="screen">
      * { margin: 0; padding: 0; }
      .tischplan-header { height: 0px; width: 100%; position: fixed; }
      .tischplan-header i { transition: 0.5s; }
      .tischplan-header i:hover { color: darkgray; cursor: pointer; }
      .container-tischplan { width: 1900px; height:1080px; background-image: url('tischplan.jpg'); background-size: 100% 100%; image-rendering: pixelated; }
      rect { cursor: pointer; transition: 0.5s; touch-action: none; }
      rect:hover { opacity: 0.3; }

      #viewEdit {
        display: none; position: fixed; top: 0; left: 0; min-height: 100%; height: auto; width: 300px;
        font-family: sans-serif; background-color: rgba(255,255,255,0.9); border: 2px solid black;
      }
      #viewEdit i { float: right; padding: 5px 15px 0px 0px; cursor: pointer; transition: 0.5s; }
      #viewEdit i:hover { color: red; }
      #viewEdit h2 { width: 100%; padding: 5px; font-size: 2rem; text-align: center; }
      #viewEdit h5 { font-size: 1.2rem; padding: 5px; margin: 20px 0px 20px 0px; border-top: 2px solid darkgray;}
      .colorBox { width: 100%; height: 120px; text-align: center; }
      .radio-label { cursor: pointer; width: 35px; height: 35px; margin: 2%; display: inline-block; border: 3px solid transparent; }
      .radio-label-current { border: 3px solid black; }

      .uploadBox { width: 100%; height: auto; margin-top: 10px; text-align: center; overflow: hidden; }
      .uploadBox input { font-size: 1rem;  }
      .uploadBox input[type=button] { cursor: pointer; background-color: white; margin-top: 20px; border: 1px solid black; width: 100px; padding: 2px;}

      .objektBox { width: 75%; height: auto; margin-top: 10px; margin-left: auto; margin-right: auto; }
      .objektBox h3 { text-align: center; }
      .objekt-data { display: flex;  font-size: 1.3rem; flex-direction: row; width: 100%; margin: 10px 0px 0px 0px; }
      .objekt-data:first-of-type { display: flex; text-align: center; font-size: 1.3rem; width: 100%; margin: 10px 0px 0px 0px; }
      .objekt-data:first-of-type button { margin-top: 0px; margin-bottom: 15px; }
      .objekt-data label { width: 65px; margin: 0px 10px 0px 10px; text-align: center; }
      .objekt-data input { width: 100px; outline: none; font-size: 1.3rem;  }
      .objektBox button { width: 100px; margin-left: auto; margin-right: auto; margin-top: 15px; padding: 5px; border: 1px solid black; cursor: pointer; }

      .navBox { text-align: center; }
      .navBox button { margin-left: 10px; margin-right: 10px; padding: 5px 5px 5px 5px; background-color: white; cursor: pointer; border: 1px solid black; width: 100px; font-size: 1.2rem; }

      /* SWITCH */
      .switch { position: relative; display: inline-block; width: 62px; height: 34px; }
      .switch input { opacity: 0; width: 0; height: 0; }
      .slider { position: absolute; cursor: pointer;   top: 0; left: 0; right: 0; bottom: 0; background-color: blue; -webkit-transition: .4s; transition: .4s; }
      .slider:before { position: absolute; content: ""; height: 26px;   width: 26px; left: 4px; bottom: 4px; background-color: white; -webkit-transition: .4s; transition: .4s; }
      input:checked + .slider { background-color: green; }
      input:focus + .slider { box-shadow: 0 0 1px green; }
      input:checked + .slider:before { -webkit-transform: translateX(26px); -ms-transform: translateX(26px); transform: translateX(26px); }
      .slider.round { border-radius: 34px; }
      .slider.round:before { border-radius: 50%; }
    </style>

  </head>


  <body>

    <div class="tischplan-header">
      <i class="fas fa-edit fa-3x" style="padding: 0.5%;"></i>
    </div>

    <div class="container-tischplan">
      <?php
        require_once('sync.php');
        $con = connect();
        $querySettings = $con->query('SELECT * FROM rsettings');
        if($querySettings){
          $settings = array();
          foreach ($querySettings as $key) { $settings[$key['sEinstellung']] = $key['sWert']; }
        }
      ?>
      <div id="viewEdit">
        <i class="fas fa-times fa-2x"></i>
        <!-- Farbe ändern | ausgewähltes Objekt x,y,width,height ändern | Bild zum hochladen Button | Speichern button -->
        <h2>Einstellungen</h2>
        <h5>Farben</h5>
        <div class="colorBox">
          <?php
          $farben = array('rgba(255, 0, 0, 0.9)','rgba(0, 0, 255, 0.9)','rgba(0, 255, 0, 0.9)','rgba(255, 255, 0, 0.9)','rgba(255, 0, 255, 0.9)','rgba(0, 255, 255, 0.9)');
          $r=0;
          foreach ($farben as $key => $value) {
            if($value == $settings['farbe']){
              echo '<label class="radio-label radio-label-current" id="farbe'.$r.'" style="background-color: '.$value.'"></label>';
            } else {
              echo '<label class="radio-label" id="farbe'.$r.'" style="background-color: '.$value.'"></label>';
            }
            $r++;
          }
          ?>
        </div>
        <h5>Objekt</h5>
        <div class="objektBox">
          <div class="objekt-data"><button id="button-add">Hinzufügen</button></div>
          <h3>Unbekannt</h3>
          <div class="objekt-data"><label>X</label><input type="number" id="obj-x" placeholder="X..."></div>
          <div class="objekt-data"><label>Y</label><input type="number" id="obj-y" placeholder="Y..."></div>
          <div class="objekt-data"><label>Width</label><input type="number" id="obj-width" placeholder="Width..."></div>
          <div class="objekt-data"><label>Height</label><input type="number" id="obj-height" placeholder="Height..."></div>
          <div class="objekt-data"><label>Place</label><input type="text" id="obj-place" placeholder="Place..."></div>
          <div class="objekt-data"><button id="button-delete">Entfernen</button><button id="button-speichern">Speichern</button></div>
        </div>
        <h5>Hintergrund</h5>
        <div class="uploadBox">
          <form enctype="multipart/form-data"> <input name="file" type="file" /> <input type="button" value="Upload" /> </form>
        </div>
        <h5>Navigation</h5>
        <div class="navBox">
          <button onclick="window.location.href='index.php'">Tischplan</button>
          <button onclick="window.location.href='admin.php'">Admin</button>
        </div>
      </div>
      <svg width="1900" height="1080" xmlns="http://www.w3.org/2000/svg" id="tischplan-svg">
        <?php
        $query = $con -> query("SELECT * FROM rtable") or die();
        if($query){
          foreach ($query as $key) {
            echo '<rect id="'.$key["tableID"].'" class="draggable" height="'.$key["tableHeight"].'" width="'.$key["tableWidth"].'" x="'.$key["tableX"].'" data-x="" y="'.$key["tableY"].'" data-y="" place="'.$key["tablePlace"].'" stroke="#000" fill="'.$settings["farbe"].'" />';
          }
        }
        ?>
      </svg>
    </div>

    <script type="text/javascript">
      $('.fa-edit').click(()=>{
        if($('#viewEdit:visible').length == 0){
          $('.container-tischplan').css('margin-left','300px');
          $('#viewEdit').css('display','block');
        }
      });
      $('.fa-times').click(()=>{
        $('#viewEdit').css('display','none');
        $('.container-tischplan').css('margin-left','0px');
      });
      $('.radio-label').click((event)=>{
        const current_color = $('#'+event.target.id).css('background-color');
        $.ajax({ url: "script/sync-tischplan.php", method: "POST", data: { changeColor: current_color },
        success: function(result) {
          if(result=='1'){
            location.reload(); return; //$('#tischplan-svg').load(" #tischplan-svg > *");
          }
          alert('Ein Fehler ist aufgetreten! \n'+result); return;
        }
        });
      });

      $('.objekt-data input').change((event)=>{
        if($('.objektBox h3').text() == "Unbekannt"){ alert('Bitte zuerst einen Tisch auswählen!'); return; }
        var objID = $('.objektBox h3').text();
        if(event.target.id == 'obj-x'){ $('#'+objID).attr('x',event.target.value); }
        if(event.target.id == 'obj-y'){ $('#'+objID).attr('y',event.target.value); }
        if(event.target.id == 'obj-width'){ $('#'+objID).attr('width',event.target.value); }
        if(event.target.id == 'obj-height'){ $('#'+objID).attr('height',event.target.value); }
      });

      $('rect').click(function(){
        if($('#viewEdit:visible').length == 0){
          $('.container-tischplan').css('margin-left','300px');
          $('#viewEdit').css('display','block');
        }
        $('.objektBox h3').text($(this).attr('id'));
        $('#obj-x').val($(this).attr('x'));
        $('#obj-y').val($(this).attr('y'));
        $('#obj-width').val($(this).attr('width'));
        $('#obj-height').val($(this).attr('height'));
        $('#obj-place').val($(this).attr('place'));
      });

      $('#button-speichern').click(()=>{
        var objID = $('.objektBox h3').text();
        if(objID == "Unbekannt"){ alert('Bitte zuerst einen Tisch auswählen!'); return; }
        var place = $('#obj-place').val(); var objX = $('#obj-x').val(); var objY = $('#obj-y').val();
        var objW = $('#obj-width').val(); var objH = $('#obj-height').val();
        $.ajax({ url: "script/sync-tischplan.php", method: "POST", data: { saveTisch:objID+";"+objX+";"+objY+";"+objW+";"+objH+";"+place },
        success: function(result) {
          if(result=='1'){ location.reload(); return; }
          alert('Tisch konnte nicht aktualisiert werden! \n Fehler: '+result); return;
        }
        });
      });
      $('#button-delete').click(() => {
        var objID = $('.objektBox h3').text();
        if(objID == "Unbekannt"){ alert('Bitte zuerst einen Tisch auswählen!'); return; }
        if(confirm('Soll Tisch '+objID+' wirklich entfernt werden?')){
          $.ajax({ url: "script/sync-tischplan.php", method: "POST", data: { deleteTisch:objID },
          success: function(result) {
            if(result=='1'){ location.reload(); return; } alert('Tisch konnte nicht entfernt werden! \n Fehler: '+result); return;
          }
          });
        }
      });
      $('#button-add').click(()=>{
        $.ajax({ url: "script/sync-tischplan.php", method: "POST", data: { addTisch:'true' },
        success: function(result) {
          if(result=='1'){ location.reload(); return; } alert('Tisch konnte nicht erstellt werden! \n Fehler: '+result); return;
        }
        });
      });
    </script>

    <script type="module">
    import interact from 'https://cdn.interactjs.io/v1.10.11/interactjs/index.js';
    $('.draggable').click(function(){
      var element = $(this).attr('id'); var x = 0; var y = 0;
      interact('.draggable')
      .draggable({
        modifiers: [
          interact.modifiers.snap({
            targets: [ interact.snappers.grid({ x: 1, y: 1 }) ]
          }),
          interact.modifiers.restrictRect({
            restriction: 'parent', endOnly: true
          })
        ],
        listeners: {
          move: dragMoveListener,
          end (event){
            var dataY = $('#'+element).attr('data-y'); var dataX = $('#'+element).attr('data-x');
            var newY = (parseInt($('#'+element).attr('y')) + parseInt(dataY)); var newX = (parseInt($('#'+element).attr('x')) + parseInt(dataX));
            //event.target.setAttribute('x', newX); event.target.setAttribute('y', newY);
            $('#obj-x').val(newX); $('#obj-y').val("");
            updateTisch(newX,newY,element);
          }
        },
        inertia: false,
        autoScroll: true
      })

      function dragMoveListener (event) {
        var target = event.target;
        //target.style.zIndex = 2;
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
        target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
        target.setAttribute('data-x', x); target.setAttribute('data-y', y);

        //var newY = (parseInt($('#'+element).attr('y')) + parseInt(y)); var newX = (parseInt($('#'+element).attr('x')) + parseInt(x));
        //console.log(newX + " - " + newY);
      }

      function updateTisch(x,y,id) {
        $.ajax({ url: "script/sync-tischplan.php", method: "POST", data: { updateTisch: x+";"+y+";"+id },
        success: function(result) {
          if(result!='1'){
            alert('Tisch konnte nicht aktualisiert werden! \n Fehler: '+result); return;
          }
          $('#obj-x').val(x); $('#obj-y').val(y);
        }
        });
      }
    });
    </script>
  </body>
</html>
