
function regex(t){
  const regex = "/[+_\-:;\/*{}´^<>=&%$§#']+/gm";
  var m = regex.match(t, regex);
  if(m !== null){
    m.forEach((match, groupIndex) => { return false; });
  }
  return true;
}

function generateUUID() { // Public Domain/MIT
    var d = new Date().getTime();//Timestamp
    var d2 = (performance && performance.now && (performance.now()*1000)) || 0;//Time in microseconds since page-load or 0 if unsupported
    return 'xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16;//random number between 0 and 16
        if(d > 0){//Use timestamp until depleted
            r = (d + r)%16 | 0;
            d = Math.floor(d/16);
        } else {//Use microseconds since page-load if supported
            r = (d2 + r)%16 | 0;
            d2 = Math.floor(d2/16);
        }
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
}

/* TISCH BEARBEITUNG NACH RESERVIERUNG */
$(document).ready(function(){
  if($('#viewChangeReserve').length >= 1){
    var rCookie = $('#viewChangeReserve').attr("class");
    $.ajax({
      url: "sync.php",
      method: "POST",
      data: { changeReserve: rCookie},
      success: function(result) {
        if(result){
          var d = JSON.parse(result);

          $('#viewChangeReserve').append('<div class="cr-top"><h2>Tisch '+d[0]['tableID']+'</h2><p>Reserviert von '+d[0]['reserveStart']+' Uhr bis '+d[0]['reserveEnd']+' Uhr</p></div>');
          $('#viewChangeReserve').append('<div class="cr-content"></div>');
          $('#viewChangeReserve').append('<div class="cr-submit"><button id="cr-back">Schließen</button><button id="cr-change" class="'+d[0]['reserveID']+'">Bestätigen</button></div>');

          $('.cr-content').append('<div class="cr-nav"><ul style="list-style:none; text-align: left;"></ul></div>');
          $('.cr-content').append('<div class="cr-main"></div>');

          for (var i = 1; i < 11; i++) {
            $('.cr-nav ul').append('<li id="'+i+'">Haushalt '+i+'</li>');
          }

          for (var i = 1; i < 11; i++) {
              $('.cr-main').append('<div id="right-inputs-hh'+i+'" class="hh">');
              $('#right-inputs-hh'+i).append('<i class="fas fa-user-times fa-2x"></i>');
              $('#right-inputs-hh'+i).append('<h3>Haushalt '+i+'</h3>');
              if(typeof d[0]['clients'][i] !== "undefined"){
                $('#right-inputs-hh'+i).append('<input type="hidden" value="'+d[0]['clients'][i]['clientID']+'" class="clientID">');
                $('#right-inputs-hh'+i).append('<input type="text" value="'+d[0]['clients'][i]['clientVorname']+'" class="clientVorname" placeholder="Vorname">');
                $('#right-inputs-hh'+i).append('<input type="text" value="'+d[0]['clients'][i]['clientName']+'" class="clientName" placeholder="Name">');
                $('#right-inputs-hh'+i).append('<input type="text" value="'+d[0]['clients'][i]['clientMail']+'" class="clientMail" placeholder="E-Mail">');
                $('#right-inputs-hh'+i).append('<input type="text" value="'+d[0]['clients'][i]['clientAdresse']+'" class="clientAdresse" placeholder="Adresse">');
                $('#right-inputs-hh'+i).append('<input type="text" value="'+d[0]['clients'][i]['clientTNR']+'" class="clientTNR"  placeholder="Telefon">');
              } else {
                $('#right-inputs-hh'+i).append('<input type="hidden" value="'+generateUUID()+'" class="clientID">');
                $('#right-inputs-hh'+i).append('<input type="text" class="clientVorname" placeholder="Vorname">');
                $('#right-inputs-hh'+i).append('<input type="text" class="clientName" placeholder="Name">');
                $('#right-inputs-hh'+i).append('<input type="text" class="clientMail" placeholder="E-Mail">');
                $('#right-inputs-hh'+i).append('<input type="text" class="clientAdresse" placeholder="Adresse">');
                $('#right-inputs-hh'+i).append('<input type="text" class="clientTNR" placeholder="Telefon">');
              }
              $('#right-inputs-hh'+i).css("display","none");
          }
          $('#right-inputs-hh1').css("display","block");
          $('#right-inputs-hh1').addClass("cr-current")
        }
      }
    });
  }
  $(document).on("click",".cr-nav ul li", function(){
    const id = $(this).attr("id");
    $('.cr-current').css("display","none");
    $('.cr-current').removeClass("cr-current");
    $('#right-inputs-hh'+id).addClass("cr-current");
    $('#right-inputs-hh'+id).css("display","block");
  });
  $(document).on("click","#cr-back",function(){
    window.location.href = "index.php";
  });
  $(document).on("click",".cr-main .fa-user-times",function(){
    if(confirm("Dieser Haushalt wird aus der Reservierung gelöscht, fortfahren?")){
      var id = $('.cr-current .clientID').val();
      $.ajax({
        url: "sync.php", method: "POST", data: { crDelete: id },
        success: function(result) {
          if(result == "1"){
            location.reload();
          } else {
            alert("Ein Fehler ist aufgetreten!\n"+result);
          }
        }
      });
    }
  });
  $(document).on("click","#cr-change",function(){
    var haushalt = new Array();
    for (var i = 1; i < 11; i++) {
      const ci = $('#right-inputs-hh'+i+' .clientID').val();
      const cv = $('#right-inputs-hh'+i+' .clientVorname').val();
      const cn = $('#right-inputs-hh'+i+' .clientName').val();
      const cm = $('#right-inputs-hh'+i+' .clientMail').val();
      const ca = $('#right-inputs-hh'+i+' .clientAdresse').val();
      const ct = $('#right-inputs-hh'+i+' .clientTNR').val();
      if(cv.length >= 3){
        if(r(cn) == true && r(cm) == true && r(ca) == true && r(ct) == true){
          haushalt[i-1] = cv + ";" + cn + ";" + cm + ";" + ca + ";" + ct + ";" + ci;
        }
      }
    }

    if(haushalt.length > 0){
      const rID = $(this).attr("class");
      $.ajax({
        url: "sync.php", method: "POST", data: { crSubmit: haushalt, crID: rID },
        success: function(result) {
          console.log(result);
          if(result == "1"){
            window.location.href = "index.php";
          } else {
            alert("Ein Fehler ist aufgetreten!\n"+result);
          }
        }
      });
    }
  });
});


