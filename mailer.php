<?php
if($_POST) {
       // from the form
       $name = trim(strip_tags($_POST['name']));
       $email = trim(strip_tags($_POST['email']));
       $message = htmlentities($_POST['message']);
	   $mobile = trim(strip_tags($_POST['mobile']));
	   $captcha;
	   if(isset($_POST['g-recaptcha-response'])){
	   	$captcha=$_POST['g-recaptcha-response'];
	   }
	   
	   if(!$captcha){
	   	echo '<h2>Go back and Please check the captcha form.</h2>';
		
		exit;
	   }
	   $secretKey = "6LfqlDwUAAAAADJ7YH2tfE2MrBGczAKqVu9W17jw";
	   $remoteip = $_SERVER['REMOTE_ADDR'];
	   
	   $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$remoteip);
	   $responseKeys = json_decode($response,true);
	   if(intval($responseKeys["success"]) !==1){
	   	echo '<h2>Please check the Captcha and try again!</h2>';
	   }else{
	   	echo '<h2>Captcha successfully verified!</h2>';
	   }

       // set here
       $subject = "RETZ Technologies Contact form submitted!";
       $to = 'info@retztechnologies.com';
	   $bcc = 'zameer@retztechnologies.com';

       $body = <<<HTML
Recieved message from Contact Us form. 

Name: $name
Email: $email
Mobile: $mobile
Message:
$message

HTML;

	   $headers = "Reply-To: $email\r\n"; 
       $headers .= "Return-Path: $email\r\n";
       $headers .= "From: $email\r\n"; 
	   $headers .= "Bcc: $bcc\r\n";
	   //$headers .= "Organization: RETZ Technologies\r\n";
	   //$headers .= "MIME-Version: 1.0\r\n";
	   $headers .= "Content-type: text/plain;\r\n";
	   //$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
	   //$headers .= "Content-type: text/html;\r\n";
	   $headers .= "X-Priority: 3\r\n";
	   //$headers .= "X-Mailer: PHP". phpversion() ."\r\n" 

       // send the email
       mail($to, $subject, $body, $headers);

       // redirect afterwords, if needed
       header('Location: thanks.html');
}   
?>