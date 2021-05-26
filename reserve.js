$(document).ready(function(){
  // DEBUG
  //viewTable(98);
  //viewReserved('36',1,'2021-05-21','6');

  // Table Parameter for QR Code
  tc=getTableParameter();
  if(tc != false){ viewTablewithCode(tc); }

  if(localStorage.getItem('rCalendar') === null){
    viewCalendar(); return;
  } else {
    // Wenn Tag in localStorage kleiner als Tag heute
    var rc = localStorage.getItem('rCalendar').split(';');
    if(dateToSQL() > rc[0] && rc[0] != 'admin'){ viewCalendar(); return; }
  }

  // Lade Tische mit Ampelsystem
  (async() => { await loadTables(localStorage.getItem('rCalendar').split(';')[0]); })();
  // Überprüfe Login
	(async() => { await userCheck().then(function(result){ if(result == true){ $('.icon-user').css("color","green"); } }); })();


  $(document).on('click','.table', function(){
    if($('.form-table').length <= 0){
	    (async() => {
  			var id = jQuery(this).attr("id").split("-")[1];
  			await userCheck().then(function(result){
  				if(result == false){ viewTable(id, localStorage.getItem('rCalendar').split(';')[0]); } else { window.location.href = "admin.php?reservierungen&table="+id+"&date="; }
  			});
		  })();
    }
  });

	$('.table-close').click(function(){ $("#viewTable").css("display","none"); $('#viewTable').empty(); $('.container-reserve').css("background-color","white"); });
	$('.icon-user').click(function(){ if($('#form-login').length <= 0){ viewLogin(); } });
});

async function userCheck() {
	let result;
	try {
		var d = "";
		var c = getCookie('rSession');
		result = $.ajax({ url: "sync.php", method: "POST", data: { user: c }, success: function(result) { d = result; }
		});
		await new Promise((resolve, reject) => setTimeout(resolve, 100));
		if(d.toString() == CryptoJS.MD5(c).toString()){ return true; } return false;
	} catch(err){
		console.log("Error " + err);
	}
}



async function loadTables(date) {
  let result;
  try {
    var data = "";
    $.ajax({ url: "sync.php", method: "POST", data: {loadTables: date}, success: function(result) {
		$('#tischplan-svg').empty();
		data = JSON.parse(result);
		data.forEach((item, i) => {
		if(item['tableActive'] == 'open'){ item['tableActive'] = 'rgba(60, 179, 113,0.5)'; } else { item['tableActive'] = 'rgba(255, 0, 0,0.5)'; }
		var xml = jQuery.parseXML('<rect xmlns="http://www.w3.org/2000/svg" class="table" id="tisch-'+item["tableID"]+'" height="'+item["height"]+'" width="'+item["width"]+'" y="'+item["y"]+'" x="'+item["x"]+'" stroke="#000" fill="'+item["tableActive"]+'"/>');
		$('#tischplan-svg').append(xml.documentElement);
		});
	} });
    return true;
  } catch (e) {
    console.log("Error loadTables: " + e);
  }
}






