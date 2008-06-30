<?php
session_start();
$GLOBALS['prefix'] = "../";
require('../conf/variables.php');
require 'security.inc.php';
require('../top.php');

if  (!isset($_SESSION['username'])) {
	
	echo "<h1>Access denied.</h1><br><p>Please <a href=\"index.php\">log in</a> ";
	require('bottom.php');
	exit;
}


$sql = "SELECT * FROM $playerstable WHERE Confirmation != 'Ok' AND Confirmation != '' AND Confirmation != 'Deleted' ORDER BY player_id DESC";
$result = mysql_query($sql, $db);

echo "<h1>Unconfirmed players</h1>";


while ($row = mysql_fetch_array($result)) {
	echo "<br>$row[name] | $row[mail]";
}
	
echo "<br><br>Unconfirmed: ".mysql_num_rows($result)."<br><br>";
	

echo "<br><br>Here are the e-mails, ready to be pasted into any program/list:<br><br>";
mysql_data_seek($result, 0);
while ($row = mysql_fetch_array($result)) {
	echo "$row[mail];";
}
echo "<br><br>";
require('../bottom.php');
?>
