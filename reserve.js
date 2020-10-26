$(document).ready(function(){
  tc=getTableParameter()
  if(tc != false){ viewTablewithCode(tc); }

  $.ajax({
    url: "sync.php",
    method: "POST",
    data: {loadTables: "1"},
    success: function(result) {
      var d = JSON.parse(result);
      for (var i = 0; i < d.length; i++) {
        var h3 = "#"+d[i]['tableID']+"-h3"; var img = "#"+d[i]['tableID']+"-img";
        $(h3).text(d[i]['tableID']); $(img).attr("src","img/"+d[i]['tableActive']+"/t-"+d[i]['tableType']+"-transparent.png");
      }
    }
  });

  (async() => {
  	const uc = await userCheck().then(function(result){
  		if(result == true){ $('.icon-user').css("color","green"); }
  	});
  })();


  $('.table').click(function(event){
    if($('.form-table').length <= 0){
	    (async() => {
  			var id = jQuery(this).attr("id");
  			const uc = await userCheck().then(function(result){
  				if(result == false){ viewTable(id); } else { var src = $('#'+id+ " img").attr("src"); viewAdminTable(id, src); }
  			});
		  })();
    }
  });

  $('.table-close').click(function(){
    $("#viewTable").css("display","none");
    $('#viewTable').empty();
    $('.container-reserve').css("background-color","white");
  });

  $('.icon-user').click(function(){
    if($('#form-login').length <= 0){
      viewLogin();
    }
  });
});

async function userCheck() {
	let result;
	try {
		var d = "";
		var c = getCookie('rSession');
		result = $.ajax({ url: "sync.php", method: "POST", data: { user: c }, success: function(result) {
			d = result;
			}
		});

		await new Promise((resolve, reject) => setTimeout(resolve, 100));
		if(d.toString() == CryptoJS.MD5(c).toString()){
			return true;
		}
		return false;
	} catch(err){
		console.log("Error " + err);
	}
}