// ACP TABLE WINDOW
$(document).on("click",".acp-bottom-nav li", function(){
  var type = $(this).text();
  $(".acp-bottom-nav li").removeClass("acp-bottom-nav-current");
  $(this).addClass("acp-bottom-nav-current");
  $('#acp-bottom-reservierung').css("display","none");
  $('#acp-bottom-haushalt').css("display","none");
  if(type == "Reservierung") {
    $('#acp-bottom-reservierung').css("display","flex");
  } else if(type == "Haushalt"){
    $('#acp-bottom-haushalt').css("display","block");
  }
});

$(document).on("click",".information-box-acp",function(){
  $('.information-box-acp').css("color","white");
  $('.information-box-acp').css("font-weight","400");

  var id = $(this).attr("id");
  if($('#bt-freigeben').hasClass(id) || $(this).text() == "Keine Reservierung gefunden"){
    $('#bt-eingetroffen').removeClass();
    $('#bt-freigeben').removeClass();
    $('#bt-abgesagt').removeClass();
    $('#bt-no-show').removeClass();
    $('#acpReserveID').val("");
    $('#acpSubmit').val("Erstellen");
    // Hier noch ACP leeren
    clearACP();
    return;
  }

  $(this).css("font-weight","600");

  $('#bt-eingetroffen').removeClass();
  $('#bt-freigeben').removeClass();
  $('#bt-abgesagt').removeClass();
  $('#bt-no-show').removeClass();

  $('#bt-eingetroffen').addClass(id);
  $('#bt-freigeben').addClass(id);
  $('#bt-abgesagt').addClass(id);
  $('#bt-no-show').addClass(id);
  $('#acpReserveID').val(id);

  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { acpInputs: id},
    success: function(result) {
      if(result){
        var d = JSON.parse(result);
        console.log(d);
        $('#acpInputTime').val(d['reserve']['reserveStart']);
        $('#acpInputDate').val(d['reserve']['reserveDate']);
        if(d['reserveDuration'] == "gz"){d['reserve']['reserveDuration']=2;}else{d['reserve']['reserveDuration']=1;}
        $('#acpInputDuration').val(d['reserve']['reserveDuration']);
        $('#acpInputAmount').val(d['reserve']['reserveAmount']);

        for (var count = 1; count <= Object.keys(d['clients']).length; count++) {
          $('.right-inputs-hh'+count+' .clientID').val(d['clients'][count]['clientID']);
          $('.right-inputs-hh'+count+' .clientVorname').val(d['clients'][count]['clientVorname']);
          $('.right-inputs-hh'+count+' .clientName').val(d['clients'][count]['clientName']);
          $('.right-inputs-hh'+count+' .clientMail').val(d['clients'][count]['clientMail']);
          $('.right-inputs-hh'+count+' .clientAdresse').val(d['clients'][count]['clientAdresse']);
          $('.right-inputs-hh'+count+' .clientTNR').val(d['clients'][count]['clientTNR']);
        }
        $('#acpSubmit').val("Bearbeiten");
      }
    }
  });
});

