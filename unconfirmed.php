<?php


require('conf/variables.php');
require('top.php');

if  ($loggedin == 0) {
	
	echo "<h1>Access denied.</h1><br><p>Please <a href=\"index.php\">log in</a> ";
	require('bottom.php');
	exit;
}


$sqlavpw="SELECT * FROM $playerstable WHERE Confirmation != 'Ok' AND Confirmation != ''  ORDER BY player_id DESC";
$resultavpw=mysql_query($sqlavpw,$db);

echo "<h1>Unconfirmed players</h1>";


while ($rowavpw = mysql_fetch_array($resultavpw)) {
	
	// Ritter is a deleted guy and I dont want to bother him, so this is a temp. solution untill we have a "deleted" flag in the user profile column...
	
	if ($rowavpw[name] != "RitterKunibert") {
		echo "<br>$rowavpw[name] | $rowavpw[mail]";
		$counter++;
	}
	
	}
	
	echo "<br><br>Unconfirmed: $counter<br><br>";
	
	$sqlavpw="SELECT * FROM $playerstable WHERE Confirmation != 'Ok' AND Confirmation != ''  ORDER BY player_id DESC";
$resultavpw=mysql_query($sqlavpw,$db);

echo "<br><br>Here are the e-mails, ready to be pasted into any program/list:<br><br>";
while ($rowavpw = mysql_fetch_array($resultavpw)) {
	
	if ($rowavpw[name] != "RitterKunibert") {
		
		echo "$rowavpw[mail];";
		
	}
	
	}
	echo "<br><br>";
require('bottom.php');
?>