function viewCalendar(){
  if($('.calendar-inputs').length >= 1){ return false; }
  $('#viewCalendar').append('<div class="calendar-inputs"></div>');
  if(localStorage.getItem('rCalendar') !== null){
    var rc = localStorage.getItem('rCalendar').split(';');
    $('.calendar-inputs').append('<input type="date" id="calendar-date" value="'+rc[0]+'">');
    $('.calendar-inputs').append('<select id="calendar-time"></select>');
    $.getJSON('http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php', function(data) {
      data.forEach((item, i) => {
          time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
          if(item['id'] == rc[1]){
            $('.calendar-inputs select').append('<option value="'+item["id"]+'" selected>'+time+' Uhr</option>');
          } else {
            $('.calendar-inputs select').append('<option value="'+item["id"]+'">'+time+' Uhr</option>');
          }
      });
    });
  } else {
    $('.calendar-inputs').append('<input type="date" id="calendar-date">');
    document.getElementById('calendar-date').valueAsDate = new Date();
    $('.calendar-inputs').append('<select id="calendar-time"></select>');
    // http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php
    // http://localhost/html/Reservierung/script/load.timeblock.php
    // http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php
    $.getJSON('http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php', function(data) {
      data.forEach((item, i) => {
        time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
        $('.calendar-inputs select').append('<option value="'+item["id"]+'">'+time+' Uhr</option>');
      });
    });
  }
  $('#viewCalendar').append('<div class="calendar-buttons"></div>');
  var link = "'https://www.hubraum-durlach.de/'";
  $('.calendar-buttons').append('<button onClick="window.location.href='+link+';">Reservierung verlassen</button><button id="calendar-confirm">Tisch auswählen</button>');
  $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
  $('#viewCalendar').css("display","block");
}

function confirmDay(date){




}

$(document).on('click','#calendar-confirm',function(){
  const date = $('#calendar-date').val(); const time = $('#calendar-time').val();
  var today = new Date().toISOString().slice(0, 10);
  if(time == null) { $('#calendar-time').css('background-color','#e63946'); return; }
  if(todayPlusSixWeeks() <= date) { $('#calendar-date').css('background-color','#e63946'); viewError('Reservierungen können maximal 6 Wochen im Voraus eingetragen werden!'); return; }
  if(date < today) { $('#calendar-date').css('background-color','#e63946'); viewError('Datum kann nicht in der Vergangenheit liegen!'); return; }

  var test = 0;
  $.ajax({ url: "sync.php", method: "POST", data: { confirmDay: date},
    success: function(result) {
      // Wenn result==1 Dann Tag nicht geöffnet
      if(result=="1"){
        viewError('HubRaum hat am ' + date + ' nicht geöffnet!');
      } else { // Restaurant hat am ausgewählten Tag geöffnet
        $.getJSON('http://localhost:8012/Reservierung%20-%20Github/script/load.feiertag.php',function(data){
          var check = true;
          data.forEach((item, i) => { if(item['date'] == date){ check=false; } });
          if(date.length == 10 && time.length >= 1 && check==true){
            // Setze Datum in localStorage und lade Seite neu
            localStorage.setItem('rCalendar',date+';'+time); location.reload();
          } else {
            $('#calendar-date').css('background-color','#e63946');
            viewError('HubRaum hat am ' + date + ' nicht geöffnet!');
          }
        });
      }
    }
  });
});

$(document).on('click','.fa-calendar-alt',function(){ viewCalendar(); });









