<?php

include ("logincheck.inc.php");


$sql="set @num  = 0";
$result=mysql_query($sql,$db) or die ("failed to set num");

$sql="select rank, Avatar, Avatar, country, mail, aim, msn, Titles, Joined, name, wins, losses, games, rating, LastGame, MsgMe, CanPlay, Approved, active from  (
select
   @num := @num + 1 as rank, Avatar, country, mail, aim, msn, Titles, Joined, name, wins, losses, games, rating, LastGame, MsgMe, CanPlay, Approved, active
from $playerstable
order by rating desc ) as A where name='$_GET[name]'";
//echo $sql;
$result=mysql_query($sql,$db) or die ("Failed to  select current player information");
$row = mysql_fetch_array($result);
$rank = $row['rank'];

$rawdata = $row[LastGame];
//echo $rawdata ."<br>";

// Since the format is always identical with the identical number of characters in the time/date string we can just as well use string positioning to rip out what we need. Dirty, but works.
$hour = substr($rawdata, 0,2);
$minute = substr($rawdata, 3,2);
$day = substr($rawdata, 6,2);
$month = substr($rawdata, 9,2);
$year = substr($rawdata, 12,2);
	
// To get the number of days that has passed since the latest played game we first need to convert the latest-played-game-date into unix epoch seconds. That's easy since we already have all the numbers from the database tucked into nice $variables....
$unixedlatest = mktime($hour, $minute, 1, $month, $day, $year);
	 echo "<br>This is the extracted:  $hour:$minute $day/$month/$year";
// Now, we take the date today (in seconds since 70) and subtract it with the date then. The result is the difference in seconds.
$timedifference =  (date("U") - $unixedlatest);

// Let's convert this into days since we played...
$daysago = ceil(($timedifference / (24*60*60))); 
//echo $daysago;
$daysleft = $passivedays - $daysago;

// Show us the date when he enters passive mode...
$passivedate = ($unixedlatest + ($passivedays*24*60*60));
$passivedate = date("j M", $passivedate);


