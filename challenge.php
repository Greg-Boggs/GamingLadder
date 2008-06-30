<?php
session_start();
// Login check & boot if not in...
require_once 'autologin.inc.php';
require_once 'logincheck.inc.php';

require 'top.php';

// If he presses the form button... 
if ($_POST["sent"]) {
    $amail = trim(strip_tags($_POST["sent"]));

    // Start with checking for empty field... theres a problem here with the checking, totaly blank field will be sent. Don tknow how to solve it...
    if (strlen($amail) <= 5) {
        echo "<h1>Oops..</h1><br>It's rude not to write something in the message. At least tell him/her when you think you should play.";
        include('bottom.php');
        exit;
    } else {
		// If there is a message we proceed with the email sending....
				
		// Get challenger info again
		$sql="SELECT *, (SELECT CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating 
                          FROM $gamestable g WHERE contested_by_loser = 0 AND withdrawn = 0 AND
                               (a.name = g.winner OR a.name = g.loser) ORDER BY reported_on DESC LIMIT 1) as rating
                FROM $playerstable a WHERE name = '".$_SESSION['username']."'";

		$result=mysql_query($sql,$db);
		$row = mysql_fetch_array($result);

		// Get challenged info that was sent after pressing the button... [challenged2]
		$sql2="SELECT *, (SELECT CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating 
                          FROM $gamestable g WHERE contested_by_loser = 0 AND withdrawn = 0 AND
                               (a.name = g.winner OR a.name = g.loser) ORDER BY reported_on DESC LIMIT 1) as rating
                 FROM $playerstable a WHERE name = '$_GET[challenged2]'";
		$result2=mysql_query($sql2,$db);
		$row2 = mysql_fetch_array($result2);


		require("class.phpmailer.php");
		$mail = new PHPMailer();
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->IsMail(); // telling the class to use SMTP
		$mail->Host = $mailhost; // SMTP server
		$mail->From = "$row[mail]";
		$mail->FromName = "$row[name]";
		$mail->AddAddress("$row2[mail]");
		$mail->Subject = "[LoW] $row[name] vs $row2[name]";
		$mail->Body = "// Ladder of Wesnoth Challenge\n
		$row[name] ($row[rating], $row[country]) wants to play a ladder game of Wesnoth with you ($row2[rating]).\n\n
		$row[name]s message:\n
		[ $amail ]\n\n\n
		Reply to this mail to answer $row[name]. Change your profile if you don't want to get more challenges.";
		//$mail->WordWrap = 500;

				if(!$mail->Send()) {
				   echo 'Message was not sent. Please contact admin with a copy of the error message if the problem reamains for more than 2 days.';
				   echo 'Mailer error: ' . $mail->ErrorInfo;
						include('bottom.php');
							exit;   
				}
				else
				{
				   echo '<br><br>The mail was sent. Please <i>don\'t send more than one</i> if he/she doesn\'t reply. It\'s also a good idea to add other players in a wesnoth group in your <a href="http://www.pidgin.im/">messenger</a> as most of them use some kind of IM.';
					include('bottom.php');
					exit;      
				}


// the following 2 are probably worthless...
			include('bottom.php');
		exit;

		// If there is a message it's time to finally send the mail...
	}
		
}
// End of what happens when a user presses submit button


// Get challenger info
$sql="SELECT * FROM $playerstable WHERE name = '".$_SESSION['username']."'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);

// Get challenged info
$sql2="SELECT * FROM $playerstable WHERE name = '$_GET[challenged]'";
$result2=mysql_query($sql2,$db);
$row2 = mysql_fetch_array($result2);
?>

<table>

<tr>
<td><h2 valign="bottom"><?echo $row['name']." (".$row['rating'].")";?>&nbsp;<img src='avatars/<?php echo "$row[Avatar].gif'";?>></h2></td>

<td><h2 valign="bottom">&nbsp;&nbsp;&nbsp;  vs. &nbsp;&nbsp;&nbsp; </h2></td>

<td><h2 valign="bottom"><img src='avatars/<?php echo "$row2[Avatar].gif'";?>> &nbsp;<?echo "$row2[name] ($row2[rating])";?></h2></td>
</tr>

</table>
<br>
<p>Send well in advance. Type 2-3 suggestion of <i>times (GMT) </i> when you both can play. Everything else will be sent automatically.</p>
<form action="challenge.php?challenged2=<?php echo $row2["name"];?>" method="post">

<textarea maxlength="1000" cols="60" rows="6" name="sent"></textarea>

<br>
<input value="Send mail" type="submit" />
</form>



<?
require('bottom.php');
?>
