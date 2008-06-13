<?
// Version 1.13
$page = "index";
$time=time();
require('conf/variables.php');
?>
<?php
if ($_POST[user] AND $_POST[pass]) {

	$fname = $_POST[user];
	$fpass = $_POST[pass];
	
	
// Lets hinder players who havent verified their mail from loging in / creating logged in cookies...
//eye Check if the player has verified his email by clicking the activation link...

$sql="SELECT * FROM $playerstable WHERE name = '$fname'";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok") {
		require('top.php');
		echo "<b>You cant login because you have not activated your account.</b><br /><br />When you registered a mail was sent to you containing a unique <i>activation link</i>. Please find that mail and click the activation link. Dont forget to check your spam box, as some services wrongly flag our activation mail as spam. <br><br>Feel free to contact us if you are sure that you have missplaced your activation email.";
		require('bottom.php');
		exit;		
		}

// Okey, we have no check that the user is verified and can let him in...
// Lets generate the encrypted pass, after all, its the one thats stored in the database... we do it by applying the salt and hashing it twice.
// We need to take the users real pass, "encrypt" it the same way we did when he registered, and then compare the results.
// The salt is read from the config file.

$passworddb = $salt.$fpass;
$passworddb = md5($passworddb); 
$passworddb = md5($passworddb); 

	

	//echo "<br>Form: $fname / $fpass";
	$sql = "SELECT * FROM $playerstable WHERE name='$fname' AND passworddb='$passworddb'";
	$result = mysql_query($sql,$db);
	$bajs = mysql_fetch_array($result); 
	//echo "<br>Test 2";
	 if ("$bajs[player_id]" > 0) {
		
		// Set cookies... 776000 sec = 3 months before they expire.
		  setcookie ("LadderofWesnoth1", $bajs[name], $time+7776000); 
		  setcookie ("LadderofWesnoth2", $bajs[passworddb], $time+7776000); 
		header("Location: index.php");
		//echo $sql;
		exit;
		
		//header("Location: http://www.example.com/");

		//DEB print_r($_COOKIE);
		//DEB echo "<div align='right'>// $bajs[name]</div>";

	 } else {
				$login_error= true; 
				// Show error msg if the login failed...
			require('top.php');
			echo "<h1>Login Failed.</h1><br><p>Please make sure that you're registered and that you typed in the correct username/password. If you've checked everything 5 times and the problem still remains please contact us and we'll assist you.</p>";
			require('bottom.php');
			
			exit;
				
				}
}

require('top.php');

?> 


<link rel="stylesheet" type="text/css" href="style.css" />

<br />
<table border=0 width="100%" style="smallinfo">
	<tr>
	
	
	<td width="70%" valign="top" padding-right="15px">

<?php

