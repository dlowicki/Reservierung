function viewCalendar(){
  if($('.calendar-inputs').length >= 1){ return false; }
  $('#viewCalendar').append('<div class="calendar-inputs"></div>');
  if(localStorage.getItem('rCalendar') !== null){
    var rc = localStorage.getItem('rCalendar').split(';');
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Datum</h4><input type="date" id="calendar-date" value="'+rc[0]+'"></div>');
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Blockzeit</h4><select id="calendar-time"></select></div>');
    $.getJSON('script/load.timeblock.php', function(data) {
      data.forEach((item, i) => {
          time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
          if(item['id'] == rc[1]){
            $('.calendar-inputs select').append('<option value="'+item["id"]+'" selected>'+time+' Uhr</option>');
          } else {
            $('.calendar-inputs select').append('<option value="'+item["id"]+'">'+time+' Uhr</option>');
          }
      });
    });
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Personen</h4><input type="number" min="1" max="10" id="calendar-amount" value="'+rc[2]+'"></div>');
  } else {
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Blockzeit</h4><input type="date" id="calendar-date"></div>');
    document.getElementById('calendar-date').valueAsDate = new Date();
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Blockzeit</h4><select id="calendar-time"></select></div>');
    $.getJSON('script/load.timeblock.php', function(data) {
      data.forEach((item, i) => {
        time = item['start'].substring(0,item['start'].length - 3) + " - " + item['end'].substring(0,item['end'].length - 3);
        $('.calendar-inputs select').append('<option value="'+item["id"]+'">'+time+' Uhr</option>');
      });
    });
    $('.calendar-inputs').append('<div class="calendar-box"><h4>Personen</h4><input type="number" min="1" max="10" id="calendar-amount" value="1"></div>');
  }
  $('#viewCalendar').append('<div class="calendar-buttons"></div>');
  var link = "'https://www.hubraum-durlach.de/'";
  $('.calendar-buttons').append('<button id="calendar-confirm">Tisch auswählen</button><button id="calendar-leave" onClick="window.location.href='+link+';">Reservierung verlassen</button>');
  $('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
  $('#viewCalendar').css("display","block");
}

$(document).on('click','#calendar-confirm',function(){
  const date = $('#calendar-date').val(); const time = $('#calendar-time').val(); const amount = $('#calendar-amount').val();
  var today = new Date().toISOString().slice(0, 10);
  if(time == null) { $('#calendar-time').css('background-color','#e63946'); return; }
  if(todayPlusSixWeeks() <= date) { $('#calendar-date').css('background-color','#e63946'); viewError('Reservierungen können maximal 6 Wochen im Voraus eingetragen werden!'); return; }
  if(date < today) { $('#calendar-date').css('background-color','#e63946'); viewError('Datum kann nicht in der Vergangenheit liegen!'); return; }
  if(amount <= 0 || amount > 10){ viewError('Es können nur maximal 10 Personen teilnehmen'); return; }
  $.ajax({ url: "sync.php", method: "POST", data: { confirmDay: date},
    success: function(result) {
      // Wenn result==1 Dann Tag nicht geöffnet bzw. Event an dem Tag
      if(result=="1"){
        viewError('HubRaum hat am ' + germanDateFormat(date) + ' nicht geöffnet!'); return;
      } else { // Restaurant hat am ausgewählten Tag geöffnet
        $.getJSON('script/load.feiertag.php',function(data){
          var check = true;
          data.forEach((item, i) => { if(item['date'] == date){ check=false; } });
          if(date.length == 10 && time.length >= 1 && check==true){
            // Setze Datum in localStorage und lade Seite neu
            localStorage.setItem('rCalendar',date+';'+time+';'+amount); location.reload();
          } else {
            $('#calendar-date').css('background-color','#e63946');
            viewError('HubRaum hat am ' + germanDateFormat(date) + ' nicht geöffnet!');
          }
        });
      }
    }
  });
});

$(document).on('click','.fa-calendar-alt',function(){ viewCalendar(); });
