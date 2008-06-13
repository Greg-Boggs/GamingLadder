<?PHP
session_start();
require('./../../conf/variables.php');
require('./../../top.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
//if ($number == "1") {
if (true) {
?>
<p class="header">Delete a game.</p>
<?
if ($_GET[delete]) {
$sql="SELECT * FROM $gamestable WHERE game_id = '$_GET[delete]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);

$sql = "INSERT INTO `$deletedgames` (`game_id`, `winner`, `loser`, `date`, `elo_change`) VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3] ', '$row[4]')";
$result = mysql_query($sql) or die ("failed to save game");


$sql = "DELETE FROM $gamestable WHERE game_id = '$_GET[delete]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='deletegame.php?startgames=0&finishgames=$numgamespage'><font color='$color1'>Delete another game</font></a>.</p>";
}
else {
$sql="SELECT * FROM $gamestable ORDER BY game_id DESC LIMIT $_GET[startgames], $_GET[finishgames]";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
if ($num > 0) {
$sql="SELECT * FROM $gamestable ORDER BY game_id DESC";
$result=mysql_query($sql,$db);
$yo = mysql_num_rows($result);
$number = 0;
$link = 1;
$finishnumber = $numgamespage;
$startnext = $_GET[startgames] + $numgamespage;
$startprevious = $_GET[startgames] - $numgamespage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
echo "&nbsp;|&nbsp;<a href='$directory/Admin/League/deletegame.php?startgames=$startprevious&finishgames=$finishnumber'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
echo "&nbsp;<a href='$directory/Admin/League/deletegame.php?startgames=$number&finishgames=$finishnumber'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
$number = $number + $numgamespage;
$link = $link + 1;
}
if ($start < $yo - $numgamespage) {
echo "<a href='$directory/Admin/League/deletegame.php?startgames=$startnext&finishgames=$finishnumber'><font color='$color1'>></font></a>&nbsp;|";
}
?>
<br><br>
<table border="1" cellspacing="1" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../../icons/delete.gif' width='18' height='18' align='middle'></td>
<td bordercolor='<?echo"$color7" ?>'><p class='text'><b>Game</b></p></td>
</tr>
<?
$sql="SELECT * FROM $gamestable ORDER BY game_id DESC LIMIT $_GET[startgames], $_GET[finishgames]";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"
<tr>
<td align='center' bordercolor='$color7'><a href='deletegame.php?delete=$row[game_id]'><font color='$color1'>Delete.</a></td>
<td bordercolor='$color7'><p class='text'>$row[winner] - $row[loser] ($row[date])</p></td>
</tr>" ?>
<?php
}
?>
</table>
<br>
<?php
}else{
echo"<p class='text'>No games played yet.</p>";
}
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>
