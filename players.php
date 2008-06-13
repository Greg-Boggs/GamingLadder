<?

$page = "players";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>
<p class="header">Players.</p>

<!-- Form: Search By Name -->
<form method="GET"> 
	Search by Name: <input type="text" name="byname" />
	<input type="submit" value="Submit" />
</form>
<a href="players.php?startplayers=0&finishplayers=30" >Show All</a>

<?
$sql="SELECT * FROM $playerstable ORDER BY name ASC";
$result=mysql_query($sql,$db);
$yo = mysql_num_rows($result);
$number = 0;
$link = 1;
$finishnumber = $numplayerspage;
$startnext = $_GET[startplayers] + $numplayerspage;
$startprevious = $_GET[startplayers] - $numplayerspage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
echo "&nbsp;|&nbsp;<a href='players.php?startplayers=$startprevious&finishplayers=$finishnumber'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
echo "&nbsp;<a href='players.php?startplayers=$number&finishplayers=$finishnumber'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
$number = $number + $numgamespage;
$link = $link + 1;
}
if ($startplayers < $yo - $numplayerspage) {
echo "<a href='playersgames.php?startplayers=$startnext&finishplayers=$finishnumber'><font color='$color1'>></font></a>&nbsp;|";
}
?>
<br><br>
<table width="80%" border="1" bgcolor="<?php echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="0" cellpadding="2">
<tr>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Player</b></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Games</b></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Wins</b></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Losses</b></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Rating</b></p></td>
</tr>
<tr>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">&nbsp;</p></td>
</tr>
<?

// I have split the sql statement
$sql="SELECT * FROM $playerstable ";

//if byname is set than, add the where clause
if ( isset($_GET['byname']) ) {
	$sql .= " WHERE name like '%".$_GET['byname']."%' ";
}
$sql .= "ORDER BY name ASC , games ASC ";

//these two Variables had to be checked because if you search by players with the form, these two variables aren't set anymore
if ( isset($_GET[startplayers]) && isset($_GET[finishplayers]) ) { 
	$sql .= " LIMIT $_GET[startplayers], $_GET[finishplayers]";
}


$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
    if ($row["approved"] == "no") {
	$namepage = "<font color='#FF0000'>$row[name]</font>";
    } else {
	$namepage = "<font color='$color1'>$row[name]</font>";
    }

    $games = $row['games'];
    $wins = $row['wins'];
    $losses = $row ['losses'];
    $rating = $row['rating'];

?>
<tr>
<td width="20%" bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class='text'><?echo "<img src='graphics/flags/$row[country].bmp' align='absmiddle' border='1'>&nbsp;<a href='profile.php?name=$row[name]'><font color='$color1'>$namepage</font></a>"?></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$games" ?></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$wins" ?></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$losses" ?></p></td>
<td width="20%" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$rating" ?></p></td>
</tr>
<?
}
?>
</table>
<br>
<?
require('bottom.php');
?>

