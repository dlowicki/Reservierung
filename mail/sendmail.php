<?php
require("phpmailer/PHPMailerAutoload.php");

// table, blockID, date, amount
function sendMail($tableID, $datum, $blockTime, $anzahl, $to){
	$mail = new PHPMailer;
	//mertero123
	
	$mail->isSMTP();
	$mail->Host = 'smtp.ionos.de';  					// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                           	// Enable SMTP authentication
	$mail->Username = 'no-reply@mertero.de';     		// SMTP username
	$mail->Password = 'mertero123';                     // SMTP password
	$mail->SMTPSecure = 'tls';                        	// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                	// TCP port to connect to

	$mail->From = 'no-reply@mertero.de';
	$mail->FromName = 'HubRaum-Durlach';


	$mail->addAddress($to);               // Name is optional
	$mail->CharSet = "utf-8";
	$mail->Subject = 'HubRaum Reservierung';
	$text = "Lieber Gast,<br><br>Wir freuen uns über Ihre Reservierung und auf Ihren Besuch.<br>Sie haben den Tisch $tableID am $datum zur Blockzeit $blockTime Uhr für $anzahl Personen reserviert.";
	$text = $text."<br><br><b>Corona</b><br>Bitte beachten Sie die aktuellen Corona Regeln. Der Eintritt ist erlaubt mit negativem Schnelltest ( aus einem Testzentrum, vom Arbeitgeber oder Dienstleister), ";
	$text = $text . "der nicht älter als 24 Stunden ist, mit vollem Impfschutz ( 14 Tage nach der zweiten Impfung) oder mit einem Nachweis über eine Infektion, wobei diese mindestens 28 Tage und maximal sechs Monate zurückliegen muss. ";
	$text = $text . "<br><br><b>No-Show</b><br>Wir bitten Sie ganz herzlich, sich rechtzeitig bei uns zu melden, wenn Sie ihre Reservierung nicht wahrnehmen können oder sich die angegebene Personenzahl verändert. ";
	$text = $text . "Da nicht wahrgenommene Reservierungen für uns einen großen Verlust darstellen, hoffen wir auf Ihre Solidarität und auf Ihr Verständnis. Wir behalten uns vor, bei wiederholtem Nicht- Erscheinen keine Reservierungsanfragen dieser Gäste mehr anzunehmen.";
	$mail->Body = $text;
	$mail->IsHTML(true);

	// Mail konnte nicht verschickt werden
	if(!$mail->send()) {
		return false;
	} else {
		/*// send confirmation to Hubraum
		$mail2 = new PHPMailer;
		$mail2->isSMTP();
		$mail2->Host = 'smtp.ionos.de';  												// Specify main and backup SMTP servers
		$mail2->SMTPAuth = true;                               	// Enable SMTP authentication
		$mail2->Username = 'no-reply@osteriacarre.de';          // SMTP username
		$mail2->Password = 'xH6&N552';                          // SMTP password
		$mail2->SMTPSecure = 'tls';                           	// Enable TLS encryption, `ssl` also accepted
		$mail2->Port = 587;                                   	// TCP port to connect to
		$mail2->From = 'no-reply@osteriacarre.de';
		$mail2->FromName = 'Reservierung';

		$mail2->addAddress('info@osteria-carre.de');
		$mail2->addAddress('e.brumar@gmx.net');
		$mail2->CharSet = "utf-8";
		$mail2->Subject = 'Reservierung';
		$mail2->Body    = "Reservierung: <ul><li>Tisch: $tableID</li><li>Datum: $datum</li><li>Zeit: $blockTime</li><li>Anzahl: $anzahl</li></ul>";
		$mail2->IsHTML(true);
		if(!$mail2->send()){ return false; }*/
		return true;
	}

}





