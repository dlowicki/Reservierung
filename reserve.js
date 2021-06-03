/*
Zu beachten bei Update auf hubRaum
sync.php getFeiertage() URL bearbeiten
Update Datenbank sync.php und script.admin.php

$servername = "127.0.0.1:3306";
$username = "w10072res";
$password = "jHwsa2rr";
$db = "w10072res";

jk2R_6X
*/


$(document).ready(function(){
  // DEBUG
  //viewTable(98,localStorage.getItem('rCalendar').split(';')[0]);
  //viewReserved('36',1,'2021-05-21','6');
  //viewError('test123');

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

  // Lade Tische async mit Ampelsystem
  (async() => { await loadTables(localStorage.getItem('rCalendar').split(';')[0],localStorage.getItem('rCalendar').split(';')[1]); })();

  $(document).on('click','.table', function(){
    if($('.form-table').length <= 0){
      var id = jQuery(this).attr("id").split("-")[1];
      var usercheck = userCheck();
      if(usercheck == false){ viewTable(id, localStorage.getItem('rCalendar').split(';')[0]); } else { window.location.href = "admin.php?reservierungen&table="+id+"&date="; }
    }
  });

	$('.table-close').click(function(){ $("#viewTable").css("display","none"); $('#viewTable').empty(); $('.container-reserve').css("background-color","white"); });
	$('.icon-user').click(function(){ if($('#form-login').length <= 0){ viewLogin(); } });
});


/*async function userCheck() {
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
}*/

async function loadTables(date, bs) {
  let result;
  try {
    var data = "";
    $.ajax({ url: "sync.php", method: "POST", data: { loadTables: date+";"+bs }, success: function(result) {
		$('#tischplan-svg').empty();
		data = JSON.parse(result);
		data.forEach((item, i) => {
		if(item['tableActive'] == 'open'){ item['tableActive'] = 'rgba(60, 179, 113,0.5)'; } else { item['tableActive'] = 'rgba(255, 0, 0,0.5)'; }
		var xml = jQuery.parseXML('<rect xmlns="http://www.w3.org/2000/svg" class="table" id="tisch-'+item["tableID"]+'" height="'+item["height"]+'" width="'+item["width"]+'" y="'+item["y"]+'" x="'+item["x"]+'" stroke="#000" fill="'+item["tableActive"]+'"/>');
		$('#tischplan-svg').append(xml.documentElement);
		});
	} });
    return true;
  } catch (e) { console.log("Error loadTables: " + e); }
}








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
      var d = JSON.parse(result);
      var tID = "'"+d['tableID']+"'";

      // Wenn Tisch aktiv ist und Reservierung nicht vorhanden, Tisch FREI andernfalls BELEGT
      if(d['tableActive'] == "open" && d['tableReserved'] == "open"){
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: green;">FREI</span></h1>');
      } else {
        $('#viewTable').append('<h1>Tisch '+id+' <span style="color: red;">RESERVIERT</span></h1>');
      }
	     $('#viewTable .loader').remove();
      $('#viewTable').append("<form method='POST' class='form-table' onsubmit='event.preventDefault();'></form>");

      $('.form-table').append("<div class='form-table-middle'></div>");
      $('.form-table-middle').append("<div class='form-table-left'></div>");
      $('.form-table-middle').append("<div class='form-table-right'></div>");

      $('.form-table-left').append("<div id='container-information'><h3>Reservierungen</h3><div id='container-information-content'></div></div>");
      getReservierungen(d['tableID'], date);

      $('.form-table-left').append("<h2>Tisch reservieren</h2>");
      $('.form-table-left').append("<p>Damit ein Tisch reserviert werden kann werden folgende Daten benötigt</p>");
      $('.form-table-left').append("<ul><li>Personenanzahl <b>"+d['tableMin']+" - "+d['tableMax']+"</b></li><li><b>Datum</b> auswählen</li><li>Blockzeit zwischen <b>17:00 - 19:30 Uhr</b> und <b>19:30 - 22:00 Uhr</b> wählen</li></ul>");


      $('.form-table-left').append("<div class='form-table-left-inputs'></div>");

      var options = ""; for (var i = parseInt(d['tableMin']); i <= parseInt(d['tableMax']); i++) { options = options + "<option value='"+i+"'>"+i+"</option>";}
      $('.form-table-left-inputs').append('<select id="amount">'+options+'</select><i class="fas fa-users"></i></div>');
      $('.form-table-left-inputs').append('<input type="date" id="timeDate" value="'+date+'">');
      var localBlock = localStorage.getItem('rCalendar').split(';')[1];
      $.getJSON('script/load.timeblock.php', function(data) {
        $('.form-table-left-inputs').append('<select id="timeBlock"></select>');
        data.forEach((item, i) => {
          time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
          if(item['id'] == localBlock){
            $('#timeBlock').append('<option value="'+item["id"]+'" id="option'+item["id"]+'" selected>'+time+' Uhr</option>');
          } else { $('#timeBlock').append('<option value="'+item["id"]+'" id="option'+item["id"]+'">'+time+' Uhr</option>'); }
        });
      });

      // Setze Data für Überprüfung von Feiertag, Öffnungszeit oder Event bei onChange
      $('#timeDate').data('before', date);

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

