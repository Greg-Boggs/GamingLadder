<?
// v 1.03
$page = "standings";
require('conf/variables.php');
require('top.php');

require('ladder_cookie.inc.php');


// Let us now get the names of those player who excell somehow... just because its interesting
// We wil only get info thats positive, we dont want a ladder where we point out people that suck...



// Highest Positive Streak 

$sql="SELECT * FROM $playerstable WHERE games >= $gamestorank AND rating >= $ladderminelo ORDER BY streakwins DESC, games DESC,rating DESC LIMIT 0,1";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);

$higheststreaks = $row[name];
// DEB echo "<h1>$higheststreaks</h1>";


// Most Played Games
$sql="SELECT * FROM $playerstable WHERE games >= $gamestorank AND rating >= $ladderminelo ORDER BY games DESC, rating DESC LIMIT 0,1";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$mostgames = $row[name];






?>

<!-- <p class="header">Standings.</p> -->
<script type="text/javascript" language="javascript">
$(document).ready(function()
    {
        $("#ladder")
    	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    	.tablesorterPager({container: $("#pager")});
    }
);

</script>
Please be patient for the ladder to load... 
<table id="ladder" class="tablesorter" width="100%" border="0" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="2" cellpadding="2">
<thead>
<tr>
<th bordercolor="<?echo"$color7" ?>" align="left" nowrap>No.</th>
<th bordercolor="<?echo"$color7" ?>" align="left" nowrap>Avatar</th>
<th bordercolor="<?echo"$color7" ?>" align="left" nowrap>Player</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Wins</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Losses</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Total</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Wins% &nbsp; &nbsp;</th>
<th bordercolor="<?echo"$color7" ?>" align="center" nowrap>Rating</th>
<th width="1" bordercolor="<?echo"$color7" ?>" align="center" nowrap></th>
</tr>
</thead>
<tbody>

<?
if ($system == "elorating") {
$sortby = "rating DESC, games DESC";
}else if ($system == "points") {
$sortby = "points DESC";
}else if ($system == "ladder") {
$sortby = "rank ASC";
}
// How many do they have to play before they get listed?
$sql="SELECT * FROM $playerstable WHERE games >= $gamestorank AND rating >= $ladderminelo ORDER BY $sortby LIMIT 0 , $playersshown";
// , games ASC ...removed form the above... wtf did it do there?

