<?
session_start();
$time = time();
require('conf/variables.php');
require('include/smileys.inc.php');
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
        $_SESSION['username'] = $bajs['name'];
        $_SESSION['real-username'] = $bajs['name'];
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
$news = addSmileys($news);

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
$news = addSmileys($news);

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


/* If the user has clicked on a specific news he only sees that one and we need to change the list of old news below it,
   so it shows news that are older than the news item he is viewing. So, if he reads newsitem 45, we want a list below it with
   news with a lower id than 45. That's what the following does, and we simply alter the sql query depending on if he views the
  index page or if he has clicked a specific news item...
*/
if ($_GET[readnews]) {

$query = "SELECT COUNT(*) FROM $newstable"; 
$result = mysql_query($query,$db) or die(mysql_error());
$row = mysql_fetch_array($result);
// Let's count the number of news items in the database. Rumors say this is a faster method than getrows, but I don't know.
$numindexnews2 = $row['COUNT(*)'] - $_GET[readnews];

$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT $numindexnews2, $numindexnews";
	} else {
		// This is what happens when he hasn't clicked a specific news item, not complicated at all, the variables are all in the config file.
	$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT $newsitems, $numindexnews"; }

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

require 'autologin.inc.php';
include ('sidebar.php'); 

echo"<br>";
require('bottom.php');
?>
