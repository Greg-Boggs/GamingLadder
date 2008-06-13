<?php
include ('conf/variables.php');

$q = strtolower($_GET["q"]);
if (!$q) return;

$query = "SELECT player_id, name from $playerstable WHERE name like '$q%' ORDER BY name";
$result=mysql_query($query) or die("fail");
while ($row = mysql_fetch_array($result)) {
	echo $row['name'] . "|" . $row ['player_id'] . "\n";

}

?>