if ($row != "") {
if ($row["approved"] == "no") {
$blocked = "(<font color='#FF0000'>blocked or not added yet</font>)";
}else{
$blocked = "";
}

$avan = $row["Avatar"];

if ($row["mail"] == "n/a") {
$mailaddress = "n/a";
$mailpic = "";
}else{


if ($row['Joined'] != NULL) { 
	$joined = date("H:i d m y", $row['Joined']); 
} else {
	$joined ="00:00 06 03 08"; 
}

// Read the mail from the db and make it spambotsafe...
$mailaddress = $row[mail];
$mailaddress = str_replace("@", " (at) ", $mailaddress);
$mailaddress = str_replace(".", " (dot) ", $mailaddress);
$jabbername = $row[Jabber];
$jabbername = str_replace("@", " (at) ", $jabbername);
$jabbername = str_replace(".", " (dot) ", $jabbername);
$jabberpic = "<img border='1' src='images/jabber.gif' align='absmiddle'>";

$mailpic = "<img border='1' src='images/mail.gif' align='absmiddle'></a>";
}
if ($row['icq'] == "n/a") {
$icqnumber = "n/a";
$icqpic = "";
}else{
$icqnumber = $row['icq'];
$icqpic = "<img border='1' src='images/icq.gif' align='absmiddle'>";
}
if ($row['aim'] == "n/a") {
$aimname = "n/a";
$aimpic = "";
}else{
$aimname = $row['aim'];
$aimpic = "<img border='1' src='images/aim.gif' align='absmiddle'>";
}
if ($row['msn'] == "n/a") {
$msnname = "n/a";
$msnpic = "";
}else{

$msnname = $row['msn'];
$msnname = str_replace("@", " (at) ", $msnname);
$msnname = str_replace(".", " (dot) ", $msnname);


$msnpic = "<img border='1' src='images/msn.gif' align='absmiddle'></a>";
}

if ($row['games'] <= 0) {
$percentage = 0.000;
}else {
$percentage = $row['wins'] / $row['games'];
}


if ($row['games'] == 0) {
$totalpercentage = 0.000;
}else {
$totalpercentage = $row['wins'] / $row['games'];
}
if ($row[streakwins] >= 1) {
$streak = "+$row[streakwins]";
}
else if($row[streaklosses] >= 1) {
$streak = "-$row[streaklosses]";
}
else {
$streak = 0;
}
?>
<table width="100%" cellpadding="1px">
<tr>
<td valign="top">
<h1><?php echo "$row[name] $blocked" ?></h1>
<?php 
// Show the players title if he has one...
if ( $row["Titles"]  != "" ) {
	echo "<b>" . $row["Titles"] . "</b><br>";
}

if ( $row["LastGame"]  != "" ) { 
    echo $row["LastGame"] . " p. ";

    if (($daysleft >= 0)) {
	echo "($daysleft days left)";
    }
} 
?>
</td>
<td valign="top">
<img src='avatars/<? echo "$row[Avatar].gif'>"; ?>
<?php 
If ($row['country'] != "No country") { 
	echo "<br/> <p class='text'><img src='flags/$row[country].bmp' align='absmiddle' border='1'> $row[country] </p>"; 
} ?>
</td>
</tr>
</table>


<p class="text">
<table width="100%">
<tr>
<td><b>Rank</b></td>
<td><b>Rating</b></td>
<td><b>Percent</b></td>
<td><b>Wins</b></td>
<td><b>Losses</b></td>
<td><b>Played</b></td>
<td><b>Average P WLT</b></td>
<td><b>Streak</b></td>
</tr>
<tr>
<td>
<?php

// we need some info to get to know how many points the player wins in average WHEN he wins, and the same about when he loses...

$sqlavpl="SELECT elo_change FROM $gamestable WHERE loser = '$_GET[name]'  ORDER BY game_id DESC";
$resultavpl=mysql_query($sqlavpl,$db);

while ($rowavpl = mysql_fetch_array($resultavpl)) {

	if ($rowavpl[elo_change] !="") {
	$avpl = $avpl + $rowavpl[elo_change]; 
	$numlosses++;

	}
}

if ($numlosses != 0) {
	$avpl = "-". round(($avpl / $numlosses),0);
}


$sqlavpw="SELECT elo_change FROM $gamestable WHERE winner = '$_GET[name]'  ORDER BY game_id DESC";
$resultavpw=mysql_query($sqlavpw,$db);


while ($rowavpw = mysql_fetch_array($resultavpw)) {
	if ($rowavpw[elo_change] !="") {
	$avpw = $avpw + $rowavpw[elo_change]; 
	$numwins++;
	}
}
if ($numwins != 0) 
	$avpw = round(($avpw / $numwins),0);
else
	$avpw = 0;
//while ($row = mysql_fetch_array($result)) 

// get the players averahe points / game...
if ($row[games] > 0)
	$avep = round((($row[rating] - 1500)/$row[games]),2);
else
$avep = 0;

if ($row[games] < $gamestorank) {
echo "unranked"; }
else {


// Get to know how many points the player gets in an average game... this will also say something about him as a player choosing his opponents.



if ($daysleft >= 0) {echo $rank;} else {echo "<a href=\"faq.php#passive\">(passive)</a>";}  }?></td>
<td><?
echo round($row[rating],0);
if (($row[games] >= $gamestorank) && ($daysleft >= 0)) { echo " ($classrating)"; } ?></td>

<td><?echo round(($totalpercentage * 100),0); ?>%</td>
<td><?echo "$row[wins]" ?></td>
<td><?echo "$row[losses]" ?></td>
<td><?echo "$row[games]" ?></td>
<td><?echo "$avpw / $avpl / $avep" ?></td>
<td><?echo "$streak" ?></td>

</tr>
</table>
</p>
<br><br>
<table width="100%" bgcolor="#E7D9C0"><tr><td>
		<?php 
		If ($row[MsgMe] == "Yes") {echo "<b><font color=\"#0D3D02\">Contact me to play!</font></b>";}
		else {echo "<font color=\"#9E005D\">Please don't message me asking for a game.</font>";}
		
		
				if ($loggedin == 1 && $row[MsgMe] == "Yes"){
			echo "  <a href=\"challenge.php?challenger=$nameincookie&challenged=$_GET[name]\">[Challenge]</a>";
						
		}

		
		?>
		</td>
		</tr>
		</table>
<br>

<?php // Only show contact info if the user wants to be contacted 
if ($row[MsgMe] == "Yes") {
	?>
	<table>
	
	<?php
	if ( $mailaddress != "n/a" && $mailaddress != "") { 
	?>
	
	<tr><td nowrap><p class="text">Mail:</p></td><td nowrap><p class="text"><?php echo "$mailpic $mailaddress"; ?></p></td></tr>
	
	
	<?php } ?>
	
	

	
	
	<?php if ($icqnumber != "n/a" && $icqnumber != "") { ?>
	<tr>
		<td nowrap><p class="text">Icq:</p></td>
		<td nowrap><p class="text"><?echo "$icqpic $icqnumber"?></p></td>
		</tr>
	<?php } ?>	
	
	

	

	
	<?php if ($aimname != "n/a" && $aimname != "") { ?>
	
		<tr>
		<td nowrap><p class="text">Aim:</p></td>
		<td nowrap><p class="text"><?echo "$aimpic $aimname" ?></p></td>
		</tr>
		
	<?php } ?>	
	
	
	
	<?php if ($msnname != "n/a" && $msnname != "") { ?>
	
		<tr>
		<td nowrap><p class="text">Msn:</p></td>
		<td nowrap><p class="text"><?echo "$msnpic $msnname" ?></p></td>
		</tr>
	
	<?php } ?>	
	
	
	<?php if ($jabbername != "n/a" && $jabbername != "") { ?>
	
		<tr>
		<td nowrap><p class="text">Jabber:</p></td>
		<td nowrap><p class="text"><?echo "$jabberpic $jabbername" ?></p></td>
		</tr>
	
	<?php } ?>	
	
				
</table>



<?php  
/*
$pos1 = strpos("$row[CanPlay]", "MonA");
if ($pos1 != FALSE) {echo "<br>Can play Monday Afternoon...";}

$pos1 = strpos("$row[CanPlay]", "MonE");
if ($pos1 != FALSE) {echo "<br>Can play Monday Evening...";}

$pos1 = strpos("$row[CanPlay]", "MonNi");
if ($pos1 != FALSE) {echo "<br>Can play Monday Night...";}
*/
?>



<?php 

if ($row[CanPlay] != "") { ?>
	
	<p class="text">Uses <?echo "$row[HaveVersion]" ?> version of Wesnoth & can usually play (GMT):</p>
	<table width="100%">
	
	
	<tr>
	
	<td></td>
	<td>Morning</td>
	<td>Noon</td>
	<td>Afternoon</td>
	<td>Evening</td>
	<td>Night</td>
	</tr>
	
	
	
	<tr>
		<td bgcolor="#E7D9C0">Monday</td>
		
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "MonM");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
		
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "MonN");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "MonA");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "MonE");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "MonG");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	</tr>
	
	<tr>
		<td>Tuesday</td>
	
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "TueM");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
		
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "TueN");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "TueA");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "TueE");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
		
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "TueG");
		if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	</tr>
	
	
	<tr>
	<td bgcolor="#E7D9C0">Wednesday</td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "WedM");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "WedN");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "WedA");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "WedE");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "WedG");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	</tr>
	
	<tr>
	<td>Thursday</td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "ThuM");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "ThuN");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "ThuA");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "ThuE");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "ThuG");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	</tr>
	
	<tr>
	<td bgcolor="#E7D9C0">Friday</td>
	
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "FriM");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "FriN");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "FriA");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "FriE");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "FriG");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	
	</tr>
	
	<tr>
	<td>Saturday</td>
		<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "SatM");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "SatN");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "SatA");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "SatE");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#FFFBF0"><?php $pos1 = strpos("$row[CanPlay]", "SatG");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	
	</tr>
	
	<tr>
	<td bgcolor="#E7D9C0">Sunday</td> 
		<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "SunM");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";}?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "SunN");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "SunA");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "SunE");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>
	<td bgcolor="#E7D9C0"><?php $pos1 = strpos("$row[CanPlay]", "SunG");
	if ($pos1 != FALSE) {echo "<img border=\"0\" src=\"images/streakplus.gif\">";} ?></td>

	
	</table>
	
	<?php } ?>


