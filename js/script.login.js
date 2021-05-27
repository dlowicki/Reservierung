$(document).ready(()=>{
  // Überprüfe Login
  (async() => { await userCheck().then(function(result){ if(result == true){ $('.icon-user').css("color","green"); } }); })();

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

function loginClose() {
  $('#viewLogin').empty();
  $("#viewLogin").css("display","none");
  $('.container-reserve').css("background-color","transparent");
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