function viewTable(id) {
   var a = true;
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: {loadTableID: id},
    success: function(result) {
      console.log(result);
      var d = JSON.parse(result);

      var tID = "'"+d['tableID']+"'";

      $('#viewTable').append('<i class="fa fa-times fa-2x" onClick="tableClose()"></i>');

      if(d['tableActive'] == "open" && d['tableReserved'] == "open"){
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: green;">FREI</span></h1>');
      } else {
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: red;">BELEGT</span></h1>');
      }

      $('#viewTable').append("<form method='POST' class='form-table' onsubmit='event.preventDefault();'></form>");
      $('.form-table').append("<div id='container-information'><h3>Reservierungen</h3><div id='container-information-content'></div></div>");
      $('.form-table').append("<div class='form-table-left'></div>");
      $('.form-table').append("<div class='form-table-right'></div>");

      $('.form-table-left').append("<h2>Tisch reservieren</h2>");
      $('.form-table-left').append("<p>Damit ein Tisch reserviert werden kann werden folgende Daten benötigt</p>");
      $('.form-table-left').append("<ul><li>Personenanzahl <b>"+d['tableMin']+"/"+d['tableMax']+"</b></li><li>Zeit zwischen <b>17 Uhr</b> und <b>21:30 Uhr</b> wählen</li><li>Personendaten eintragen</li></ul>");


      $('.form-table-left').append("<div class='form-table-left-inputs'></div>");

      var options = ""; for (var i = parseInt(d['tableMin']); i <= parseInt(d['tableMax']); i++) { options = options + "<option value='"+i+"'>"+i+"</option>";}

      $('.form-table-left-inputs').append('<div class="left-inputs-icon"><select id="amount">'+options+'</select><i class="fas fa-user fa-1x"></div>');
      $('.form-table-left-inputs').append('<input type="date" id="timeDate" onChange="checkTimeFrom('+tID+')">');
      $('.form-table-left-inputs').append('<input type="time" id="timeFrom" value="17:00" onChange="checkTimeFrom('+tID+')" min="17:00" max="21:00" step="900">');
      $('.form-table-left-inputs').append('<select id="timeDuration" onChange="checkTimeFrom('+tID+')"><option value="1">2:30h</option><option value="2">Bis 22 Uhr</option></select>');
      //document.querySelector("#timeDate").valueAsDate = new Date();

      //setTimeout(function(){ getReservierungen(d['tableID'], $('#timeDate').val()); }, 100);

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
        t = "'clientAdresse'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientAdresse" onkeyup="verifyInput('+t+')" placeholder="Adresse">');
        t = "'clientTNR'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientTNR" onkeyup="verifyInput('+t+')" placeholder="TNR">');
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

      $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
      $('#viewTable').css("display","block");
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

function checkTimeFrom(t) {
  $('#timeDuration').css("color","black");
  $('#timeLabel').css("color","black");
  // Reservierungen anzeigen lassen
  setTimeout(function(){ getReservierungen(t, $('#timeDate').val()); }, 100);
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
          if(d[i]['rState'] == 3 && d[i]['rState'] == 4){ continue; }
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

function getReservierungen(t,dt) {
  $('#container-information-content').empty();
  // 0 = Reserviert | 1 = Eingetroffen | 2 = Frühzeitig beendet | 3 = Abgesagt | 4 = NoShow
  var colors = ['#4ea8de','#2b9348','#ee6c4d','#ba181b','#006d77'];
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { getTime: t, sndDate: dt},
    success: function(result) {
      console.log(result);
      if(result != false){
        var d = JSON.parse(result);
        d.forEach((item, i) => {
          if(item['rState'] != 3 && item['rState'] != 4){
            var colorNum = item['rState'];
            if(item['rD'] == "gz"){
              $('#container-information-content').append("<div class='information-box' style='background-color: "+colors[colorNum]+"'>Reserviert<br>Bis 22 Uhr</div>");
              return;
            } else {
              dateStart = new Date(item['rDate']+" "+item['rS']);
              var dateStartMinutes = dateStart.toTimeString().slice(3, 5);
              dateEnd = new Date(item['rDate']+" "+item['rE']);
              var dateEndMinutes = dateEnd.toTimeString().slice(3, 5);
              $('#container-information-content').append("<div class='information-box' style='background-color: "+colors[colorNum]+"'>Reserviert<br>"+dateStart.getHours()+":"+dateStartMinutes+" Uhr bis "+dateEnd.getHours()+":"+dateEndMinutes+" Uhr</div>");
            }
          }
        });
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
        success: function(result) {
          if(result != 0){
            viewTable(result);
          }
        }
      });
    }
}

async function viewLogin() {
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

        $('#viewLogin').append('<button onClick="viewReserveOverviewDay('+today+')">Übersicht</button>');
        $('#viewLogin').append('<button onClick="">No-Show Liste</button>');
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

function viewAdminTable(id, src){
  (async() => {
    const uc = await userCheck().then(function(result){
		if(result == true){

      // Wenn schon geöffnet
      if($('#acpSettings').length > 0){ return; }

			$('#viewAdminTable').append('<i class="fa fa-times fa-2x" onClick="tableClose()"></i>');
			$('#viewAdminTable').append("<h1>Bearbeitung Tisch "+id+"</h1>");
			$('#viewAdminTable').append("<div class='acp-top'></div>");
      $('#viewAdminTable').append("<div class='acp-bottom'></div>");

      getTableACP(id);

      $('.acp-top').append("<div id='container-information'><h3>Reservierungen</h3><div id='container-information-content'></div></div>");
      $('.acp-top').append("<div class='acp-settings-buttons'></div>");
      // Aktivieren | Deaktivieren | Löschen | Bearbeiten // Tisch verlängern |
      $('.acp-settings-buttons').append("<button id='bt-eingetroffen'><i class='fas fa-check'></i> Eingetroffen</button><button id='bt-freigeben'><i class='fas fa-unlock'></i> Wieder freigeben</button><button id='bt-abgesagt'><i class='fas fa-user-slash'></i> Abgesagt</button><button id='bt-no-show'><i class='fas fa-user-times'></i> No-Show</button>");

      $('.acp-bottom').append("<div class='acp-bottom-content'></div>");
      $('.acp-bottom').append("<ul class='acp-bottom-nav'><li class='acp-bottom-nav-current'>Reservierung</li><li>Haushalt</li></ul>");

      var str = "'" + id + "'";
			$('.acp-bottom-content').append('<div id="acp-bottom-reservierung"></div>');

      $('#acp-bottom-reservierung').append('<div id="acp-table"><img src="'+src+'"></div>');
      $('#acp-bottom-reservierung').append('<input type="hidden" id="acpReserveID">');
			$('#acp-bottom-reservierung').append('<label id="acpTimeLabel">Reservieren ab <input type="time" value="17:00" class="setting" id="acpInputTime" min="17:00" max="21:00"></label>');
      $('#acp-bottom-reservierung').append('<label id="acpDateLabel">Datum auswählen <input type="date" id="acpInputDate" onChange="getReservierungenACP('+str+')"></label>');
      $('#acp-bottom-reservierung').append('<label id="acpDurationLabel">Dauer auswählen <select class="setting" id="acpInputDuration"><option value="1">2:30h</option><option value="2">Ganztags</option></select></label>');
			$('#acp-bottom-reservierung').append('<label id="acpAmountLabel">Anzahl <input type="text" class="setting" id="acpInputAmount" placeholder="z.B. 10"></label>');
      document.querySelector("#acpInputDate").valueAsDate = new Date();

      getReservierungenACP(id);

      $('.acp-bottom-content').append('<div id="acp-bottom-haushalt"></div>');

      $('#acp-bottom-haushalt').append("<div class='form-table-right-inputs'><ul class='table-right-inputs-nav'></ul><div class='table-right-inputs-con'></div></div>");
      $('.table-right-inputs-nav').append('<li class="right-input-nav" id="1" style="border-bottom:1px solid #c05f5f; color: #c05f5f">Haushalt 1</li><li class="right-input-nav" id="2">Haushalt 2</li><li class="right-input-nav" id="3">Haushalt 3</li><li class="right-input-nav" id="4">Haushalt 4</li><li class="right-input-nav" id="5">Haushalt 5</li>');

      for (var i = 0; i < 5; i++) {
        $('#acp-bottom-haushalt').append('<div class="right-inputs-hh'+(i+1)+' hh">');
        $('.right-inputs-hh'+(i+1)).append('<h3>Haushalt 1</h3>');
        $('.right-inputs-hh'+(i+1)).append('<input type="hidden" class="clientID">');
        var t = "'clientVorname'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientVorname" onkeyup="verifyInput('+t+')" placeholder="Vorname">');
        t = "'clientName'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientName" onkeyup="verifyInput('+t+')" placeholder="Name">');
        t = "'clientMail'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientMail" onkeyup="verifyInput('+t+')" placeholder="E-Mail">');
        t = "'clientAdresse'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientAdresse" onkeyup="verifyInput('+t+')" placeholder="Adresse">');
        t = "'clientTNR'";
        $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientTNR" onkeyup="verifyInput('+t+')" placeholder="TNR">');
        $('.right-inputs-hh'+(i+1)).css("display","none");
      }
      $('.right-inputs-hh1').css("display","block");


			var tID = "'"+id+"'";
			$('#viewAdminTable').append('<div class="form-table-submit"></div>');
			$('.form-table-submit').append('<input type="button" onClick="tableClose()" id="acpClose" value="Schließen"><input type="submit" id="acpSubmit" onClick="acpSubmit('+tID+')" value="Erstellen">');

      $('#container-information').css("max-width","60%");
      $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
			$('#viewAdminTable').css("display", "block");
		} else {
			submitLogoff();
		}
	});
  })();
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

function updateTable(id) {
  var check = $('.switch input').prop("checked");
  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { setTableActive: id, value: check},
    success: function(result) {
      var src = $('#acp-table img').attr("src").split("/");
      if(result){
        if(check == false){
          $('#acp-table img').attr("src",src[0] + "/closed/" + src[2]);
        } else {
          $('#acp-table img').attr("src",src[0] + "/open/" + src[2]);
        }
        return;
      }
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

function viewReserved(table, time, date, duration, amount){
  $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
  $('.container-reserve').append('<div id="viewReserved"></div>');
  $('#viewReserved').append('<h2>Tisch '+table+' am '+date+' reserviert!</h2>');
  $('#viewReserved').append('<ul><li>Anzahl der Personen '+amount+'</li><li>Reserviert für '+time+' Uhr</li><li>Dauer: '+decodeDuration(duration)+'</li></ul>');
  $('#viewReserved').append('<p><p>Wir wünschen Ihnen einen angenehmen Aufenthalt!<br><br>Falls die Reservierung nicht mehr stimmen sollte, bitten wir um eine telefonische Absage oder korrektur</p></p>');
  $('#viewReserved').append('<button onClick="reservedClose()" id="viewReservedButton">Verstanden</button>');
}

function viewReserveOverviewDay(date) {
  $('#viewOverview').empty();
  loginClose();
  (async() => {
    const uc = await userCheck().then(function(result){
		if(result == true){
      $('#viewOverview').append('<i class="fa fa-times fa-2x" onClick="overviewClose()"></i>');
      $('#viewOverview').append("<div class='ov-Navigation'></div><div class='ov-Data'></div>");
      var temp = "'"+date+"'";
      $('.ov-Navigation').append('<ul style="list-style: none;"><li style="color: darkgray;">Tagesbericht<input type="date" id="oInputDate" value="'+date+'"></li><li onClick="viewReserveOverviewWeek('+temp+')">Wochenbericht</li></ul>');
      $('.ov-Data').append('<table id="data-table"><tr><th>Tisch ID</th><th>Name</th><th>Datum</th><th>Uhrzeit</th><th>Dauer</th><th>Anzahl</th><th>Telefon</th></tr></table>');
      $('#oInputDate').change(function(){ viewReserveOverviewDay(this.value); });
      $.ajax({
        url: "sync.php",
        method: "POST",
        data: { getOverview: 'day', oDate: $('#oInputDate').val() },
        success: function(res) {
          if(res){
            var data = JSON.parse(res);
            data.forEach((d, i) => {
              let id = "'"+d['tID']+"'";
              let dt = d['rTime'].split(" ");
              $('#data-table').append('<tr><td><i class="fa fa-table fa-2x" onClick="viewAdminTable('+id+')"></i> Tisch '+d["tID"]+'</td><td>'+d["cName"]+'</td><td>'+dt[0]+'</td><td>'+dt[1]+' Uhr</td><td>'+d["rDuration"]+'</td><td>'+d["rA"]+'</td><td>'+d["cTNR"]+'</td></tr>');
            });
          }
        }
      });
      $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
      $('#viewOverview').css("display","block");
		} else {
			submitLogoff();
		}
	});
  })();
}

function viewReserveOverviewWeek(dt) {
  $('#viewOverview').empty();
  loginClose();
  (async() => {
    const uc = await userCheck().then(function(result){
		if(result == true){
      $('#viewOverview').append('<i class="fa fa-times fa-2x" onClick="overviewClose()"></i>');
      $('#viewOverview').append("<div class='ov-Navigation'></div><div class='ov-Data'></div>");
      var temp = "'"+dt+"'";
      $('.ov-Navigation').append('<ul style="list-style: none;"><li onClick="viewReserveOverviewDay('+temp+')">Tagesbericht</li><li style="color: darkgray;">Wochenbericht<input type="date" id="oInputDate" value="'+dt+'"></li></ul>');
      $('.ov-Data').append('<table id="data-table"><tr><th>Tisch ID</th><th>Name</th><th>Datum</th><th>Uhrzeit</th><th>Dauer</th><th>Anzahl</th><th>Telefon</th></tr></table>');
      $('#oInputDate').change(function(){ viewReserveOverviewWeek(this.value); });

      var date = new Date(dt);
      date.setDate(date.getDate() + 8);

      $.ajax({
        url: "sync.php",
        method: "POST",
        data: { getOverview: 'week', oDate: dt, o7Date: dateToSQL(date) },
        success: function(res) {
          if(res){
            var data = JSON.parse(res);
            data.forEach((d, i) => {
              let id = "'"+d['tID']+"'";
              let dt = d['rTime'].split(" ");
              $('#data-table').append('<tr><td><i class="fa fa-table fa-2x" onClick="viewAdminTable('+id+')"></i> Tisch '+d["tID"]+'</td><td>'+d["cName"]+'</td><td>'+dt[0]+'</td><td>'+dt[1]+' Uhr</td><td>'+d["rDuration"]+'</td><td>'+d["rA"]+'</td><td>'+d["cTNR"]+'</td></tr>');
            });
          }
        }
      });
      $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
      $('#viewOverview').css("display","block");
		} else {
			submitLogoff();
		}
	});
  })();
}





function loginClose() {
  $('#viewLogin').empty();
  $("#viewLogin").css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function overviewClose() {
  $('#viewOverview').empty();
  $("#viewOverview").css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function tableClose() {
  $('#viewTable').empty();
  $('#viewAdminTable').empty();
  $('#viewCoronaInfo').empty();
  $("#viewTable").css("display","none");
  $("#viewAdminTable").css("display","none");
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
    const amount = $('#amount').val(); if(r(amount)){ inputs[0] = amount; }
    const date = $('#timeDate').val(); if(r(date)){ inputs[1] = date; }
    const time = $('#timeFrom').val(); if(r(time)){ inputs[2] = time; }
    const duration = $('#timeDuration').val(); if(r(duration)){ inputs[3] = duration; }
    inputs[4] = tID;
    for (var i = 1; i < 6; i++) {
      const cv = $('.right-inputs-hh'+i+' .clientVorname').val();
      const cn = $('.right-inputs-hh'+i+' .clientName').val();
      const cm = $('.right-inputs-hh'+i+' .clientMail').val();
      const ca = $('.right-inputs-hh'+i+' .clientAdresse').val();
      const ct = $('.right-inputs-hh'+i+' .clientTNR').val();
      if(cv.length >= 3){
        if(r(cv) == true && r(cn) == true && r(cm) == true && r(ca) == true && r(ct) == true){
          haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ca + ";" + ct;
        }
      }
    }
    inputs[5] = haushalt;
    if(haushalt.length > 0){
      $.ajax({
        url: "sync.php", method: "POST", data: { createReserve: inputs },
        success: function(result) {
          //console.log("Result: " + result);
          if(result == "1"){
            tableClose();
            //table, time, date, duration, amount
            viewReserved(tID,time,date,duration,amount);
          }
        }
      });
    }
}

function submitLogin(){
  var n = $('#hubraumName').val(); var p = $('#hubraumSecure').val();
  if(n.length >= 5 && p.length >= 7){
    $.ajax({
      url: "sync.php", method: "POST", data: { hubName: CryptoJS.MD5(n).toString(), hubSecure: CryptoJS.MD5(p).toString() },
      success: function(result) {
        location.reload();
        loginClose();
      }
    });
  }
}

function submitLogoff() {
  setCookie("rSession","",-1);
  location.reload();
}

// regex t=text | r=regex
function r(t){
  const regex = "/[+_\-:;\/*{}´^<>=&%$§#']+/gm";
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
        const ca = $('.right-inputs-hh'+i+' .clientAdresse').val();
        const ct = $('.right-inputs-hh'+i+' .clientTNR').val();
        if(cv.length >= 3){
          if(r(cv) == true && r(cn) == true && r(cm) == true && r(ca) == true && r(ct) == true){
            haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ca + ";" + ct + ";" + ci;
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

function dateToSQL(d) {
  var today = d;
  var dd = String(today.getDate()).padStart(2, '0'); var mm = String(today.getMonth() + 1).padStart(2, '0'); var yyyy = today.getFullYear();
  return yyyy + "-" + mm + "-" + dd;
}