function viewTable(id, date) {
	$('#viewTable').append('<i class="fa fa-times fa-2x" onClick="tableClose()"></i>');
	$('#viewTable').append('<div class="loader"></div>');
	$('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
	$('#viewTable').css("display","block");

	$.ajax({
    url: "sync.php",
    method: "POST",
    data: {loadTableID: id, loadTableDate: date},
    success: function(result) {
		//console.log(result);
      var d = JSON.parse(result);
      var tID = "'"+d['tableID']+"'";

      // Wenn Tisch aktiv ist und Reservierung nicht vorhanden, Tisch FREI andernfalls BELEGT
      if(d['tableActive'] == "open" && d['tableReserved'] == "open"){
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: green;">FREI</span></h1>');
      } else {
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: red;">BELEGT</span></h1>');
      }
	$('#viewTable .loader').remove();
      $('#viewTable').append("<form method='POST' class='form-table' onsubmit='event.preventDefault();'></form>");
      $('.form-table').append("<div id='container-information'><h3>Reservierungen</h3><div id='container-information-content'></div></div>");
      getReservierungen(d['tableID'], date);
      $('.form-table').append("<div class='form-table-middle'></div>");
      $('.form-table-middle').append("<div class='form-table-left'></div>");
      $('.form-table-middle').append("<div class='form-table-right'></div>");

      $('.form-table-left').append("<h2>Tisch reservieren</h2>");
      $('.form-table-left').append("<p>Damit ein Tisch reserviert werden kann werden folgende Daten benötigt</p>");
      $('.form-table-left').append("<ul><li>Personenanzahl <b>"+d['tableMin']+" - "+d['tableMax']+"</b></li><li>Zeit zwischen <b>17 Uhr</b> und <b>21:30 Uhr</b> wählen</li><li>Personendaten eintragen</li></ul>");


      $('.form-table-left').append("<div class='form-table-left-inputs'></div>");

      var options = ""; for (var i = parseInt(d['tableMin']); i <= parseInt(d['tableMax']); i++) { options = options + "<option value='"+i+"'>"+i+"</option>";}
      $('.form-table-left-inputs').append('<select id="amount">'+options+'</select><i class="fas fa-users"></i></div>');
      $('.form-table-left-inputs').append('<input type="date" id="timeDate" value="'+date+'">');
      var localBlock = localStorage.getItem('rCalendar').split(';')[1];
      $.getJSON('http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php', function(data) {
        $('.form-table-left-inputs').append('<select id="timeBlock"></select>');
        data.forEach((item, i) => {
          time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
          if(item['id'] == localBlock){
            $('#timeBlock').append('<option value="'+item["id"]+'" id="option'+item["id"]+'" selected>'+time+' Uhr</option>');
            $('#container-information-content').children().each((index, element) => {
              if(item["id"] == element['id'].slice(-1)){ $('#timeBlock').css('color','red'); $('#option'+item["id"]).css('color','red'); }
            });
          } else { $('#timeBlock').append('<option value="'+item["id"]+'" id="option'+item["id"]+'">'+time+' Uhr</option>'); }
        });
      });


      $('.form-table-right').append("<h2>Registrierung zwecks Corona</h2>");
      $('.form-table-right').append('<p>Damit ein Tisch bei uns reserviert werden kann, müssen wir den Anforderungen entsprechend die Daten einer Person bei uns abspeichern.<br>Bitte Denken Sie daran, dass bei mehreren Haushalten an einem Tisch, <b>pro Haushalt eine Kontakperson</b> registriert werden muss.<br>Die Daten werden <a href="#">Datenschutzkonform</a> abgespeichert</p');

      $('.form-table-right').append("<div class='form-table-right-inputs'><ul class='table-right-inputs-nav'></ul><div class='table-right-inputs-con'></div></div>");
      $('.table-right-inputs-nav').append('<li class="right-input-nav" id="1" style="border-bottom:1px solid #c05f5f; color: #c05f5f">Haushalt 1</li><li class="right-input-nav" id="2">Haushalt 2</li><li class="right-input-nav" id="3">Haushalt 3</li><li class="right-input-nav" id="4">Haushalt 4</li><li class="right-input-nav" id="5">Haushalt 5</li>');


      for (var i = 0; i < 5; i++) {
        $('.table-right-inputs-con').append('<div class="right-inputs-hh'+(i+1)+' hh">');
        $('.right-inputs-hh'+(i+1)).append('<h3>Haushalt 1</h3>');
        var t = "'clientVorname'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientVorname" onkeyup="verifyInput('+t+')" placeholder="Vorname">');
        t = "'clientName'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientName" onkeyup="verifyInput('+t+')" placeholder="Name">');
        t = "'clientMail'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientMail" onkeyup="verifyInput('+t+')" placeholder="E-Mail">');
        t = "'clientTNR'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientTNR" onkeyup="verifyInput('+t+')" placeholder="Telefonnummer">');
        $('.right-inputs-hh'+(i+1)).css("display","none");
      }
      $('.right-inputs-hh1').css("display","block");

      $('.form-table').append('<div class="form-table-submit"></div>');
      $('.form-table-submit').append('<input type="button" onClick="tableClose()" id="close-table" value="Schließen">');
      if(d['tableActive'] == "open" && d['tableReserved'] == "open"){
        $('.form-table-submit').append('<input type="submit" id="submit-table" onClick="sendReserve('+tID+')" value="Reservieren">');
      } else {
        $('.form-table-submit').append('<input type="submit" id="submit-table" value="Reservieren">');
      }


    }
  });
}


$(document).on("click",".right-input-nav", function(event){
  $('.right-input-nav').css("border-bottom","1px solid darkgray");
  $('.right-input-nav').css("color","black");
  $(this).css("border-bottom","1px solid #c05f5f");
  $(this).css("color","#c05f5f");
  var id = event.target.id;
  $('.hh').css("display","none");
  $('.right-inputs-hh'+id+' h3').text("Haushalt "+id);
  $('.right-inputs-hh'+id).css("display","block");
});

$(document).on('change','#timeBlock',function(){
  var block = []; var count = 0;
  $('#container-information-content').children().each((index, element) => { block[count] = element['id'].slice(-1); count++; });
  if(block.includes($(this).val())){ $(this).css('color','red'); } else {
    $('#option'+$(this).val()).css('color','black'); $(this).css('color','black');
  }
});

$(document).on('change','#timeDate',function(){
  var tisch = $('#viewTable h1').text().split(' ')[1];
  var date = $(this).val();
  tableClose();
  viewTable(tisch, date);
});

function checkTimeFrom(t) {
  $('#timeDuration').css("color","black");
  $('#timeLabel').css("color","black");


  // Reservierungen anzeigen lassen
  //setTimeout(function(){ getReservierungen(t, $('#timeDate').val()); }, 100);
  var duration = $('#timeDuration').val(); // 1=2:30 2=Ganztags
  var tf = $("#timeFrom").val();
  var da = $("#timeDate").val();
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { getTime: t, sndDate: da },
    success: function(result) {
      const startTime = "17:00"; const endTime = "21:30"; // rID | rDate | rS | rE | rD | cc = clientConfirm | cn = clientname | rState
      var r = new Date().toString().split(" ");
      var timeDate = new Date(r[0] + " " + r[1] + " " + r[2] + " " + r[3] + " " + tf + ":00"); // ausgewählte Zeit in Date
      if(result != false){ // Wenn für Tag Reservierungen existieren
        var d = JSON.parse(result);
        var ueberschneidung = new Array();
        for (var i = 0; i < d.length; i++) {
          // Wenn Reservierung Datenbank GANZTAGS und INPUT GANZTAGS
          if(d[i]['rD'] == "gz" && duration == 2){ $('#timeDuration').css("color","red"); return false; }
          if(d[i]['rState'] == 3 || d[i]['rState'] == 4){ continue; }

          dateStart = new Date(d[i]['rDate']+" "+d[i]['rS']); // Startzeit in Datenbank
          dateEnd = new Date(d[i]['rDate']+" "+d[i]['rE']);   // Endzeit in Datenbank

          // Wenn Reservierung Datebank 2:30 und INPUT GANZTAGS
          if(d[i]['rD'] == "2:30" && duration == 2){
            // Wenn Endzeit < INPUT Reservierungszeit
            if(dateEnd.getHours()+":"+dateEnd.getMinutes()<timeDate.getHours()+":"+timeDate.getMinutes()){
              continue;
            }
          }

          tfH = timeDate.getHours()+2;      // ausgewählte Zeit +2
          tfM = timeDate.getMinutes()+30;   // ausgewählte Zeit +30
          if(tfM >= 60){                    // Wenn ausgewählte Zeit + 30 Minuten größer als 60
            tfH = timeDate.getHours()+3;
            tfM = timeDate.getMinutes()+30-60; if(tfM.toString().length == 1){ tfM = "0"+tfM; }
          }

          // Wenn Reservierung Datenbank GANZTAGS und INPUT 2:30
          if(d[i]['rD'] == "gz" && duration == 1){
            // ausgewählte Uhrzeit + 2:30 kleiner gleich Startzeit in Datenbank
            if(tfH+":"+tfM >= dateStart.getHours()+":"+dateStart.getMinutes()){
              ueberschneidung[i] = true;
              continue;
            }
          }

          // Ausgewählte Zeit >= Startzeit in Datenbank
          if(timeDate.getHours()+":"+timeDate.getMinutes()>=dateStart.getHours()+":"+dateStart.getMinutes() && timeDate.getHours()+":"+timeDate.getMinutes()<=dateEnd.getHours()+":"+dateEnd.getMinutes()){
              ueberschneidung[i] = true;
              continue;
          }
        }

        //console.log("Überschneidung: " + ueberschneidung);
        switch (ueberschneidung.includes(true)) {
          case true:
            $('#timeFrom').css("color","red");
            return;
            break;
          case false:
            if(tf >= startTime && tf <= endTime){ // Wenn Zeit größer gleich startTime und kleiner gleich endTime
              $('#timeFrom').css("color","green");
            } else {
              $('#timeFrom').css("color","red");
            }
            break;
        }

      } else { // Keine Reservierungen gefunden
        if(tf >= startTime && tf <= endTime){ // Wenn Zeit größer gleich startTime und kleiner gleich endTime
          $('#timeFrom').css("color","green");
        } else {
          $('#timeFrom').css("color","red");
        }
      }

    }
  });
}