if ($_GET[readnews]) {
$sql="SELECT * FROM $newstable WHERE news_id = '$_GET[readnews]' ORDER BY news_id DESC LIMIT 0, $newsitems";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$news = nl2br($row["news"]);
include ('smileys.php');

print("
<p class=header>$row[title]</p>
<p class=text>$news</p>
<hr size=1 color=$color1><br>
");

}else{
$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT 0, $newsitems";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
$news = nl2br($row["news"]);
include ('smileys.php');

print("
<p class=header>$row[title]</p>
<p class=text>$news</p>
<br>
<hr size=1 color=$color1><br>
");
}

}
print("
<p class=header>Other news articles:</p>
<p class=text>
");

$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT $newsitems, $numindexnews";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"<a href='index.php?readnews=$row[news_id]'><font color='$color1'>$row[date] - $row[title]</font></a><br>";
}
?>
	
	</td>
	
	<td>&nbsp;&nbsp;&nbsp;</td>
	
			<td width="30%" valign="top" class="smallinfo">	



<?php 

// Lets check to see if there are Ladder cookies to see if the user is logged in. If so, we wont show the login box....


// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.

	if (isset($_COOKIE["LadderofWesnoth1"]) AND isset($_COOKIE["LadderofWesnoth2"])) {
		//DEB echo "2 Cookies are set..."; 
		
			
		$nameincookie = $_COOKIE["LadderofWesnoth1"];
		$passincookie =  $_COOKIE["LadderofWesnoth2"];
		
		// We hash it again to avoid getting the password broken by Rainbow tables...
		

	
		// Now lets compare the cookies with the database. If there is a playername and pass that corresponds, he's logged in...
		$sql = "SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb='$passincookie'";
		$result = mysql_query($sql,$db);
		$bajs = mysql_fetch_array($result); 
		
		//DEB echo "<div align='right'>Vnameincookie: $nameincookie</div>";
		//DEB echo "bajsplayerid: $bajs[name] $bajs[player_id]";
		
			if ($bajs[player_id] > 0) { 
				echo "<div align='right'>//<a href=\"profile.php?name=$bajs[name]\">$nameincookie</a></div>";
				$loggedin = 1;
			} else { $loggedin = 0; }
		}

?>

<?php
// We dont want to show the login form if we're logged in alread, so:
If ($loggedin == 0) {
?>


	<form action=index.php method=post> 
	<input type=text name=user size=15>
	<input type=password name=pass size=15>
	<input type=submit value=Login>
	</form>

<?php } 
?>
  <div class="border_left"><div class="border_right"><div class="border_bottom">
  <div class="corner_bottomleft"><div class="corner_bottomright">
  <div class="border_top"><div class="corner_topleft"><div class="corner_topright">

<div class="sidebar">
<?php


// Lets erase all waiting players who are no longer waiting...

$sql="SELECT * FROM $waitingtable ORDER BY id DESC";
$result=mysql_query($sql,$db);

while ($row = mysql_fetch_array($result)) {

	// Set the time they wanted to search for a game...

	$inactive = time()-(60*60*$row[time]);
	// DEB echo "inactive: $inactive<br>";	

	// Delete the entry if the time has passed...

	if ($row[entered] < $inactive) {
	
		$sql3="DELETE FROM $waitingtable WHERE username = '$row[username]'";
		$result3=mysql_query($sql3,$db);
	}
	


}

//$db->query ("DELETE FROM online WHERE lastactive < $inactive");



$sql="SELECT * FROM $waitingtable ORDER BY id ASC";
$result=mysql_query($sql,$db);


// If nobody at all is looking for a game at this moment we want a special teazer pic to show up...

if ((mysql_num_rows($result)==0) && ($loggedin == 1)) {

echo "<div align='left'><a href='playnow.php'><img border='0' src='graphics/waiting.gif'></a></div><br />";

// If people were in the list we dont display the cock-teazing picture.. instead we show the names and causal links

} elseif (mysql_num_rows($result)!=0) {
	echo "<b>Looking for a game</b><ol>";
	
	while ($row = mysql_fetch_array($result)) {
	
		$timeleft = $row[entered]-(time()-(60*60*$row[time]));
	
	
	TimeConvert("$timeleft");
	
	
		print("<li><a href=\"profile.php?name=$row[username]\">$row[username]</a> ($row[rating])<br> $beenconverted - $row[meetingplace]</li>
		");
	}
	echo "</ol><br />";
	
	// Let's display proper edit / del links if the user is in the waiting list and then show them below it..,..
	
	$sql = "SELECT id FROM $waitingtable WHERE username = '$nameincookie'";
	$intable = mysql_query($sql);
	
		if (mysql_num_rows($intable)!=0) {
		echo "<div align='right'><a href='playnow.php'>edit</a> | <a href='playnow.php?del=$nameincookie'>del</a></div><br>";
		
		} else {
		
		if ($loggedin == 1) {
			echo "<div align='right'><a href='playnow.php'>add me </a></div>";
			}
		}
		
}
	
	
	
// Show latest played games:	
	
	$sql ="SELECT winner, loser, date FROM $gamestable ORDER BY game_id DESC LIMIT 0,$numindexresults";
	$result = mysql_query($sql,$db);
	//$bajs = mysql_fetch_array($result); 
	

	echo "<b>Latest results</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show latest joined and verified players...

	
	// $sql ="SELECT name FROM $playerstable ORDER BY player_id DESC";
	$sql ="SELECT name FROM $playerstable WHERE Confirmation = 'Ok' ORDER BY player_id DESC LIMIT 0,$numindexnewbs";
	$result = mysql_query($sql,$db);

	echo "<br><b>Newcomers</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show the top x players
	
	echo "<br /><br /><b>Top $numindexhiscore players</b><ol>";
	

	// Only get the payers that show in the ladder.... its defined by the minimum amount of games they have to played and a minimal rating

	$sql="SELECT * FROM $playerstable WHERE games >= $gamestorank AND rating >= $ladderminelo and active = 1 ORDER BY rating DESC, games DESC  LIMIT 0,$numindexhiscore";
	//old $sql ="SELECT * FROM $playerstable ORDER BY rating DESC, totalgames DESC";
	$result = mysql_query($sql,$db);
	

	while ($row = mysql_fetch_array($result)) {
		echo "<li><a href=\"profile.php?name=$row[name]\">$row[name]</a> ($row[rating])</li>"; 
	}
	echo "</ol>";
	




echo "</ol><br><div align='left'><a href='friends.php'><img border='0' src='graphics/friendslist.jpg'></a></div><br />";

echo "</ol><br><div align='left'><a href='http://chaosrealm.net/wesnoth/index.php?readnews=-1'><img border='0' src='graphics/mod.jpg'></a></div><br />";
	
	$sql=mysql_query("SELECT * FROM $playerstable");
	$number=mysql_num_rows($sql);
	echo "<br /><br /><b>Players:</b> $number";

	$sql=mysql_query("SELECT * FROM  $gamestable");
	$number=mysql_num_rows($sql);
	echo "<br /><b>Games:</b> $number";
	
	
// Show x  deleted games...

	
	$sql ="SELECT winner, loser, date, elo_change FROM $deletedgames ORDER BY game_id DESC LIMIT 0,$numindexdeled";
	$result = mysql_query($sql,$db);

	echo "<br /><br><b>Deleted reports</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a><br>$bajs[date] / $bajs[elo_change] p.</li>";
	}
	echo "</ol>";

	
	If ($loggedin == 1)  {
		echo "<br><br><a href='logout.php'>Log out</a>";	
	}
	
	?>


</td>	
	
	

	
	
	
	


	</tr>
</table>
</div>
 </div></div></div></div></div></div></div></div>
<hr>

<?php
echo"<br>";
require('bottom.php');


function TimeConvert($ToConvert) {
global $beenconverted;

$min = floor($ToConvert/60);
$h = floor($min/60);
$min2 = $min;

if ($h >= 1) {$min2 = ($min - ($h * 60));}

if ($h >= 1) {
$beenconverted = $h."h ".$min2."min"; 
} else {
$beenconverted = $min2."min"; 
}

}
?>