$result=mysql_query($sql,$db);
$cur=1;
$odd = 1;
while ($row = mysql_fetch_array($result)) {



// Okey, let's convert the crap we store in the database into nice digits, putting the hour, sec, day, month and year in one variable each.
	$rawdata = $row[LastGame];
	//D echo "<h1>HÄR >> $rawdata</h1>";

	// Since the format is always identical with the identical number of characters in the time/date string we can just as
	// well use string positioning to rip out what we need. Dirty, but works.
	
	$hour = substr($rawdata, 0,2);
	$minute = substr($rawdata, 3,2);
	$day = substr($rawdata, 6,2);
	$month = substr($rawdata, 9,2);
	$year = substr($rawdata, 12,2);

	//DEB echo "<br>This is the extracted:  $hour:$minute $day/$month/$year";
	
	// To get the number of days that has passed since the latest played game we first need to convert the latest-played-game-date into unix epoch seconds. That's easy since we already have all the numbers from the database tucked into nice $variables....

	$unixedlatest = mktime($hour, $minute, 1, $month, $day, $year);
	
	// Now, we take the date today (in seconds since 70) and subtract it with the date then. The result is the difference in seconds.

	$timedifference =  (date("U") - $unixedlatest);

	// Let's convert this into days since we played...

	$daysago = ceil(($timedifference / (24*60*60))); 
	
	// DEB echo "<br>Days since last game: ". $daysago;

	// Display the number of days left before setting the player in passive mode...

	$daysleft = $passivedays - $daysago;


// Only output anything if they're not in passive mode...

if ($daysleft >= 0) {


if ($row[approved] == "no") {
$namepage = "<font color='#FF0000'>$row[name]</font>";
}
else {
$namepage = "<font color='$color1'>$row[name]</font>";
}
if ($row[games] <= 0) {
$percentage = 0.000;
}
else {
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
?>
<tr>

<?php 
// Decide the colour of this part of the table output... 

if ($odd == 1) { $cellcolor = "d7bd95"; } else { $cellcolor = "E7D9C0";}
if (($loggedin == 1) && ($row[name] == $nameincookie)) { $cellcolor = "99b377";}
if ($mostgames == $row[name]) {$cellcolor = "b0afb5"; }
if ($row[losses] == 0) {$cellcolor = "cb9850"; }
if ($higheststreaks == $row[name]) {$cellcolor = "cb6d50"; }


// $cellcolor = "cb9850" gula
// röda cb6d50  /  blåa 509bcb  / lila a09bba // grå b0afb5



// DEB echo "$higheststreaks / $row[name]<br>";

?>
<td bgcolor="#<?php echo "$cellcolor";?>" width="5%" bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class='text'><?php echo "&nbsp; <b>$cur</b>&nbsp"?></td>



<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
	else { echo "bgcolor='#E7D9C0'"; }?> width="10%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class='text'><?php 	echo "<img border='0' src='avatars/$row[Avatar].gif' alt='avatar'>"?></td>
	
	
<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="left" nowrap><p class='text'><?echo "<p>&nbsp;<a href='profile.php?name=$row[name]'>$namepage</a>&nbsp;</p>"?></td>


<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text"><?echo "$row[wins]" ?></p></td>

<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
	else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text"><?echo "$row[losses]" ?></p></td>


<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text"><?echo ($row[losses]+$row[wins]); ?></p></td>

<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
	else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text"><? $ny = $percentage * 100; printf("%.0f", $ny); ?></p></td>

<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
	else { echo "bgcolor='#E7D9C0'"; }?> width="20%" bordercolor="<?echo"$color7" ?>" align="center" nowrap><p class="text">
<?php
if ($system == "points") {
echo "$row[points]";
}
else {
echo "$row[rating]";
}
?>
</p></td>
<td <?php if (($loggedin == 1) && ($row[name] == $nameincookie)) {
	echo "bgcolor='#99b377'";	}
	else if ($odd == 1) { echo "bgcolor='#d7bd95'"; }
	else { echo "bgcolor='#E7D9C0'"; }?> width="1" bordercolor="<?echo"$color7" ?>" align="left" nowrap>
<p class='text'><?echo "<img src='$picture' alt='Streak: $streak' align='absmiddle' border='0'>"?></p></td>
</tr>



<?php
$cur++;

if ($odd == 1) {
$odd = 0;
} 
else { 
$odd = 1;
}



}
}
?>
</table>
<br>
<table><TR><TD width="50%"><IMG src="graphics/i2.gif" align="left" border="0"><b>rampage</b> - highest win streak of all members, inc. passive.</TD>

<td><IMG src="graphics/i6.gif" align="left" border="0"><b>warhorse</b> - played most games of all members, inc. passive.</td>
</TR>

<TR><TD width="50%"><IMG src="graphics/i1.gif" align="left" border="0"><b>unscathed</b> - never lost a ladder game.</TD>

<td>&nbsp;</td>
</TR>

</tbody>
</table>
<div id="pager" class="pager">
	<form>
		<img src="jquery/tablesorter/icons/first.png" class="first"/>
		<img src="jquery/tablesorter/icons/prev.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img src="jquery/tablesorter/icons/next.png" class="next"/>
		<img src="jquery/tablesorter/icons/last.png" class="last"/>
		<select class="pagesize">
			<option selected="selected"  value="10">10</option>

			<option value="20">20</option>
			<option value="30">30</option>
			<option  value="40">40</option>
		</select>
	</form>
</div>

<p class="copyleft">To <i>show up</i> in the ladder above you must have played >= <?php echo "$gamestorank"; ?> games, have a rating of >= <?php echo "$ladderminelo"; ?> & have played within <?php echo "$passivedays"; ?> days. Don't worry  if you haven't played for a while. All it takes is one game to become active again. Your rating doesn't decay while you are gone. 1500 is the <i>average</i> skilled player, new players will have less and vets more. Hence, don't quit playing if you rate below 1500.</p>

<br>

<?

require('bottom.php');
?>