function getReservierungen(tableID, date) {
  $('#container-information-content').empty();
  // 0 = Reserviert | 1 = Eingetroffen | 2 = Frühzeitig beendet | 3 = Abgesagt | 4 = NoShow | 5 = Abweichende Anzahl + Eingetroffen
  var colors = ['#4ea8de','#2b9348','#ee6c4d','#ba181b','#006d77', '#7400b8'];
  $.ajax({
    url: "sync.php",
    method: "GET",
    data: { getReservierungen: tableID+";"+date},
    success: function(result) {
      console.log("Reservierungen: " + result);
      if(result != false){
        var d = JSON.parse(result);
        d.forEach((item, i) => {
          if(item['rState'] != 3 && item['rState'] != 4){
            var colorNum = item['rState'];
            if(item['rT'].length > 0){
              var time = item['rT'].split(" - ");
              $('#container-information-content').append("<div class='information-box' id='rsBlock"+item['rB']+"' style='background-color: "+colors[colorNum]+"'>Reserviert<br>"+time[0].slice(0,5)+" Uhr bis "+time[1].slice(0,5)+" Uhr</div>");
            }
          }
        });
        $('#timeBlock').css('color','black');
      }
    }
  });
}



function viewTablewithCode(tc) {
    if(r(tc) == true){
      $.ajax({
        url: "sync.php",
        method: "POST",
        data: { qrCode: tc},
        success: function(result) { if(result != 0){ viewTable(result); } }
      });
    }
}

