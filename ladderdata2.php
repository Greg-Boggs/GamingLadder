<?php
$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];
require('conf/variables.php');
require('ladder_cookie.inc.php');

?>
<table id="ladder" class="tablesorter" width="100%">
<thead>
<tr>
<th bordercolor="<?echo"$color7" ?>" align="left" width=10% nowrap>No.</th>
<th bordercolor="<?echo"$color7" ?>" align="left" nowrap>Player&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="left" nowrap>Avatar&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Wins&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Losses&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Total&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Wins% &nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Rating&nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Streak&nbsp; &nbsp;</th>
</tr>
</thead>
<tbody>

<?php

$sql="SELECT * FROM $playerstable WHERE active = 1 AND games >= $gamestorank AND rating >= $ladderminelo ORDER BY rating DESC LIMIT 0 , $playersshown";
//echo $sql;
$result=mysql_query($sql,$db) or die ("failed to select players");
$cur=1;
while ($row = mysql_fetch_array($result)) {
	$namepage = "$row[name]";
	if ($row[games] != 0) {
		$percentage = $row[wins] / $row[games];
	}

if ($row[streakwins] >= $hotcoldnum) {
$picture = 'icons/streakplusplus.gif';
$streak = $row[streakwins];
}else if ($row[streaklosses] >= $hotcoldnum) {
$picture = 'icons/streakminusminus.gif';
$streak = -$row[streaklosses];
}else if ($row[streakwins] > 0) {
$picture = 'icons/streakplus.gif';
$streak = $row[streakwins];
}else if ($row[streaklosses] > 0) {
$picture = 'icons/streakminus.gif';
$streak = -$row[streaklosses];
}else{
$picture = 'icons/streaknull.gif';
$streak = 0;
}
if ($nameincookie == $namepage) {
echo '<tr class="myrow">';
} else {
?>
<tr>
<?php 
}
?>
<td ><?php echo "$cur"?></td>	
<td><a href='profile.php?name=<?php echo "$namepage '> $namepage"; ?></a> </td>
<td><?php echo "<img border='0' src='avatars/$row[Avatar].gif' alt='avatar'>"?></td>
<td><?php echo "$row[wins]" ?> </td>
<td><?php echo "$row[losses]" ?></td>
<td><?php echo ($row[losses]+$row[wins]); ?></td>
<td><?php $ny = $percentage * 100; printf("%.0f", $ny); ?></td>
<td><?php echo "$row[rating]"; ?></td>
<td><?php echo "$streak"?></td>
</tr>
<?php 
	$cur++;
}
?>

</table>
<br>
<p>
<div id="pager" class="pager">
	<form>
		<img src="lib/first.png" class="first"/>
		<img src="lib/prev.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img src="lib/next.png" class="next"/>

		<select class="pagesize">
			<option selected="selected"  value="10">10</option>

			<option value="20">20</option>
			<option value="30">30</option>
			<option  value="40">40</option>
		</select>
	</form>
</div>

<p class="copyleft">To <i>show up</i> in the ladder above you must have played >= <?php echo "$gamestorank"; ?> games, have a rating of >= <?php echo "$ladderminelo"; ?> & have played within <?php echo "$passivedays"; ?> days. Don't worry  if you haven't played for a while. All it takes is one game to become active again. Your rating doesn't decay while you are gone. 1500 is the <i>average</i> skilled player, new players will have less and vets more. Hence, don't quit playing if you rate below 1500.</p>

<?php 
$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime, 5);
echo "<br>Ladder data in $totaltime seconds.";
?>

