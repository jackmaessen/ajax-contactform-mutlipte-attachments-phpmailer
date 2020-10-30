<?php
//////////////// SETTINGS ///////////////////////
$receiver_email = 'your.email@email.com'; // fill in your own email-adrress
$receiver_name = 'John Doe'; // fill in your name
/////////////// END SETTINGS ////////////////////


error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//use PHPMailer\PHPMailer\SMTP; 
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
//require 'PHPMailer/SMTP.php';


// function check mime type attachments
function check_doc_mime( $tmpname ) {
	$finfo = finfo_open( FILEINFO_MIME_TYPE );
	$mtype = finfo_file( $finfo, $tmpname );
	finfo_close( $finfo );
	// Allowed mime types
	if( $mtype == ( "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) || 
		$mtype == ( "application/vnd.ms-excel" ) ||
		$mtype == ( "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) || 
		$mtype == ( "application/vnd.ms-powerpoint" ) ||
		$mtype == ( "application/vnd.openxmlformats-officedocument.presentationml.presentation" ) || 
		$mtype == ( "image/jpeg" ) || 
		$mtype == ( "image/png" ) || 
		//$mtype == ( "xxx/xxx" ) ||  extend your desired list by adding more fileds
		$mtype == ( "application/pdf" ) ) {
        return TRUE;
    }
    else {
        return FALSE;
    }
}

// vars
$error_msg = '';
$attachments = false;
$attachments_allowed = true;

$honeypot = $_POST["honeypot"];
$user_name = $_POST["user_name"];
$user_email = $_POST["user_email"];
$user_subject = $_POST["user_subject"];
$user_message = nl2br($_POST["user_message"]);
$user_attachment_tmp = $_FILES['user_attachment']['tmp_name'];
$user_attachment = $_FILES['user_attachment']['name'];
$user_attachment_size =  $_FILES['user_attachment']['size'];

// check if there are attachments
$count_attachments = count($user_attachment);
for ($i = 0; $i < $count_attachments; $i++) {
    if (!empty($user_attachment[$i])) {
		$attachments = true;		
    }
}

// check if allowed attachments
if( function_exists( "check_doc_mime" ) ) {	
	foreach ($user_attachment_tmp as $file) {		
		if ( !check_doc_mime( $file ) ) {
			$attachments_allowed = false;
		}			
	}
}

// check input fields
if(!empty($honeypot)) { // check spam
	exit;
}
if( empty($user_name) ) { // check empty username
	$error_msg .= '<p>Naam is a required field!</p>';
	$error = true;
}
if( empty($user_email) ) { // check empty email
	$error_msg .= '<p>Email is a required field!</p>';
	$error = true;
}
elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL) ) { // check valid email
    $error_msg .= '<p>Choose a valid email address!</p>';
	$error = true;
}
if( empty($user_message) ) { // check empty message
	$error_msg .= '<p>Message is a required field!</p>';
	$error = true;
}
if($attachments && !$attachments_allowed) { // check if allowed attachments
	$error_msg .=  '<p>1 or more attachment(s) are not allowed! Choose again</p>';
	$error = true;
}

if($error) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert"><p><b>&excl;&nbsp;Some entry fileds need attention:</b></p>'.$error_msg.'</div>'; //  error message input fields
	exit;
}
else {

	// settings phpmailer
	$mail = new PHPMailer(true);	
	//$mail->isSMTP(); 
	//$mail->SMTPDebug = 0; 
	//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->Host = 'localhost'; 
	//$mail->SMTPSecure = 'ssl'; 
	$mail->Port = 25;
	//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
	$mail->SMTPAuth = false;
	//$mail->Username = 'yourusername'; 
	//$mail->Password = 'yourpassword;	
	$mail->setFrom($user_email, $user_name); // Where is the mail coming from	
	$mail->addAddress($receiver_email, $receiver_name); // Receiver of the mail	
	$mail->Subject = $user_subject;		
	$mail->isHTML(true);	
	$mail->Body = $user_message;	
		
	if($attachments) { // check if there are attachments 
		for ($ct = 0, $ctMax = count($user_attachment_tmp); $ct < $ctMax; $ct++) {
			$uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $user_attachment[$ct]));
			$filename = $user_attachment[$ct];
			if (move_uploaded_file($user_attachment_tmp[$ct], $uploadfile)) {
				if (!$mail->addAttachment($uploadfile, $filename)) {					
					echo '<div class="alert alert-danger alert-dismissible" role="alert">Failed to attach file: '.$filename.'</div>';					
				}
				
			} else {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">Uploading one or more attachments failed! Try again</div>';				
			}
		}		
	}
	
	if (!$mail->send()) {
		echo '<div class="alert alert-danger alert-dismissible" role="alert">Error in sending the mail! Try again!</div>'; // error message send failed
	} else {
		echo '<div class="alert alert-success alert-dismissible" role="alert"><b>&check;</b>&nbsp;Your message has been sent successfully!</div>'; // success message
	}
	 
}


?>
