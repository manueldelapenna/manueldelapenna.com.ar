<?php
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
require '../PHPMailer/PHPMailerAutoload.php';

$myGmailEmail = "";
$myPassword = "";
$myName = "";

if(empty($myGmailEmail) || empty($myPassword) || empty($myName)){
	echo "Falta configurar email";
	die;
}

if($_POST) {

   $name = trim(stripslashes($_POST['contactName']));
   $email = trim(stripslashes($_POST['contactEmail']));
   $subject = trim(stripslashes($_POST['contactSubject']));
   $contact_message = trim(stripslashes($_POST['contactMessage']));

   $error = array();
   
   // Check Name
	if (strlen($name) < 2) {
		$error['name'] = "Ingrese su nombre.";
	}
	// Check Email
	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $email)) {
		$error['email'] = "Ingrese un email válido";
	}
	// Check Message
	if (strlen($contact_message) < 15) {
		$error['message'] = "Por favor ingrese un mensaje de al menos 15 caracteres";
	}
   // Subject
	if ($subject == '') { $subject = "Consulta desde sitio Web"; }

	if (!$error) {

		date_default_timezone_set('Etc/UTC');
		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $myGmailEmail;
		//Password to use for SMTP authentication
		$mail->Password = $myPassword;
		//Set who the message is to be sent from
		$mail->setFrom($myGmailEmail, 'Formulario Web');
		//Set an alternative reply-to address
		$mail->addReplyTo($email, $name);
		//Set who the message is to be sent to
		$mail->addAddress($myGmailEmail, $myName);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->isHTML(false);

		$mail->Body = $contact_message;
					
		//send the message, check for errors
		if (!$mail->send()) {
			$errorMsg = "El mensaje no pudo ser enviado, mande un email a $myGmailEmail. ";
			if($mail->SMTPDebug){
				$errorMsg.= $mail->ErrorInfo;
			}
			echo $errorMsg;
		} else {
			echo "OK";
		}
		
	} # end if - no validation error

	else {

		$response = (isset($error['name'])) ? $error['name'] . "<br /> \n" : null;
		$response .= (isset($error['email'])) ? $error['email'] . "<br /> \n" : null;
		$response .= (isset($error['message'])) ? $error['message'] . "<br />" : null;
		
		echo $response;

	} # end if - there was a validation error

}

?>