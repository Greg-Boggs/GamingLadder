<?php
require('conf/variables.php');
require('top.php');
 require('ladder_cookie.inc.php');
// v 1.01
?>

<table class="tablesorter" width="100%" border="0" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="2" cellpadding="2">
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
$sql="set @num  = 0";
//echo $sql . "<br>";
$result=mysql_query($sql,$db) or die ("failed to set num");

$sql="select rank from  (
select
   @num := @num + 1 as rank, avatar, name, wins, losses, games, rating, LastGame
from $playerstable
where active = 1 and games >= $gamestorank
order by rating desc ) as A where name='$nameincookie'";
//echo $sql . "<br><br>";

$result=mysql_query($sql,$db) or die ("Failed to  select current players rank");
$row = mysql_fetch_array($result);
$my_rank = $row['rank'];
if ($my_rank == "") {
	echo "You are inactive, so I am displaying the top ten. Play a game and support the ladder!<br>";
}

$sql="set @num = 0";

//echo $sql . "<br>";
$result=mysql_query($sql,$db) or die ("failed to set num");

$sql="select * from (
select
   @num := @num + 1 as rank, Avatar, name, wins, losses, games, rating, LastGame
from $playerstable
WHERE active = 1 and games >= $gamestorank 
order by rating desc) as A 
WHERE rank > $my_rank-10 && rank < $my_rank+10";


//echo $sql . "<br>";
$result=mysql_query($sql,$db) or die ("Failed to  select ranked players");
if ($my_rank > 10) {
	//echo "works";
	$cur = $my_rank-9;
} else { $cur = 1; }
$odd = 1;
$i = 0;
while ($row = mysql_fetch_array($result)) {

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
$picture = 'images/streakplusplus.gif';
$streak = $row[streakwins];
}else if ($row[streaklosses] >= $hotcoldnum) {
$picture = 'images/streakminusminus.gif';
$streak = -$row[streaklosses];
}else if ($row[streakwins] > 0) {
$picture = 'images/streakplus.gif';
$streak = $row[streakwins];
}else if ($row[streaklosses] > 0) {
$picture = 'images/streakminus.gif';
$streak = -$row[streaklosses];
}else{
$picture = 'images/streaknull.gif';
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
</table><p>
<?php require('bottom.php');?>
>