/*

//Form Data
$txtData = "";
$htmData = "";
$txtData .= "Anrede: " . $_POST["element_0"] . "\r\n";
$htmData .= "<tr><td width=\"25%\"><b>Anrede:</b></td><td>" . $_POST["element_5"] . "</td></tr>";
$txtData .= "Name: " . $_POST["element_1"] . "\r\n";
$htmData .= "<tr><td width=\"25%\" bgcolor=\"#EEEEEE\"><b>Name:</b></td><td bgcolor=\"#EEEEEE\">" . $_POST["element_5"] . "</td></tr>";
$txtData .= "Vorname: " . $_POST["element_2"] . "\r\n";
$htmData .= "<tr><td width=\"25%\"><b>Vorname:</b></td><td>" . $_POST["element_3"] . "</td></tr>";
$txtData .= "Telefonnummer ( Nicht Erforderlich): " . $_POST["Itm_8_00_4"] . "\r\n";
$htmData .= "<tr><td width=\"25%\" bgcolor=\"#EEEEEE\"><b>Telefonnummer ( Nicht Erforderlich):</b></td><td bgcolor=\"#EEEEEE\">" . $_POST["element_4"] . "</td></tr>";
$txtData .= "E-Mail : " . $_POST["element_5"] . "\r\n";
$htmData .= "<tr><td width=\"25%\"><b>E-Mail :</b></td><td>" . $_POST["element_6"] . "</td></tr>";
$txtData .= "Ihre Besonderen Wünsche: " . $_POST["element_5"] . "\r\n";
$htmData .= "<tr><td width=\"25%\" bgcolor=\"#EEEEEE\"><b>Ihre Besonderen Wünsche:</b></td><td bgcolor=\"#EEEEEE\">" . $_POST["element_5"] . "</td></tr>";
$txtData .= "Reservierung für den: " . " " . $_POST["element_5"] . " " . $_POST["element_5"] . " " . $_POST["element_5"] . "\r\n";
$htmData .= "<tr><td width=\"25%\"><b>Reservierung für den:</b></td><td>" . " " . $_POST["Itm_8_00_7_d"] . " " . $_POST["element_5"] . " " . $_POST["element_5"] . "</td></tr>";
$txtData .= "Anzahl der Gäste: " . $_POST["element_5"] . "\r\n";
$htmData .= "<tr><td width=\"25%\" bgcolor=\"#EEEEEE\"><b>Anzahl der Gäste:</b></td><td bgcolor=\"#EEEEEE\">" . $_POST["element_5"] . "</td></tr>";

// Template
$htmHead = "<table width=\"90%\" border=\"0\" bgcolor=\"#404040\" cellpadding=\"4\" style=\"font: 13px BaronDB; color: #FFFFFF; border: 1px solid #BBBBBB;\">";
$htmFoot = "</table>";

//Send email to owner
$txtMsg = "";
$htmMsg = $htmHead . "<tr><td></td></tr>" . $htmFoot;
$oEmail = new imEMail(($imForceSender ? $_POST["Itm_8_00_5"] : "dlowicki@ibs-ka.de"),"dlowicki@ibs-ka.de","","iso-8859-1");
$oEmail->setText($txtMsg . "\r\n\r\n" . $txtData);
$oEmail->setHTML("<html><body bgcolor=\"#404040\"><center>" . $htmMsg . "<br>" . $htmHead . $htmData . $htmFoot . "</center></body></html>");
$oEmail->send();

//Send email to user
$txtMsg = "Herzlichen Dank für Ihre  Reservierung wir \r\nmelden uns baldmöglichst bei Ihnen,\r\nnach durchsicht unseres Terminplanes.";
$htmMsg = $htmHead . "<tr><td>Herzlichen Dank für Ihre  Reservierung wir <br>melden uns baldmöglichst bei Ihnen,<br>nach durchsicht unseres Terminplanes.</td></tr>" . $htmFoot;
$oEmail = new imEMail("info@osteria-carre.de","dlowicki@ibs-ka.de","","iso-8859-1");
$oEmail->setText($txtMsg);
$oEmail->setHTML("<html><body bgcolor=\"#404040\"><center>" . $htmMsg . "</center></body></html>");
$oEmail->send();*/
//@header("Location: ../index.html");
?>
