<?PHP
session_start();
$page = "deletegame";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {
?>
<p class="header">Delete a game.</p>
<?
if ($_GET[delete]) {
$sql="SELECT * FROM $gamestable WHERE game_id = '$_GET[delete]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$game_id = $row["game_id"];
$winnername = $row["winner"];
$losername = $row["loser"];
$date = $row["date"];
$sql="SELECT * FROM $playerstable WHERE name = '$winnername'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$ratingoldwinner = $row["rating"];
$sql="SELECT * FROM $playerstable WHERE name = '$losername'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$ratingoldloser = $row["rating"];
$constant = $kvalue;
$rw1 = $ratingoldwinner - $ratingoldloser;
$rw2 = -$rw1/400;
$rw3 = pow(10,$rw2);
$rw4 = $rw3 + 1;
$rw5 = 1/$rw4;
$rw6 = 1 - $rw5;
$rw7 = $constant * $rw6;
$ratingdiff = round($rw7);
$sql = "UPDATE $playerstable SET wins = wins, losses= losses - 1, totalwins = totalwins, totallosses = totallosses - 1, points = points - $pointsloss, totalpoints = totalpoints - $pointsloss, games = games - 1, totalgames = totalgames - 1, streakwins = 0, streaklosses = streaklosses - 1, rating = rating + $ratingdiff WHERE name='$losername'";
$result = mysql_query($sql);
$sql = "UPDATE $playerstable SET wins = wins - 1, losses= losses, totalwins = totalwins - 1, totallosses= totallosses, points = points - $pointswin, totalpoints = totalpoints - $pointswin, games = games - 1, totalgames = totalgames - 1, streakwins = streakwins - 1, streaklosses = 0, rating = rating - $ratingdiff WHERE name='$winnername'";
$result = mysql_query($sql);
$sql = "DELETE FROM $gamestable WHERE winner = '$winnername' AND loser = '$losername' AND date = '$date' AND game_id = '$_GET[delete]'";
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
<?
}
?>
</table>
<br>
<?
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