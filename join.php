<?
// v.1.05
$page = "join";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>
<p class="header">Join.</p>
<p class="text">
<?
if ($_POST[submit]) {
	$name = trim(strip_tags($_POST[name]));
	$passworddb = trim(strip_tags($_POST[passworddb]));
	$passworddb2 = trim(strip_tags($_POST[passworddb2]));
	$msn = trim(strip_tags($_POST[msn]));
	$icq = trim(strip_tags($_POST[icq]));
	$aim = trim(strip_tags($_POST[aim]));
	$mail = trim(strip_tags($_POST[mail]));
	$WesVersion = $_POST[version];
	$MsgMeToPlay = $_POST[msgme];
	
	if ($_POST[MonM] != "") {$CanPlay = "$CanPlay $_POST[MonM]";}
	if ($_POST[MonN] != "") {$CanPlay = "$CanPlay $_POST[MonN]";}
	if ($_POST[MonA] != "") {$CanPlay = "$CanPlay $_POST[MonA]";}
	if ($_POST[MonE] != "") {$CanPlay = "$CanPlay $_POST[MonE]";}
	if ($_POST[MonNi] != "") {$CanPlay = "$CanPlay $_POST[MonNi]";}
	
	if ($_POST[TueM] != "") {$CanPlay = "$CanPlay $_POST[TueM]";}
	if ($_POST[TueN] != "") {$CanPlay = "$CanPlay $_POST[TueN]";}
	if ($_POST[TueA] != "") {$CanPlay = "$CanPlay $_POST[TueA]";}
	if ($_POST[TueE] != "") {$CanPlay = "$CanPlay $_POST[TueE]";}
	if ($_POST[TueNi] != "") {$CanPlay = "$CanPlay $_POST[TueNi]";}
	
	if ($_POST[WedM] != "") {$CanPlay = "$CanPlay $_POST[WedM]";}
	if ($_POST[WedN] != "") {$CanPlay = "$CanPlay $_POST[WedN]";}
	if ($_POST[WedA] != "") {$CanPlay = "$CanPlay $_POST[WedA]";}
	if ($_POST[WedE] != "") {$CanPlay = "$CanPlay $_POST[WedE]";}
	if ($_POST[WedNi] != "") {$CanPlay = "$CanPlay $_POST[WedNi]";}
	
	if ($_POST[ThuM] != "") {$CanPlay = "$CanPlay $_POST[ThuM]";}
	if ($_POST[ThuN] != "") {$CanPlay = "$CanPlay $_POST[ThuN]";}
	if ($_POST[ThuA] != "") {$CanPlay = "$CanPlay $_POST[ThuA]";}
	if ($_POST[ThuE] != "") {$CanPlay = "$CanPlay $_POST[ThuE]";}
	if ($_POST[ThuNi] != "") {$CanPlay = "$CanPlay $_POST[ThuNi]";}
	
		
	if ($_POST[FriM] != "") {$CanPlay = "$CanPlay $_POST[FriM]";}
	if ($_POST[FriN] != "") {$CanPlay = "$CanPlay $_POST[FriN]";}
	if ($_POST[FriA] != "") {$CanPlay = "$CanPlay $_POST[FriA]";}
	if ($_POST[FriE] != "") {$CanPlay = "$CanPlay $_POST[FriE]";}
	if ($_POST[FriNi] != "") {$CanPlay = "$CanPlay $_POST[FriNi]";}
			
	if ($_POST[SatM] != "") {$CanPlay = "$CanPlay $_POST[SatM]";}
	if ($_POST[SatN] != "") {$CanPlay = "$CanPlay $_POST[SatN]";}
	if ($_POST[SatA] != "") {$CanPlay = "$CanPlay $_POST[SatA]";}
	if ($_POST[SatE] != "") {$CanPlay = "$CanPlay $_POST[SatE]";}
	if ($_POST[SatNi] != "") {$CanPlay = "$CanPlay $_POST[SatNi]";}
		
	if ($_POST[SunM] != "") {$CanPlay = "$CanPlay $_POST[SunM]";}
	if ($_POST[SunN] != "") {$CanPlay = "$CanPlay $_POST[SunN]";}
	if ($_POST[SunA] != "") {$CanPlay = "$CanPlay $_POST[SunA]";}
	if ($_POST[SunE] != "") {$CanPlay = "$CanPlay $_POST[SunE]";}
	if ($_POST[SunNi] != "") {$CanPlay = "$CanPlay $_POST[SunNi]";}		
		
	
	if ($passworddb == "") {
		echo "Please enter a password.";
	}
	
	
	else if ($passworddb != $passworddb2) {
	echo "Passwords don't match. Please retype the passwords..<br>";
	}
	
	else if ($mail == "") {
	echo "Please enter a valid email. An activation link will be sent to it.<br>";
	}
	
	else if ( $WesVersion == "") {
	echo "You must specify what version(s) of Wensoth you're using...<br>";
	}
	
	else if ($name == "") {
	echo "Please enter a nickname.";
	}
	
	else if (!preg_match("/^[a-zA-Z0-9\-\_]+$/i", $name)) {
	echo "You're only allowed to use standard aA-zZ 0-9 alfanumerical characters and the - and _ signs. <br>Please enter a valid Wesnoth multiplayer nickname.";
	}
	
	
	else {
	
	
	
	
	// Random confirmation code
$confirm_code=md5(uniqid(rand()));

// Salt for the md5...
// Lets generate the encrypted pass... we do it by applying the salt and hashing it twice.
$salt = "Reinfeldt är ett borgarsvin som förtjänar ett evigt lidande.";
$emailthispass = $passworddb;
$passworddb = $salt.$passworddb;
$passworddb = md5($passworddb); 
$passworddb = md5($passworddb); 	
	
		$length = strlen($name);
		if ($length <= 20) {
		$sql="SELECT * FROM $playerstable WHERE name = '$name'";
		$result=mysql_query($sql,$db);
		$samenick = mysql_num_rows($result);
		if ($samenick < 1) {
		if ($approve == 'yes') {
		$approved = 'no';
		}
		else {
		$approved = 'yes';
		}
		if (getenv("HTTP_X_FORWARDED_FOR"))
		{
		$ip = getenv("HTTP_X_FORWARD_FOR");
		}
		else
		{
		$ip = getenv("REMOTE_ADDR");
		}
		$sql = "INSERT INTO $playerstable (name, passworddb, mail, icq, aim, msn, country, approved, ip, avatar, HaveVersion, MsgMe, Confirmation, CanPlay) VALUES ('$name', '$passworddb', '$mail','$icq','$aim', '$msn', '$_POST[country]', '$approved', '$ip', '$_POST[avatar]', '$WesVersion', '$MsgMeToPlay', '$confirm_code', '$CanPlay')";
		$result = mysql_query($sql);
		echo "An activation mail has been sent to your mail. To activate your account <b>you must click the link</b> that is within it. <br /><br />If you have not recieved the mail within an hour <b>please check your spam box</b> or contact us.";
		
		
		
		
		
		
		// if suceesfully inserted data into database, send confirmation link to email
if($result){

// ---------------- SEND MAIL FORM ----------------

// send e-mail to ...
$to = $mail;

// Your subject
$subject = "Ladder of Wesnoth activation link";


// Your message
$body="Welcome to the Ladder of Wesnoth. This is your activation mail. \r\n";
$body.="Click the link below to activate your account: \r\n";
$body.="http://ladder.subversiva.org/confirmation.php?passkey=$confirm_code \r\n";
$body.="If it doesnt work you can try to copy & pass it into your browser instead.\r\n";
$body.="\r\n";
$body.="Your username and password is: $name / $emailthispass\r\n";
$body.="Save the info! We can't give it to you if you lose it.\r\n";
$body.="\r\n";
$body.="\r\n";
$body.="As a new player you start with a rating of 1500. That is the average rating that a player who knows the game fairly will have. Players that are new to the game are expected to get a much lower rating after a couple of games, while veterans are expected to get a higher.\r\n";
$body.="\r\n";
$body.="Dont quit if you get low rating - it is fully normal and expected while you learn the game, and Wesnoth takes quite some time to master. The rating is, first and foremost, a personal measure to track your own skills for your own sake. Play to have fun and use the ladder as a tool for information. Use it to find players that have about the same skills as you, thats when the game is most fun to play.\r\n";
$body.="\r\n";
$body.="\r\n";
$body.="Please read the FAQ & Rules before you play a ladder game. Also, feel free to contact us if you need help or have suggestions.\r\n";
$body.="\r\n";
$body.="See you in Wesnoth...\n";

// send email
// OLD way $sentmail = mail($to,$subject,$message,$header);

$sentmail = send_mail($to, $body, $subject, "eyerouge@gmail.com", "Ladder of Wesnoth");

 // $mail_sent;
 
}

// if not found
else {
echo "Your mail wasn't found in our database.";
}

// if your email succesfully sent
	if($sentmail){
		//deb echo "Your Confirmation link Has Been Sent To Your Email Address.";
	}
	else {
		echo "Failed to send activation link to your e-mail address. Contact admin if the problem remains tomorrow.";
	}
		
				}
		
		else {
		echo "The name you entered already exists.";
		}
		}
		else {
			echo "The name you entered is too long.";
		}
	}
}
else{
?>

<b>Multiple accounts are forbidden</b>. If you have problems with your account or registration please read the rules & faq. Contact us <i>after</i> that if you're problems remain. By registering you agree to all the ladder rules.

<form method="post" action="<?php echo $PHP_SELF?>">
<table border="0" cellpadding="0">
<tr>
<td><p class="text"><b>Nickname:</b></p></td>
<td>&nbsp;<input type="Text" name="name" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"> (20 char. max, <i>must</i> be same as in the game)</td>
</tr>
<tr>
<td><p class="text"><b>Password:</b></p></td>
<td>&nbsp;<input type="password" name="passworddb" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"></td>
</tr>

<tr>
<td><p class="text"><b>Re-type Password:</b></p></td>
<td>&nbsp;<input type="password" name="passworddb2" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"></td>

</tr>


<tr>
<td><p class="text"><b>Mail:</b></p></td>
<td>&nbsp;<input type="Text" name="mail" value="" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"> A verification mail will be sent.</td>
</tr>
<tr>
<td>
<p class="text">ICQ:</p></td>
<td>&nbsp;<input type="Text" name="icq" value="n/a" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td>
<p class="text">Aim:</p></td>
<td>&nbsp;<input type="Text" name="aim" value="n/a" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Msn:</p></td>
<td>&nbsp;<input type="Text" name="msn" value="n/a" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Country:</p></td>
<td>&nbsp;<select size="1" name="country" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<option>No country</option>
<?php include ("countries.inc"); ?>
</select></td>
</tr>

<tr><td><p class="text">Avatar:</p></td>
<td>&nbsp;<select size="1" name="avatar" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<?php include 'avatars.inc'; ?>
</select></td>

<tr><td><p class="text"><b>My version of Wesnoth:</b></p></td>
<td>&nbsp;<select size="1" name="version" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<option></option>
<option>Stable</option>
<option>Development</option>
<option>Both</option>
</select></td>

<tr><td><p class="text">Msg me to play:</p></td>
<td>&nbsp;<select size="1" name="msgme" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<option>Yes</option>
<option>No</option>
</select></td>
</table>



<p class="text">I can usually play these times (GMT):</p>
<table width="100%">


<tr>

<td></td>
<td>Morning</td>
<td>Noon</td>
<td>Afternoon</td>
<td>Evening</td>
<td>Night</td>
</tr>



<tr>
<td bgcolor="#E7D9C0">Monday</td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonM" value="MonM" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonN" value="MonN" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonA" value="MonA" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonE" value="MonE" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonNi" value="MonG" /></td>
</tr>



<tr>
<td>Tuesday</td>
<td><input type="checkbox" name="TueM" value="TueM" /></td>
<td><input type="checkbox" name="TueN" value="TueN" /></td>
<td><input type="checkbox" name="TueA" value="TueA" /></td>
<td><input type="checkbox" name="TueE" value="TueE" /></td>
<td><input type="checkbox" name="TueNi" value="TueG" /></td>
</tr>


<tr>
<td bgcolor="#E7D9C0">Wednesday</td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedM" value="WedM" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedN" value="WedN" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedA" value="WedA" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedE" value="WedE" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedNi" value="WedG" /></td>
</tr>

<tr>
<td>Thursday</td>
<td><input type="checkbox" name="ThuM" value="ThuM" /></td>
<td><input type="checkbox" name="ThuN" value="ThuN" /></td>
<td><input type="checkbox" name="ThuA" value="ThuA" /></td>
<td><input type="checkbox" name="ThuE" value="ThuE" /></td>
<td><input type="checkbox" name="ThuNi" value="ThuG" /></td>
</tr>

<tr>
<td bgcolor="#E7D9C0">Friday</td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriM" value="FriM" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriN" value="FriN" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriA" value="FriA" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriE" value="FriE" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriNi" value="FriG" /></td>
</tr>

<tr>
<td>Saturday</td>
<td><input type="checkbox" name="SatM" value="SatM" /></td>
<td><input type="checkbox" name="SatN" value="SatN" /></td>
<td><input type="checkbox" name="SatA" value="SatA" /></td> 
<td><input type="checkbox" name="SatE" value="SatE" /></td> 
<td><input type="checkbox" name="SatNi" value="SatG" /></td>
</tr>

<tr>
<td bgcolor="#E7D9C0">Sunday</td> 
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunM" value="SunM" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunN" value="SunN" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunA" value="SunA" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunE" value="SunE" /></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunNi" value="SunG" /></td>

</table>

<p class="text"><input type="Submit" name="submit" value="Join." style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"><br><br>
</form>
</p>
<?
}
require('bottom.php');

