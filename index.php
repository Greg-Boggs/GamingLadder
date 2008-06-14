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

<?php include ('sidebar.php'); ?>


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
