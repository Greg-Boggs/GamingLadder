<?php
require("class.phpmailer.php");
$bajs ="eyerouge@gmail.com";
$mail = new PHPMailer();
$mail->IsSMTP(); // telling the class to use SMTP
//$mail->IsMail(); // telling the class to use SMTP
$mail->Host = "smtp1.b-one.net"; // SMTP server
$mail->From = "test@mjau.nu";
$mail->FromName = "Ladder of Wesnoth";
$mail->AddAddress("$bajs");
$mail->Subject = "[LoW] Challenge";
$mail->Body = "1
2 $bajs

3.";
$mail->CharSet = "utf-8";

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