function viewLogin() {
	$('#viewLogin').empty();
	$('#viewLogin').css("display","block");
	$('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
	$('#viewLogin').append('<i class="fa fa-times fa-2x" onClick="loginClose()"></i><i class="fa fa-user-circle fa-5x login-icon"></i>');

  if(getCookie("rSession") != ""){
    try {
	   $('#viewLogin').css("height","300px");
	  (async() => {
		const uc = await userCheck().then(function(result){
			if(result == true){
				$('#viewLogin').append('<h3>Bereits angemeldet</h3>');
				var today = new Date();
				var dd = String(today.getDate()).padStart(2, '0'); var mm = String(today.getMonth() + 1).padStart(2, '0'); var yyyy = today.getFullYear();
				today = "'" + yyyy + "-" + mm + "-" + dd + "'";

				$('#viewLogin').append('<button id="login-NoShow">Übersicht</button>');
				$('#viewLogin').append('<button onClick="submitLogoff()">Abmelden</button>');
			} else {
				submitLogoff();
			}
		});
	  })();
    } catch(e){ console.log(e); }
  } else {
	$('#viewLogin').append('<form id="form-login" onsubmit="event.preventDefault();"></form>');
	$('#form-login').append('<input type="text" id="hubraumName" placeholder="Name..."><input type="password" id="hubraumSecure" placeholder="Passwort..."><input type="submit" onClick="submitLogin()" value="Login">');
  }
}

function viewCoronaInfo() {
  $('#viewCoronaInfo').css("display","block");
  $('#viewCoronaInfo').append("<h3>Informationen für Corona</h3><p>Bitte Denken Sie daran, dass bei mehreren Haushalten an einem Tisch, pro Haushalt eine Kontakperson registriert werden muss.<br><br>Vielen Dank für ihr Verständnis!</p>");
  setTimeout(function(){
    $('#viewCoronaInfo').css("display","none");
    $('#viewCoronaInfo').empty();
  }, 5000);
}





function getTableACP(id) {
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { getTableActive: id},
    success: function(result) {
      var src = $('#acp-table img').attr("src").split("/");
      var tid = "'"+id+"'";
      if(result == "open"){
        $('#acp-table img').attr("src",src[0] + "/open/" + src[2]);
        $('#acp-table').append('<label class="switch"><input type="checkbox" onChange="updateTable('+tid+')" checked><span class="slider round"></span></label>');
        return;
      }
      $('#acp-table img').attr("src",src[0] + "/closed/" + src[2]);
      $('#acp-table').append('<label class="switch"><input type="checkbox" onChange="updateTable('+tid+')"><span class="slider round"></span></label>');
    }
  });
}



