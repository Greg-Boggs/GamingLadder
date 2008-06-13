<?php 

$page = "rssfeed";
$time=time();
require('variables.php');
require('variablesdb.php');
// require_once 'rss_generator.inc.php';


//	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok")


$sql="SELECT name, country FROM $playerstable ORDER BY player_id DESC LIMIT 0,11";
$result=mysql_query($sql,$db);
// $row = mysql_fetch_array($result);

while ($row = mysql_fetch_array($result)){
echo $row[name] . "<br>";
// if ($row[country] != "No country") {echo " - " . $row[country]; }
}

?>