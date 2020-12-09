<?php
if($_POST && isset($_FILES['datafile'])) {
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
	   
	   //Get uploaded file data
		$file_tmp_name    = $_FILES['datafile']['tmp_name'];
		$file_name        = $_FILES['datafile']['name'];
		$file_size        = $_FILES['datafile']['size'];
		$file_type        = $_FILES['datafile']['type'];
		$file_error       = $_FILES['datafile']['error'];
		
		if($file_error > 0)
		{
			die('Upload error or No files uploaded');
		}
		
		$bodyContent = <<<HTML
Recieved message from Career form. 

Name: $name
Email: $email
Mobile: $mobile
Message:
$message

HTML;
	   // set here
       $subject = "RETZ Technologies Career form submitted!";
       $to = 'info@retztechnologies.com';
	   $bcc = 'zameer@retztechnologies.com';
	   
	  //read from the uploaded file & base64_encode content for the mail
		$handle = fopen($file_tmp_name, "r");
		$content = fread($handle, $file_size);
		fclose($handle);
		$encoded_content = chunk_split(base64_encode($content));

        $boundary = md5("sanwebe");
        //header
		$headers = "Reply-To: $email\r\n"; 
       $headers .= "Return-Path: $email\r\n";
       $headers .= "From: $email\r\n"; 
	   $headers .= "Bcc: $bcc\r\n";
	   $headers .= "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
        
        //plain text 
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
        $body .= chunk_split(base64_encode($bodyContent)); 
        
        //attachment
        $body .= "--$boundary\r\n";
        $body .="Content-Type: $file_type; name=".$file_name."\r\n";
        $body .="Content-Disposition: attachment; filename=".$file_name."\r\n";
        $body .="Content-Transfer-Encoding: base64\r\n";
        $body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
        $body .= $encoded_content; 
       
       // send the email
       mail($to, $subject, $body, $headers);

       // redirect afterwords, if needed
       header('Location: thanks.html');
}   
?>