<?php
session_start();
// Playnow  v. 1.01
require 'autologin.inc.php';
require('logincheck.inc.php');

require 'top.php';
/* In the HTML we'll use a drop down with server options. They're based in the users settings;
   If he has only the developers version or only the stable version of the game, then they'll be the only
   ones to appear. If he has both versions, a drop down will appear with both. 
*/

if (($bajs[HaveVersion] == "Both") || ($bajs[HaveVersion] == "")) {$dropdown = "<option selected>Instant Message</option><option>Development Server</option><option>Stable Server</option>";}

if ($bajs[HaveVersion] == "Development") {$dropdown = "<option selected>Instant Message</option><option>Development Server</option>";}

if ($bajs[HaveVersion] == "Stable") {$dropdown = "<option selected>Instant Message</option><option>Stable Server</option>";}

// get the rating of the player...
$Rating = $bajs[rating];
// DEB echo "<h1>$Rating</h1>";


// See if the player uses any Instant Messangers...

if ((($bajs[Jabber] == "") || ($bajs[Jabber] == NULL) || ($bajs[Jabber] == "n/a")) && (($bajs[msn] == "") || ($bajs[msn] == NULL) || ($bajs[msn] == "n/a")) && (($bajs[aim] == "") || ($bajs[aim] == NULL) || ($bajs[aim] == "n/a")) && (($bajs[icq] == "") || ($bajs[icq] == NULL) || ($bajs[icq] == "n/a")) ) { $HaveNoIM = TRUE; }


// Check if the player has checked "dont contact me"...
if ($bajs[MsgMe] != "Yes") {$dontmsg = TRUE;}


// This happens when the page loads and the submit button has been pushed..
if ($_POST['wait']) {


// Check if the player selected the IM option in the dropdwon menu AND if he at the same time lacks an IM
If (($_POST[server] == "Instant Message" ) && ($HaveNoIM == TRUE)) {

	echo "<h1>Whoops...</h1><br>You selected the \"Instant Message\" option as the way to find you. <br>There is no Instant Messenger info in your profile.<br><br>Please <a href=\"edit.php?name=$bajs[name]\">edit your profile</a> and give us the information if you want to be contacted via IM.";
	require('bottom.php');
	exit;
}

// Check if the player selected the IM option in the dropdwon menu AND if he at the same has stated that he doesnt want to be contacted...
If (($_POST[server] == "Instant Message" ) && ($dontmsg == TRUE)) {

	echo "<h1>Whoops...</h1><br>You selected the \"Instant Message\" option as the way to find you. <br>In your profile you tell people not to contact you.<br><br>Please <a href=\"edit.php?name=$bajs[name]\">edit your profile</a> and allow people to contact you if you want to be contacted via IM.";
	require('bottom.php');
	exit;
}






// Set what meeting info that should go into the database later on...
if ($_POST["server"] == "Instant Message") {$MeetingPlace = "im"; }
if ($_POST["server"] == "Development Server") {$MeetingPlace = "dev"; }
if ($_POST["server"] == "Stable Server") {$MeetingPlace = "sta"; }



// Set the time when the player eneterd himself in the waiting for a game-list...
$lastactive = time();
	
	
// Check if visitor is already in the table
// ("SELECT onlineid FROM online WHERE ipaddress = '$ipaddress'");
$sql = "SELECT id FROM $waitingtable WHERE username = '".$_SESSION['username']."'";
$intable = mysql_query($sql);

// $intable = mysql_num_rows($sql);


// if in table the update the user... else add him...

	if (mysql_num_rows($intable)==0) {
	
					
	// "INSERT INTO online (ipaddress, lastactive) VALUES ('$ipaddress', $lastactive)"
		$sql = "INSERT INTO $waitingtable (username, time, entered, meetingplace, rating) VALUES ('".$_SESSION['username']."', '$_POST[hours]', '$lastactive', '$MeetingPlace', '$Rating')";
		$result = mysql_query($sql);

			// if suceesfully inserted data into database....
			if($result){
			echo "<h1>added new entry</h1><br /><a href='$wesnothdir'>back to index >></a>";
			require('bottom.php');
			exit;

			
			} else {
				echo "mysql error: cant add new enrty - contact admin if error remains for more than 1 day.";
			}


		} else {
// DEB echo "starting to updated entry...<br>";
		// "UPDATE online SET lastactive = $lastactive WHERE ipaddress = '$ipaddress'");

	$sql = "UPDATE $waitingtable SET time =  '$_POST[hours]', entered = '$lastactive', meetingplace = '$MeetingPlace', rating = '$Rating' WHERE username = '".$_SESSION['username']."'";
		$result = mysql_query($sql);
			
			if($result){
			echo "<h1>Updated entry</h1><br><a href='$wesnothdir'>back to index >></a>";
			require('bottom.php');
			exit;

			
			} else {
				echo "mysql error: cant update... contat admin if the error remains more than 1 day.";
			}
			
	}
}
?>

<?php 

// If the user is logged in and wants to delete himself from the list...

if ($_GET['del'] == $_SESSION['username']) {

	$sql3="DELETE FROM $waitingtable WHERE username = '".$_SESSION['username']."'";
	$result3=mysql_query($sql3,$db);

	if ($result3) {

		echo "<h1>Removed ".$_SESSION['username']." from list...</h1><br /><a href='$wesnothdir'>back to index >></a>";
			require('bottom.php');
			exit;
	}
	echo "Database frakked up error: Contact admin if problem remans for more then a day...";
	exit;
 	require('bottom.php');
}

?>


<br />
<table border=0 width="100%" style="smallinfo">
	<tr>
	
	
	<td width="70%" valign="top" padding-right="15px">


If you are looking for a <i>ladder game</i> you can find opponents by e-mail challenging them via their profile, their instant messanger, using our <a href="http://chaosrealm.net/wesnoth/friends.php">friends list</a> and checking out the online lobby or by putting yourself on the "I want to play now"-list below.<br /><br />Please estimate for how many hours you'll be looking for a game. Also tell us if you are already waiting on a server with the same nickname or if you want to be contacted via instant messanger. Once your set time runs out you'll be auto-removed from the list. Don't forget to remove yourself if something else shows up. Abuse of this function will get you a ban.<br /><br />
<form method="post">
<select size="1" name="hours" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"><option selected>2</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option>
</select>h is how long I'll be available for a game.
<br /><br />

<select size="1" name="server" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<?php //lets display the proper dropdown menu..
echo "$dropdown"; ?>

</select> is where you'll find and contact me.<br><br>

<input type="Submit" name="wait" value="add me to the list" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?php require('bottom.php'); ?>
