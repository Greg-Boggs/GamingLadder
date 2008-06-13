<?php
$page = "statistics";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>
<p class="header">Statistics.</p>
<form method="post">
<p class="text"><select size="1" name="stat" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
if ($_POST[stat]) {
echo"<option>$_POST[stat]</option>";
}
?>
<option>Best rating</option>
<option>Most games played</option>
<?
if ($system == "points") {
?>
<option>Most points</option>
<?
}
?>
<option>Most wins</option>
<option>Most losses</option>
<option>Best streak</option>
<option>Worst streak</option>
<option>Newcomers</option>

</select> <input type="Submit" name="submit" value="View." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
</form>
<p align="left" class="text">
<table width="40%" border="1" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="0" cellpadding="1">
<tr>
<td bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class="text"><b>
<?php
if ($_POST[stat] == "Best rating") {
$sortby = "rating DESC";
}
else if ($_POST[stat] == "Most games played") {
$sortby = "games DESC";
}
else if ($_POST[stat] == "Newcomers") {
$sortby = "player_id DESC";
}

else if ($_POST[stat] == "Most points") {
$sortby = "points DESC";
}
else if ($_POST[stat] == "Most wins") {
$sortby = "wins DESC";
}
else if ($_POST[stat] == "Most losses") {
$sortby = "losses DESC";
}
else if ($_POST[stat] == "Best streak") {
$sortby = "streakwins DESC";
}
else if ($_POST[stat] == "Worst streak") {
$sortby = "streaklosses DESC";
}else {
$_POST[stat]="Best rating";
$sortby = "rating DESC";
}
echo "&nbsp;$_POST[stat]:";
?>
</b></p>
</td>
<td bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class="text"></td>
</tr>
<tr>
<td bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class="text">&nbsp;</p></td>
   <td bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class="text">&nbsp;</p></td>
</tr>
<?

$sql="SELECT * FROM $playerstable WHERE games > 0 ORDER BY $sortby LIMIT 0,$statsnum";

if ($_POST[stat] == "Newcomers") { 
$sql="SELECT * FROM $playerstable ORDER BY $sortby LIMIT 0,$statsnum";
}
//echo $sql;
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
if ($row[approved] == "no") {
$namepage = "<font color='#FF0000'>$row[name]</font>";
}else{
$namepage = "<font color='$color1'>$row[name]</font>";
}
if ($row[games] <= 0) {
$totalpercentage = 0.000;
}
else {
$totalpercentage = $row[totalwins] / $row[games];
}
?>
<tr>
<td bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class='text'>&nbsp;<?echo "<img src='flags/$row[country].bmp' align='absmiddle' border='1'>&nbsp;<a href='profile.php?name=$row[name]'><font color='$color1'>$namepage</font></a>"?>&nbsp;</p></td>

<?php if ($_POST[stat] != "Newcomers") { ?>

<td bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text"> <?php } ?>

<?php
if ($_POST[stat] == "Best rating") {
echo "$row[rating]";
}else if ($_POST[stat] == "Most games played") {
echo "$row[games]";
}

/*
else if ($_POST[stat] == "Newcomers") {
echo "$row[name]";
}
*/


else if ($_POST[stat] == "Most points") {
echo "$row[points]";
}else if ($_POST[stat] == "Most wins") {
echo "$row[wins]";
}else if ($_POST[stat] == "Most losses") {
echo "$row[losses]";
}else if ($_POST[stat] == "Best streak") {
echo "$row[streakwins]";
}else if ($_POST[stat] == "Worst streak") {
echo "$row[streaklosses]";
}else if ($_POST[stat] != "Newcomers") 
{ echo "$row[rating]"; }

?>
<?php if ($_POST[stat] != "Newcomers") { ?>
	</p></td>
<?php } ?>

</tr>
<?
}
?>
</table>
<br>
<?
require('bottom.php');
?>