<br><br>
<?php } ?>

<?php 

// Only show game history if there are any played games...

if ($row[games] > 0) { ?>

<h2>Recent Games</h2>
<table width="100%" border="0" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>" cellspacing="0" cellpadding="5">

<tr>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Winner</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Loser</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>+/- Points</b></p></td>
<td width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><b>Date</b></p></td>
</tr>
<?php


// Show the players latest played games,...

$sql="SELECT * FROM $gamestable WHERE winner = '$_GET[name]' OR loser = '$_GET[name]'  ORDER BY game_id DESC LIMIT 0, 20";
$result=mysql_query($sql,$db);

$odd = true;

while ($row = mysql_fetch_array($result)) { ?>


<tr>
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text">
<?php

// Only show a link to the profile if it's not the profiles owner...
if ($row[winner] != $_GET[name]) {
echo "<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>"; 
} else {
	echo "$row[winner]"; 
	}
?></p></td>
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?php
// Only show a link to the profile if it's not the profiles owner...
if ($row[loser] != $_GET[name]) {
echo "<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>"; 
} else {
	echo "$row[loser]"; 
	}
?></p></td>
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><? if ($_GET[name] == $row[winner]) { echo  "+ $row[elo_change]";} else { echo  "- $row[elo_change]";} ?></p></td>
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$row[date]" ?></p></td>
</tr>

<?php

if ($odd == true) { $odd = false; } else { $odd = true; }
}
}

}

echo "</table>";
require('bottom.php');
?>