function getReservierungenACP(t){
  $('#container-information-content').empty();
  // 0 = Reserviert | 1 = Eingetroffen | 2 = Frühzeitig beendet | 3 = Abgesagt | 4 = NoShow
  var colors = ['#4ea8de','#2b9348','#ee6c4d','#ba181b','#006d77'];
  var dt = $('#acpInputDate').val();
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { getTime: t, sndDate: dt},
    success: function(result) {
      //console.log(result);
      if(result != false){
        var d = JSON.parse(result);
        d.forEach((item, i) => {
          var colorNum = item['rState'];
          var cc = '"' + item['cc'] + '"';
          if(item['rD'] == "gz"){
            $('#container-information-content').append("<div class='information-box-acp' id='"+item['rID']+"' style='background-color: "+colors[colorNum]+"'>Reserviert<br>Bis 22 Uhr</div>");
            return;
          } else {
            dateStart = new Date(item['rDate']+" "+item['rS']);
            var dateStartMinutes = dateStart.toTimeString().slice(3, 5);
            dateEnd = new Date(item['rDate']+" "+item['rE']);
            var dateEndMinutes = dateEnd.toTimeString().slice(3, 5);
            $('#container-information-content').append("<div class='information-box-acp' id='"+item['rID']+"' style='background-color: "+colors[colorNum]+"'>Reserviert<br>"+dateStart.getHours()+":"+dateStartMinutes+" Uhr bis "+dateEnd.getHours()+":"+dateEndMinutes+" Uhr</div>");
          }
        });
      } else {
          $('#container-information-content').empty();
          $('#container-information-content').append("<div class='information-box-acp' style='background-color: "+colors[3]+"; margin-left:auto;margin-right:auto;'>Keine Reservierung gefunden</div>");
      }
    }
  });
}



