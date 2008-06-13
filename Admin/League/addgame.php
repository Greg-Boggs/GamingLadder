<?PHP
session_start();
echo "session: ". $_SESSION['username'];
$page = "addgame";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Record a game.</p>
<?
if ($_GET[add]) {
$sql="SELECT * FROM $gamestable WHERE game_id = '$_GET[add]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$winnername=$row[winner];
$sql="SELECT * FROM $playerstable WHERE name = '$row[winner]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$ratingoldwinner = $row["rating"];
$rankoldwinner = $row["rank"];
$losername=$row[loser];
$sql="SELECT * FROM $playerstable WHERE name = '$row[losername]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$ratingoldloser = $row["rating"];
$rankoldloser = $row["rank"];
$constant = $kvalue;
$rw1 = $ratingoldwinner - $ratingoldloser;
$rw2 = -$rw1/400;
$rw3 = pow(10,$rw2);
$rw4 = $rw3 + 1;
$rw5 = 1/$rw4;
$rw6 = 1 - $rw5;
$rw7 = $constant * $rw6;
$rd=round($rw7);
$ratingnewwinner = $ratingoldwinner+$rd;
$ratingnewloser = $ratingoldloser-$rd;
if ($rankoldwinner < 1) {
$sql="SELECT * FROM $playerstable ORDER BY rank DESC LIMIT 0,1";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$row = mysql_fetch_array($result);
$rankoldwinner = $row["rank"];
$rankoldwinner++;
}
if ($rankoldloser < 1) {
$sql="SELECT * FROM $playerstable ORDER BY rank DESC LIMIT 0,1";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$row = mysql_fetch_array($result);
$rankoldloser = $row["rank"];
$rankoldloser++;
}
if ($rankoldwinner > $rankoldloser) {
$difference = $rankoldwinner - $rankoldloser;
$rise = $difference/2;
$rise = round($rise);
$ranknewwinner = $rankoldwinner - $rise;
$ranknewloser = $rankoldloser;
if ($ranknewwinner == $ranknewloser) {
$ranknewloser++;
}
$sql = "UPDATE $playerstable SET rank = rank + 1 WHERE rank >                 $ranknewwinner - 1 AND rank < $rankoldwinner";
$result = mysql_query($sql);
}else if ($rankoldwinner == $rankoldloser) {
$ranknewwinner = $rankoldwinner;
$ranknewloser = $rankoldloser + 1;
}else {
$ranknewwinner = $rankoldwinner;
$ranknewloser = $rankoldloser;
}
$sql = "UPDATE $playerstable SET wins = wins, losses= losses + 1, totalwins = totalwins, totallosses= totallosses + 1, points = points + $pointsloss, totalpoints = totalpoints + $pointsloss, games = games + 1, totalgames = totalgames + 1, streakwins = 0, streaklosses = streaklosses + 1, rating = $ratingnewloser, rank = $ranknewloser  WHERE name='$losername'";
$result = mysql_query($sql);
$sql = "UPDATE $playerstable SET wins = wins + 1, losses= losses, totalwins = totalwins + 1, totallosses= totallosses, points = points + $pointswin, totalpoints = totalpoints + $pointswin, games = games + 1, totalgames = totalgames + 1, streakwins = streakwins + 1, streaklosses = 0, rating = $ratingnewwinner, rank = $ranknewwinner  WHERE name='$winnername'";
$result = mysql_query($sql);
$sql = "UPDATE $gamestable SET recorded = 'yes' WHERE game_id = '$_GET[add]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='addgame.php'><font color='$color1'>Record another game</font></a>.</p>";
}else{
$sql="SELECT * FROM $gamestable WHERE recorded = 'no' ORDER BY game_id DESC";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
if ($num < 1) {
echo"<p class='text'>There are no games to be recorded.</p>";
}else{
?>
<table width="60%" border="1" cellspacing="0" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../../icons/add.gif' width='18' height='18' align='middle'></td>
<td bordercolor='<?echo"$color7" ?>'><p class='text'><b>Winner</b></p></td>
<td bordercolor='<?echo"$color7" ?>'><p class='text'><b>Loser</b></p></td>
<td bordercolor='<?echo"$color7" ?>'><p class='text'><b>Date</b></p></td>
</tr>
<?
}
$num = mysql_num_rows($result);
while ($row = mysql_fetch_array($result)) {
echo"
<tr>
<td align='center' bordercolor='$color7'>
<a href='addgame.php?add=$row[game_id]'><font color='$color1'>Add</a>.
</td>
<td bordercolor='$color7'><p class='text'>
$row[winner]</p></td>
<td bordercolor='$color7'><p class='text'>
$row[loser]</p></td>
<td bordercolor='$color7'><p class='text'>
$row[date]</p></td>
</tr>";
}
if ($num > 0) {
?>
</table>
<br>
<?
}
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>