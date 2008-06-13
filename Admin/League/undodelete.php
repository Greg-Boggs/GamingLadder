<?php
session_start();
echo "session: ". $_SESSION['username'];
if ( isset($_SESSION['username']) ) {

if ( isset($_GET['delete']) ) {

	$game = $_GET['delete'];

	$sql= "SELECT * ".
		"FROM `$deletedgames` " .
		"WHERE `game_id` = '$game' " .
		"LIMIT 0 , 1" ;
	$result = mysql_query($sql) or die("failed to select the game");
	$row = mysql_fetch_array($result);
	$last_game = $row[0];
	echo "Undeleting game_id: $game: $row[1] vs $row[2] Go <a href='$directory/Admin/League/undodelete.php?startgames=0&finishgames=$numgamespage'> back</a>";
	
	$sql = "INSERT INTO `$gamestable` (`game_id`, `winner`, `loser`, `date`, `elo_change`) VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3] ', '$row[4]')";
	$result = mysql_query($sql) or die("failed to undo the deleted game");

	$sql = "DELETE FROM `$deletedgames` WHERE `game_id` = $game LIMIT 1;";
	$result = mysql_query($sql) or die("failed to delete the last game");

} else {

$sql="SELECT * FROM $deletedgames ORDER BY game_id DESC LIMIT $_GET[startgames], $_GET[finishgames]";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
if ($num > 0) {
$sql="SELECT * FROM $deletedgames ORDER BY game_id DESC";
$result=mysql_query($sql,$db);
$yo = mysql_num_rows($result);
$number = 0;
$link = 1;
$finishnumber = $numgamespage;
$startnext = $_GET[startgames] + $numgamespage;
$startprevious = $_GET[startgames] - $numgamespage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
echo "&nbsp;|&nbsp;<a href='$directory/Admin/League/undodelete.php?startgames=$startprevious&finishgames=$finishnumber'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
echo "&nbsp;<a href='$directory/Admin/League/undodelete.php?startgames=$number&finishgames=$finishnumber'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
$number = $number + $numgamespage;
$link = $link + 1;
}
if ($start < $yo - $numgamespage) {
echo "<a href='$directory/Admin/League/undodelete.php?startgames=$startnext&finishgames=$finishnumber'><font color='$color1'>></font></a>&nbsp;|";
}
?>
<br><br>
<table border="1" cellspacing="1" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../../icons/delete.gif' width='18' height='18' align='middle'></td>
<td bordercolor='<?echo"$color7" ?>'><p class='text'><b>Game</b></p></td>
</tr>
<?
$sql="SELECT * FROM $deletedgames ORDER BY game_id DESC LIMIT $_GET[startgames], $_GET[finishgames]";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"
<tr>
<td align='center' bordercolor='$color7'><a href='undodelete.php?delete=$row[game_id]'><font color='$color1'>Recover.</a></td>
<td bordercolor='$color7'><p class='text'>$row[winner] - $row[loser] ($row[date])</p></td>
</tr>" ?>
<?
}
?>
</table>
<br>
<?
}else{
echo"<p class='text'>No deleted games yet.</p>";
}
}
}else { 
	echo "you are not allowed here"; 
}
require('./../../bottom.php');
?>
?>

