<?
// v 1.01
$page = "playedgames";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>

<?
$sortby = "name ASC";
$sql="SELECT * FROM $playerstable ORDER BY $sortby";
$result=mysql_query($sql,$db);

while ($row = mysql_fetch_array($result)) {
	echo"$row[name], ";
}

?>

<br>
<?
require('bottom.php');
?>