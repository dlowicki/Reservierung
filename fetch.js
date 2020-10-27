
function regex(t){
  const regex = "/[+_\-:;\/*{}´^<>=&%$§#']+/gm";
  var m = regex.match(t, regex);
  if(m !== null){
    m.forEach((match, groupIndex) => { return false; });
  }
  return true;
}

/* TISCH BEARBEITUNG NACH RESERVIERUNG */
$(document).ready(function(){
  if($('#viewChangeReserve').length >= 1){
    var id = $('#viewChangeReserve').attr("class");
    $.ajax({
      url: "sync.php",
      method: "POST",
      data: { changeReserve: id},
      success: function(result) {
        console.log("TEST: " + result);
        if(result){
          var d = JSON.parse(result);
          console.log(d);

          $('#viewChangeReserve').append('<div class="cr-top"><h2>Tisch '+d[0]['tableID']+'</h2><p>Reserviert von '+d[0]['reserveStart']+' Uhr bis '+d[0]['reserveEnd']+' Uhr</p></div>');
          $('#viewChangeReserve').append('<div class="cr-content"></div>');

          $('.cr-content').append('<div class="cr-nav"><ul style="list-style:none; text-align: left;"></ul></div>');
          $('.cr-content').append('<div class="cr-main"></div>');

          //Object.keys(d[0]['clients']).length
          for (var i = 0; i < 10; i++) {
            if(typeof d[0]['clients'][i] !== "undefined"){
              $('.cr-nav ul').append('<li>Haushalt '+(i+1)+'</li>');
              $('.cr-main').append('<div class="right-inputs-hh'+(i+1)+' hh">');
              $('.right-inputs-hh'+(i+1)).append('<h3>Haushalt 1</h3>');
              
              $('.right-inputs-hh'+(i+1)).append('<input type="hidden" value="'+d[0]['clients'][i]['clientID']+'" class="clientID">');
              var t = "'clientVorname'";
              $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientVorname" placeholder="Vorname">');
              t = "'clientName'";
              $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientName" placeholder="Name">');
              t = "'clientMail'";
              $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientMail" placeholder="E-Mail">');
              t = "'clientAdresse'";
              $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientAdresse" placeholder="Adresse">');
              t = "'clientTNR'";
              $('.right-inputs-hh'+(i+1)).append('<input type="text" class="clientTNR" placeholder="TNR">');
              $('.right-inputs-hh'+(i+1)).css("display","none");
            }

          }
          $('.right-inputs-hh1').css("display","block");

        }
      }
    });
  }
});

// ACP TABLE WINDOW
$(document).on("click",".acp-bottom-nav li", function(){
  var type = $(this).text();
  $(".acp-bottom-nav li").removeClass("acp-bottom-nav-current");
  $(this).addClass("acp-bottom-nav-current");
  $('#acp-bottom-reservierung').css("display","none");
  $('#acp-bottom-haushalt').css("display","none");
  if(type == "Reservierung") {
    $('#acp-bottom-reservierung').css("display","block");
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