// Klick auf neuen Haushalt, neuen Haushalt anzeigen
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


// Beim ändern des Datum neuen Table anzeigen
// Abfrage von Datum ob an Feiertag oder geschlossenen Tag
$(document).on('change','#timeDate',function(event){
  var tisch = $('#viewTable h1').text().split(' ')[1];
  var date = $(this).val(); const today = new Date().toISOString().slice(0, 10);
  if(todayPlusSixWeeks() <= date) { $('#timeDate').css('background-color','#e63946'); viewError('Reservierungen können maximal 6 Wochen im Voraus eingetragen werden!'); $(this).val($(this).data('before')); return; }
  if(date < today) { $('#timeDate').css('background-color','#e63946'); viewError('Datum kann nicht in der Vergangenheit liegen!'); $(this).val($(this).data('before')); return; }

  $.ajax({ url: "sync.php", method: "POST", data: { confirmDay: date},
    success: function(result) {
      // Wenn result==1 Dann Tag nicht geöffnet bzw. Event an dem Tag
      if(result=="1"){
        viewError('HubRaum hat am ' + date + ' nicht geöffnet!'); $(this).val($(this).data('before')); return;
      } else { // Restaurant hat am ausgewählten Tag geöffnet
        $.getJSON('script/load.feiertag.php',function(data){
          var check = true;
          data.forEach((item, i) => { if(item['date'] == date){ check=false; } });
          if(date.length == 10 && time.length >= 1 && check==true){
            // Schließe Window Table und öffne neues Window
            tableClose(); viewTable(tisch, date);
          } else {
            $('#timeDate').css('background-color','#e63946');
            viewError('HubRaum hat am ' + date + ' nicht geöffnet!');
            $(this).val($(this).data('before')); return;
          }
        });
      }
    }
  });
});


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
              $('#container-information-content').append("<div class='information-box' id='rsBlock"+item['rB']+"' style='background-color: "+colors[colorNum]+"'>Reserviert<br>"+time[0].slice(0,5)+" Uhr - "+time[1].slice(0,5)+" Uhr</div>");
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



function viewCoronaInfo() {
  $('#viewCoronaInfo').css("display","block");
  $('#viewCoronaInfo').append("<h3>Informationen für Corona</h3><p>Bitte Denken Sie daran, dass bei mehreren Haushalten an einem Tisch, pro Haushalt eine Kontakperson registriert werden muss.<br><br>Vielen Dank für ihr Verständnis!</p>");
  setTimeout(function(){
    $('#viewCoronaInfo').css("display","none");
    $('#viewCoronaInfo').empty();
  }, 5000);
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
        if(r(cv) == true && r(cn) == true && r(cm) == true && r(ct) == true){
          if(validatMail(cm)==false){ $('.right-inputs-hh'+i+' .clientMail').css('color','red'); return false; }
          haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ct;
        }
      }
    }
    inputs[4] = haushalt;
    if(haushalt.length > 0){
		const mailTO = $('.right-inputs-hh0 .clientMail').val();
    console.log(inputs);
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
              tableClose();
				      viewError('Reservierung wurde erfolgreich durchgeführt. Es konnte keine E-Mail verschickt werden!');
      				viewReserved(tID,timeBlock,date,amount);
            case "4":
              viewError('Ein oder mehrere E-Mail Adressen sind nicht gültig!');
          }
        }
      });
    }
}
$(document).on('focus','.clientMail',function(){ $('.clientMail').css('color','black'); });

function viewReserved(table, blockID, date, amount){
  $.getJSON('http://localhost/html/Reservierung/script/load.timeblock.php', function(data) {
    $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
    $('.container-reserve').append('<div id="viewReserved"></div>');
    $('#viewReserved').append('<h2>Tisch '+table+' am '+germanDateFormat(date)+' reserviert!</h2>');
    data.forEach((item, i) => {
      if(item['id'] == blockID){
        $('#viewReserved').append('<ul><li>Anzahl der Personen '+amount+'</li><li>Reserviert für '+item["start"]+' - '+item["end"]+' Uhr</li></ul>');
      }
    });
    $('#viewReserved').append('<p><p>Wir wünschen Ihnen einen angenehmen Aufenthalt!<br><br>Falls die Reservierung nicht mehr stimmen sollte, bitten wir um eine telefonische Absage oder korrektur</p></p>');
    $('#viewReserved').append('<button onClick="reservedClose()" id="viewReservedButton">Verstanden</button>');
  });
}



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

function validatMail(input){
  var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
  if (input.match(validRegex)) { return true; } return false;
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

function getTableParameter() {
  var url = new URL(window.location.href); var c = url.searchParams.get("table"); if(c != null){ return c.split("&")[0]; } return false;
}

function dateToSQL() { return new Date().toISOString().slice(0, 10); }
function germanDateFormat(date) { var spl = date.split('-'); return spl[2]+"."+spl[1]+"."+spl[0]; }
function todayPlusSixWeeks() {
  var today = new Date();
  var nextweek = new Date(today.getFullYear(), today.getMonth(), today.getDate()+(7*6));
  return nextweek.getFullYear() + "-" + ("0" + (nextweek.getMonth() + 1)).slice(-2) + "-" + ("0" + nextweek.getDate()).slice(-2);
}
