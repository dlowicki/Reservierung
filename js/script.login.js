$(document).ready(()=>{
  // Überprüfe Login
  if(userCheck() == CryptoJS.MD5(getCookie('rSession')).toString()){ $('.icon-user').css("color","green"); }


});

// UserCheck nochmal überprüfen. Daten mittels PHP abgleichen nicht Javascript weil unsicher manipulation
function userCheck() {
var c = getCookie('rSession'); if(!c){ return ''; }
var rs = "";
rs = $.ajax({ url: "sync.php", method: "POST", async: false, data: { userCheck: c }, success: function(result) { rs = result.toString(); } });
return rs.responseText;
}

function viewLogin() {
	$('#viewLogin').empty();
	$('#viewLogin').css("display","block");
	$('.container-reserve').css("background-color","rgba(100,100,100,0.3)");
	$('#viewLogin').append('<i class="fa fa-times fa-2x" onClick="loginClose()"></i>');

  if(getCookie("rSession") != ""){
	   $('#viewLogin').css("height","300px");
     var usercheck = userCheck();
     if(usercheck == CryptoJS.MD5(getCookie('rSession')).toString()){
       $('#viewLogin').append('<i class="fa fa-user-circle fa-5x login-icon"></i>');
       $('#viewLogin').append('<h3>Bereits angemeldet</h3>');
       var today = new Date();
       var dd = String(today.getDate()).padStart(2, '0'); var mm = String(today.getMonth() + 1).padStart(2, '0'); var yyyy = today.getFullYear();
       today = "'" + yyyy + "-" + mm + "-" + dd + "'";
       $('#viewLogin').append('<button id="login-NoShow">Übersicht</button>');
       $('#viewLogin').append('<button onClick="submitLogoff()">Abmelden</button>');
     } else {
       submitLogoff();
     }
  } else {
  $('#viewLogin').append('<i class="fa fa-user-circle fa-5x login-icon"></i>');
	$('#viewLogin').append('<form id="form-login" onsubmit="event.preventDefault();"></form>');
	$('#form-login').append('<input type="text" id="hubraumName" placeholder="Name..."><input type="password" id="hubraumSecure" placeholder="Passwort..."><input type="submit" onClick="submitLogin()" value="Login">');
  }
}

function loginClose() {
  $('#viewLogin').empty();
  $("#viewLogin").css("display","none");
  $('.container-reserve').css("background-color","transparent");
}

function submitLogin(){
  var n = $('#hubraumName').val(); var p = $('#hubraumSecure').val();
  if(n.length >= 2 && p.length >= 5){
    $.ajax({
      url: "sync.php", method: "POST", data: { hubName: n, hubSecure: CryptoJS.MD5(p).toString() },
      success: function(result) {
        if(result!='1'){ alert('Ein Fehler ist aufgetreten! \nFehlercode: '+result); }
        location.reload(); loginClose(); return;
      }
    });
  }
}

function submitLogoff() { setCookie("rSession","",-1); location.reload(); }

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