function send_mail($to, $body, $subject, $fromaddress, $fromname)
{
  $eol="\r\n";
  $mime_boundary=md5(time());

  # Common Headers
  $headers .= "From: ".$fromname."<".$fromaddress.">".$eol;
  $headers .= "Reply-To: ".$fromname."<".$fromaddress.">".$eol;
  $headers .= "Return-Path: ".$fromname."<".$fromaddress.">".$eol;    // these two to set reply address
  $headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters

  # Boundry for marking the split & Multitype Headers
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/mixed; boundary=\"".$mime_boundary."\"".$eol.$eol;

  # Open the first part of the mail
  $msg = "--".$mime_boundary.$eol;
 
  $htmlalt_mime_boundary = $mime_boundary."_htmlalt"; //we must define a different MIME boundary for this section
  # Setup for text OR html -
  $msg .= "Content-Type: multipart/alternative; boundary=\"".$htmlalt_mime_boundary."\"".$eol.$eol;

  # Text Version
  $msg .= "--".$htmlalt_mime_boundary.$eol;
  $msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
  $msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
  $msg .= strip_tags(str_replace("<br>", "\n", substr($body, (strpos($body, "<body>")+6)))).$eol.$eol;

  # HTML Version
  $msg .= "--".$htmlalt_mime_boundary.$eol;
  $msg .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
  $msg .= $body.$eol.$eol;

  //close the html/plain text alternate portion
  $msg .= "--".$htmlalt_mime_boundary."--".$eol.$eol;



  # Finished
  $msg .= "--".$mime_boundary."--".$eol.$eol;  // finish with two eol's for better security. see Injection.
 
  # SEND THE EMAIL
  ini_set(sendmail_from,$fromaddress);  // the INI lines are to force the From Address to be used !
  $mail_sent = mail($to, $subject, $msg, $headers);
 
  ini_restore(sendmail_from);
 
  return $mail_sent;
}

?>