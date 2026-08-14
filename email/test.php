<?php

// Email sending is disabled to prevent SMTP connection errors.
function mail_sender($email, $title, $desc){
	/*
	include('smtp/PHPMailerAutoload.php');
	echo smtp_mailer($email, $title, $desc);
	*/
	return true;
}

function smtp_mailer($to, $subject, $msg){
	/*
	$mail = new PHPMailer(); 
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'tls'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	//$mail->SMTPDebug = 2; 
	$mail->Username = "daku.9705@gmail.com";
	$mail->Password = "jufklglaeasypxpp";
	$mail->SetFrom("daku.9705@gmail.com");
	$mail->Subject = $subject;
	$mail->Body =$msg;
	$mail->AddAddress($to);
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if(!$mail->Send()){
		echo $mail->ErrorInfo;
	}
	*/
	return true;
}
?>