$(document).on("click",".acp-settings-buttons button",function(){
  var reserveID = $(this).attr("class");
  if(!reserveID){
    alert("Sie müssen vorher eine Reservierung auswählen!");
    return;
  }

  var buttonType = $(this).attr("id");
  var iconType,dataType,color;

  if($('#'+reserveID).css('background-color') == "rgb(0, 109, 119)" && buttonType == "bt-no-show"){
    alert("No Show wurde für die Reservierung bereits eingetragen!");
    return;
  }

  switch (buttonType) {
    case "bt-eingetroffen":
      dataType = "1";
      iconType = "bt-eingetroffen .fa-check";
      color = "#2b9348";
      break;
    case "bt-freigeben":
      dataType = "2";
      iconType = "bt-freigeben .fa-unlock";
      color = "#ee6c4d";
      break;
    case "bt-abgesagt":
      dataType = "3";
      iconType = "bt-abgesagt .fa-user-slash";
      color = "#ba181b";
      break;
    case "bt-no-show":
      dataType = "4";
      iconType = "bt-no-show .fa-user-times";
      color = "#006d77";
      break;
  }

  var inputDate = $('#acpInputDate').val();

  $.ajax({
    url: "sync.php",
    method: "POST",
    data: { acpButton: dataType, acpReserveID: reserveID, acpDate: inputDate},
    success: function(result) {
      console.log(result);
      if(result){
        $('#'+reserveID).css("background-color",color);
        return;
      }
      alert("Fehler: Tisch konnte nicht bearbeitet werden!");
    }
  });
});

function clearACP() {
  $('#acpInputTime').val("17:00");
  //$('#acpInputDate').val("");
  $('#acpInputDuration').val(1);
  $('#acpInputAmount').val("");

  for (var count = 1; count <= 6; count++) {
    $('.right-inputs-hh'+count+' .clientID').val("");
    $('.right-inputs-hh'+count+' .clientVorname').val("");
    $('.right-inputs-hh'+count+' .clientName').val("");
    $('.right-inputs-hh'+count+' .clientMail').val("");
    $('.right-inputs-hh'+count+' .clientAdresse').val("");
    $('.right-inputs-hh'+count+' .clientTNR').val("");
  }
  $('#acpSubmit').val("Erstellen");
}
