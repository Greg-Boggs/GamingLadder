<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "report";
require('./../../conf/variables.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Report a game.</p>
<?
$date = date("M d, Y");
if ($_POST[submit]) {
$sql="SELECT * FROM $playerstable WHERE name = '$_POST[winnername]'";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$ratingoldwinner = $row["rating"];
$rankoldwinner = $row["rank"];
$sql="SELECT * FROM $playerstable WHERE name = '$_POST[losername]'";
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
}
else if ($rankoldwinner == $rankoldloser) {
$ranknewwinner = $rankoldwinner;
$ranknewloser = $rankoldloser + 1;
}
else {
$ranknewwinner = $rankoldwinner;
$ranknewloser = $rankoldloser;
}
$sql = "UPDATE $playerstable SET wins = wins, losses= losses + 1, totalwins = totalwins, totallosses= totallosses + 1, points = points + $pointsloss, totalpoints = totalpoints + $pointsloss, games = games + 1, totalgames = totalgames + 1, streakwins = 0, streaklosses = streaklosses + 1, rating = '$ratingnewloser', rank = $ranknewloser  WHERE name='$_POST[losername]'";
$result = mysql_query($sql);
$sql = "UPDATE $playerstable SET wins = wins + 1, losses= losses, totalwins = totalwins + 1, totallosses= totallosses, points = points + $pointswin, totalpoints = totalpoints + $pointswin, games = games + 1, totalgames = totalgames + 1, streakwins = streakwins + 1, streaklosses = 0, rating = '$ratingnewwinner', rank = $ranknewwinner  WHERE name='$_POST[winnername]'";
$result = mysql_query($sql);
$sql = "INSERT INTO $gamestable (winner, loser, date, recorded) VALUES ('$_POST[winnername]', '$_POST[losername]', '$date', 'yes')";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='$directory/Admin/League/report.php'><font color='$color1'>Report another game</font></a>.</p>";
}
else {
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Winner:</p></td>
<td><select size="1" name="winnername" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">>
<?php
$sql="SELECT * FROM $playerstable ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
$name = $row["name"];
echo"<option>$row[name]</option>";
}
?>
</select></td>
</tr>
<tr>
<td><p class="text">Loser:</p></td>
<td><select size="1" name="losername" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?php
$sql="SELECT * FROM $playerstable ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
$name = $row["name"];
echo"<option>$row[name]</option>";
}
?>
</select></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Report game." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>
