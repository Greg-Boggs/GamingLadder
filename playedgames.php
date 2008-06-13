<?
// v 1.01
$page = "playedgames";
require('conf/variables.php');
require('top.php');
?>
<p class="header">Played games.</p>
<form method="post" action="playedgames.php?startplayed=0&finishplayed=<?echo"$numgamespage"?>">
<p class="text"><a href="playedgames.php?startplayed=0&finishplayed=<?echo"$numgamespage"?>"><font color="<?echo"$color1"?>">View all games</font></a> |
View games from: <select size="1" name="selectname" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
$sortby = "name ASC";
$sql="SELECT * FROM $playerstable ORDER BY $sortby";
$result=mysql_query($sql,$db);

while ($row = mysql_fetch_array($result)) {
if ($_POST[selectname]==$row[name]) { echo"<option selected>$row[name]</option>";
}else{ echo"<option>$row[name]</option>";
}
}
?>
</select> <input type="Submit" name="submit2" value="View." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
</form>
<?
$sql="SELECT * FROM $gamestable ORDER BY game_id DESC";
$result=mysql_query($sql,$db);
$yo = mysql_num_rows($result);
$number = 0;
$link = 1;
$finishnumber = $numgamespage;
$startnext = $_GET[startplayed] + $numgamespage;
$startprevious = $_GET[startplayed] - $numgamespage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
echo "&nbsp;|&nbsp;<a href='playedgames.php?startplayed=$startprevious&finishplayed=$finishnumber'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
echo "&nbsp;<a href='playedgames.php?startplayed=$number&finishplayed=$finishnumber'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
$number = $number + $numgamespage;
$link = $link + 1;
}
if ($startplayed < $yo - $numgamespage) {
echo "<a href='playedgames.php?startplayed=$startnext&finishplayed=$finishnumber'><font color='$color1'>></font></a>&nbsp;|";
}
if ($approvegames == "yes") {
$tablewidth = "80%";
$width = "25%";
}
else {
$width = "33%";
$tablewidth = "60%";
}
?>
<br><br>
<table width="<?echo"$tablewidth" ?>" border="1" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="0" cellpadding="2">
<tr>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Winner</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Loser</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Points</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Date</b></p></td>
<?
if ($approvegames == "yes") {
?>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Status</b></p></td>
<?
}
?>
</tr>
<tr>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>

<?php

if ($approvegames == "yes") {
?>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<?
}
?>
</tr>
<?
if ($_POST[selectname]) {
$sql="SELECT * FROM $gamestable WHERE winner = '$_POST[selectname]' OR loser = '$_POST[selectname]'  ORDER BY game_id DESC LIMIT $_GET[startplayed], $_GET[finishplayed]";
}
else {
$sql="SELECT * FROM $gamestable ORDER BY game_id DESC LIMIT $_GET[startplayed], $_GET[finishplayed]";
}
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
if ($row["recorded"] == "yes") {
$status = "recorded";
}
else {
$status = "pending";
}
?>
<tr>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>" ?></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>" ?></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$row[elo_change]" ?></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$row[date]" ?></p></td>
<?php
if ($approvegames == "yes") {
?>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$status" ?></p></td>
<?
}
?>
</tr>
<?
}
?>
</table>
<br>
<?
require('bottom.php');
?>
