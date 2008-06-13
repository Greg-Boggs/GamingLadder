<?php
require("class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP(); // telling the class to use SMTP
$mail-> SMTPAuth = True;
$mail->Host = "smtp.gmail.com"; // SMTP server
$mail->From = "spam@eyerouge.com";
$mail->FromName = "Ladder of Wesnoth";
$mail->AddAddress("eyerouge@gmail.com");

$mail->Subject = "First PHPMailer Message";
$mail->Body = "Hi! \n\n This is my first e-mail sent through PHPMailer.";

$mail->Port = 587;
$mail->Password = "shakaree";
$mail->Usernae = "persika@gmail.com";

$mail->WordWrap = 50;

if(!$mail->Send())
{
   echo 'Message was not sent.';
   echo 'Mailer error: ' . $mail->ErrorInfo;
}
else
{
   echo 'Message has been sent.';
}
?>