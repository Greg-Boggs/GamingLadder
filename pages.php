<?
require('variables.php');
require('variablesdb.php');
require('top.php');

mysql_select_db($databasename,$db);
$sql="SELECT * FROM $pagestable WHERE page_id = '$_GET[number]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$content = nl2br($row["page"]);
include ('smileys.php');

echo "<p class='header'>$row[title].</p>
<p class='text'>$content</p>";

require('bottom.php');
?>