function loginClose() {
  $('#viewLogin').empty();
  $("#viewLogin").css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function tableClose() {
  $('#viewTable').empty();
  $('#viewCoronaInfo').empty();
  $("#viewTable").css("display","none");
  $('#viewCoronaInfo').css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function reservedClose() {
  $('#viewReserved').empty();
  $("#viewReserved").css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function verifyInput(id) {
  var data = $('.'+id).val();
  if(data.length <= 35 && data.length >= 2) {
    $('.'+id).css("border","2px solid green");
  } else if(data.length <= 5){
    $('.'+id).css("border","1px solid black");
  } else {
    $('.'+id).css("border","2px solid red");
  }
}




function sendReserve(tID) {
    var inputs = new Array();
    var haushalt = new Array();

    const amount = $('#amount').val(); if(r(amount)){ inputs[0] = amount; } else { return false; }
    const date = $('#timeDate').val(); if(r(date)==true && date >= getTodaySQLFormat() && date <= todayPlusSixWeeks()){ inputs[1] = date; } else { return false; }
    const timeBlock = $('#timeBlock').val(); if(r(timeBlock)){ inputs[2] = timeBlock; } else { return false; }
    inputs[3] = tID;
    for (var i = 1; i < 6; i++) {
      const cv = $('.right-inputs-hh'+i+' .clientVorname').val();
      const cn = $('.right-inputs-hh'+i+' .clientName').val();
      const cm = $('.right-inputs-hh'+i+' .clientMail').val();
      const ct = $('.right-inputs-hh'+i+' .clientTNR').val();
      if(cv.length >= 1){
        if(r(cv) == true && r(cn) == true && r(cm) == true && r(ct) == true){ haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ct; }
      }
    }
    inputs[4] = haushalt;
    if(haushalt.length > 0){
		const mailTO = $('.right-inputs-hh0 .clientMail').val();
      $.ajax({
        url: "sync.php", method: "POST", data: { createReserve: inputs },
        success: function(result) {
          console.log("Result: " + result);
          switch (result) {
            case "1":
              tableClose();
              console.log('Reservierung bestätigt');
              viewReserved(tID,timeBlock,date,amount); //table, time, date, amount
              break;
            case "2":
              viewError('Die E-Mail oder Telefonnummer wurde auf die Blacklist gesetzt. Eine Reservierung ist nicht möglich!');
			case "3":
				console.log('Reservierung bestätigt');
				viewError('Reservierung wurde erfolgreich durchgeführt. Es konnte keine E-Mail an '+mailTO+' verschickt werden!');
				viewReserved(tID,timeBlock,date,amount);
          }
        }
      });
    }
}

function viewReserved(table, blockID, date, amount){
  $.getJSON('http://localhost:8012/Reservierung%20-%20Github/script/load.timeblock.php', function(data) {
    $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
    $('.container-reserve').append('<div id="viewReserved"></div>');
    $('#viewReserved').append('<h2>Tisch '+table+' am '+date+' reserviert!</h2>');
    data.forEach((item, i) => {
      if(item['id'] == blockID){
        $('#viewReserved').append('<ul><li>Anzahl der Personen '+amount+'</li><li>Reserviert für '+item["start"]+' - '+item["end"]+' Uhr</li></ul>');
      }
    });
    $('#viewReserved').append('<p><p>Wir wünschen Ihnen einen angenehmen Aufenthalt!<br><br>Falls die Reservierung nicht mehr stimmen sollte, bitten wir um eine telefonische Absage oder korrektur</p></p>');
    $('#viewReserved').append('<button onClick="reservedClose()" id="viewReservedButton">Verstanden</button>');
  });
}

function submitLogin(){
  var n = $('#hubraumName').val(); var p = $('#hubraumSecure').val();
  if(n.length >= 5 && p.length >= 7){
    $.ajax({
      url: "sync.php", method: "POST", data: { hubName: CryptoJS.MD5(n).toString(), hubSecure: CryptoJS.MD5(p).toString() },
      success: function(result) { location.reload(); loginClose(); }
    });
  }
}

function submitLogoff() { setCookie("rSession","",-1); location.reload(); }

function getToday() {
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0'); var mm = String(today.getMonth() + 1).padStart(2, '0'); var yyyy = today.getFullYear();
  return dd + "." + mm + "." + yyyy;
}

function getTodaySQLFormat() {
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0'); var mm = String(today.getMonth() + 1).padStart(2, '0'); var yyyy = today.getFullYear();
  return yyyy + "-" + mm + "-" + dd;
}

// regex t=text | r=regex
function r(t){
  const regex = "/[+_\:;\/*{}´^<>=&%$§#']+/gm";
  var m = regex.match(t, regex);
  if(m !== null){
    m.forEach((match, groupIndex) => { return false; });
  }
  return true;
}

function acpSubmit(tID) {
  // Wenn keine ID in INPUT HIDDEN ist
  if(!$('#acpReserveID').val() && $('#acpSubmit').val() == "Bearbeiten"){ alert("Sie müssen eine Reservierung zum bearbeiten auswählen!"); return; }
  (async() => {
    const uc = await userCheck().then(function(result){
    if(result == true){
      var inputs = new Array();
      var haushalt = new Array();
      const type = $('#acpSubmit').val();
      inputs[0] = $('#acpInputAmount').val();
      inputs[1] = $('#acpInputDate').val();
      inputs[2] = $('#acpInputTime').val();
      inputs[3] = $('#acpInputDuration').val();
      inputs[4] = $('#acpReserveID').val();
      inputs[5] = $('#viewAdminTable h1').text().split(" ")[2];
      for (var i = 1; i < 6; i++) {
        const ci = $('.right-inputs-hh'+i+' .clientID').val();
        const cv = $('.right-inputs-hh'+i+' .clientVorname').val();
        const cn = $('.right-inputs-hh'+i+' .clientName').val();
        const cm = $('.right-inputs-hh'+i+' .clientMail').val();
        const ct = $('.right-inputs-hh'+i+' .clientTNR').val();
        if(cv.length >= 3){
          if(r(cv) == true && r(cn) == true && r(cm) == true && r(ct) == true){
            haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ct + ";" + ci;
          }
        }
      }
      inputs[6] = haushalt;
      if(haushalt.length > 0){
        $.ajax({
          url: "sync.php", method: "POST", data: { acpReserve: inputs, acpSubmit: type },
          success: function(result) {
            console.log(result);
            if(result == "1"){
              location.reload();
            } else {
              alert("Ein Fehler ist aufgetreten!\n"+result);
            }
          }
        });
      }
    } else {
      submitLogoff();
    }
  });
  })();
}

function viewError(text){
  if($('#viewError p').length <= 0){
    $('#viewError').append("<p>"+text+"</p><button>Verstanden</button>");
    $('#viewError').css('display','block');
  }
}

$(document).on('click','#viewError button',function(){
  $('#viewError').empty();
  $('#viewError').css('display','none');
});

function removeReserve(cc){
     (async() => {
       const uc = await userCheck().then(function(result){
        if(result == true){
          if(confirm("Reservierung " + cc + " wirklich entfernen?")) {
            $.ajax({
              url: "sync.php", method: "POST", data: { remove: cc },
              success: function(result) {
                if(result == 1){
                  location.reload();
                } else {
                  alert("Ein Fehler ist aufgetreten");
                }
              }
            });
          }
        } else {
          alert("Ein Fehler ist aufgetreten");
        }
    });
    })();

}

function decodeDuration(dd){
  if(dd=="1"){
    return "2:30h";
  }else if(dd=="2"){
    return "bis 22 Uhr";
  }
  return false;
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getTableParameter() {
  var url = new URL(window.location.href);
  var c = url.searchParams.get("table");
  if(c != null){
    return c.split("&")[0];
  }
  return false;
}

function dateToSQL() { return new Date().toISOString().slice(0, 10); }
function todayPlusSixWeeks() {
  var today = new Date();
  var nextweek = new Date(today.getFullYear(), today.getMonth(), today.getDate()+(7*6));
  return nextweek.getFullYear() + "-" + ("0" + (nextweek.getMonth() + 1)).slice(-2) + "-" + ("0" + nextweek.getDate()).slice(-2);
}
