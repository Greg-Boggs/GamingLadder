<?
// v.1.11
include ("logincheck.inc.php");
$page = "profile";

/* DEB
if  ($loggedin == 0) {
	echo "<h1>Not logged in...</h1><br>";
} elseif ($loggedin == 1) {echo "Logged in as $nameincookie<br>";}
*/



?>
<?


// If we're logged in and viewing our own profile then we see how many days we have left before we're marked as passive...
// The players ae supposed to plau at leasy 1 game within x days This will show them how many days they have left.

//DEB echo "<br> $loggedin / $_GET[name] / $nameincookie<br>";
//if (($loggedin == 1) && ($_GET[name] == $nameincookie)) {


	// Lets get the Latest Played game from the database, for just the logged in user...***
	$sql="SELECT LastGame FROM $playerstable WHERE name = '$_GET[name]' ORDER BY name ASC";
	$result=mysql_query($sql,$db);
	$bajs = mysql_fetch_array($result); 

	// Okey, let's convert the crap we store in the database into nice digits, putting the hour, sec, day, month and year in one variable each.
	$rawdata = $bajs[LastGame];

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
	// DEB echo "<br>Days left: $daysleft";

	// Show us the date when he enters passive mode...

	$passivedate = ($unixedlatest + ($passivedays*24*60*60));
	$passivedate = date("j M", $passivedate);

	


//}


// Get the ranking position....
// SELECT COUNT(id) FROM table WHERE id <= 3;

$rank = 0;
//$sqlr="SELECT * FROM $playerstable ORDER BY rating DESC";
$sqlr="SELECT * FROM $playerstable ORDER BY rating DESC, totalgames DESC";
//  WHERE totalgames >= 1

$resultr=mysql_query($sqlr,$db);




while ($jaja[name] != $_GET[name]) {
	
	$jaja=mysql_fetch_array($resultr);


	


	// Okey, let's convert the crap we store in the database into nice digits, putting the hour, sec, day, month and year in one variable each.
	$rawdata = $jaja[LastGame];
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


	// SETUP: Onluy count the players who are active...
	

	
	// SETUP: IMPORTANT to set the >= in $jaja[totalgames] to the minimal amount of total games needed to appear in the ladder...
	
	if (($jaja[name] != "zAdminTest03") && ($jaja[name] != "zAdminTest") &&  ($jaja[totalgames] >= $gamestorank) && ($daysleft >= 0)) {
		
		$rank = $rank+1;
		//D echo "$jaja[name]  $rank ::  ";
	}
	
	// DEB echo "$jaja[name] $jaja[rating] $rank<br>";
}


$sql="SELECT * FROM $playerstable WHERE name = '$_GET[name]' ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
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

// Read the mail from the db and make it spambotsafe...

if ($row['Joined'] != NULL) { $joined = date("H:i d m y", $row['Joined']); } else {$joined ="00:00 06 03 08"; }
$mailaddress = $row[mail];
$mailaddress = str_replace("@", " (at) ", $mailaddress);
$mailaddress = str_replace(".", " (dot) ", $mailaddress);
$jabbername = $row[Jabber];
$jabbername = str_replace("@", " (at) ", $jabbername);
$jabbername = str_replace(".", " (dot) ", $jabbername);
$jabberpic = "<img border='1' src='images/jabber.gif' align='absmiddle'>";

$mailpic = "<img border='1' src='images/mail.gif' align='absmiddle'></a>";
}
if ($row[icq] == "n/a") {
$icqnumber = "n/a";
$icqpic = "";
}else{
$icqnumber = "$row[icq]";
$icqpic = "<img border='1' src='images/icq.gif' align='absmiddle'>";
}
if ($row[aim] == "n/a") {
$aimname = "n/a";
$aimpic = "";
}else{
$aimname = "$row[aim]";
$aimpic = "<img border='1' src='images/aim.gif' align='absmiddle'>";
}
if ($row[msn] == "n/a") {
$msnname = "n/a";
$msnpic = "";
}else{

$msnname = "$row[msn]";
$msnname = str_replace("@", " (at) ", $msnname);
$msnname = str_replace(".", " (dot) ", $msnname);


$msnpic = "<img border='1' src='images/msn.gif' align='absmiddle'></a>";
}

if ($row[games] <= 0) {
$percentage = 0.000;
}else {
$percentage = $row[wins] / $row[games];
}
$total = $row[wins] + $row[losses];
$totaltotal = $row[totalwins] + $row[totallosses];
if ($totaltotal <= 0) {
$totalpercentage = 0.000;
}else {
$totalpercentage = $row[totalwins] / $totaltotal;
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
if ( $row["LastGame"]  != "" ) { 
echo $row["LastGame"] .", ". $row["Movement"] ." p. ";


// We're logged in and looking at our own profile we can see the number of days left untill we are put in passive mode... 

if (($daysleft >= 0) && ($_GET[name] == $nameincookie)) {
	echo "($daysleft days left)";
}

if (($loggedin == 1) && ($_GET[name] != $nameincookie)) {


// Lets get the rank of the profile character and the rank of the viewer... we're using it later in the functions to figure out how much theyd win from each other if they were to play & win.

$oldratingloser = $row["rating"];
		
$sqlmyself="SELECT * FROM $playerstable WHERE name = '$nameincookie'";	
		$resultmyself=mysql_query($sqlmyself,$db);		
		$rowmyself = mysql_fetch_array($resultmyself);	
		$oldratingwinner = $rowmyself["rating"];		

$case1 = EloMasterWins($oldratingwinner,$oldratingloser);	

$case2 = EloNewbWins($oldratingwinner,$oldratingloser);



echo "[$case1" . "p / " .$case2 ."p]";
}

 } 
?>
</td>
<td valign="top">
<img src='avatars/<? echo "$row[Avatar].gif'>"; ?>
<?php 
If ($row[country] != "No country") { echo "<br/> <p class='text'><img src='flags/$row[country].bmp' align='absmiddle' border='1'> $row[country] </p>"; } ?>
</td>
</tr>
</table>


<p class="text">



<!-- Percentage: <?// printf("%.3f", $percentage * 100); ?><br><br> -->
<table width="100%">
<tr>

<td bgcolor="#E7D9C0"><b>Rank</b></td>
<td bgcolor="#E7D9C0"><b>Rating</b></td>
<td bgcolor="#E7D9C0"><b>Percent</b></td>
<td bgcolor="#E7D9C0"><b>Wins</b></td>
<td bgcolor="#E7D9C0"><b>Losses</b></td>
<td bgcolor="#E7D9C0"><b>Played</b></td>
<td bgcolor="#E7D9C0"><b>Average P WLT</b></td>
<td bgcolor="#E7D9C0"><b>Streak</b></td>
</tr>
<tr>

<td>

<?php

// we need some info to get to know how many points the player wins in average WHEN he wins, and the same about when he loses...

$sqlavpl="SELECT RankMove FROM $gamestable WHERE loser = '$_GET[name]'  ORDER BY game_id DESC";
$resultavpl=mysql_query($sqlavpl,$db);

while ($rowavpl = mysql_fetch_array($resultavpl)) {

	if ($rowavpl[RankMove] !="") {
	$avpl = $avpl + $rowavpl[RankMove]; 
	$numlosses++;

	}
}

$avpl = "-". round(($avpl / $numlosses),0);


$sqlavpw="SELECT RankMove FROM $gamestable WHERE winner = '$_GET[name]'  ORDER BY game_id DESC";
$resultavpw=mysql_query($sqlavpw,$db);


while ($rowavpw = mysql_fetch_array($resultavpw)) {
	if ($rowavpw[RankMove] !="") {
	$avpw = $avpw + $rowavpw[RankMove]; 
	$numwins++;
	}
}
$avpw = round(($avpw / $numwins),0);

//while ($row = mysql_fetch_array($result)) 

// get the players averahe points / game...
$avep = round((($row[rating] - 1500)/$row[totalgames]),2);


if ($row[totalgames] < $gamestorank) {
echo "unranked"; }
else {


// Get to know how many points the player gets in an average game... this will also say something about him as a player choosing his opponents.



if ($daysleft >= 0) {echo $rank;} else {echo "<a href=\"faq.php#passive\">(passive)</a>";}  }?></td>
<td><?echo round($row[rating],0); ?></td>
<td><?echo round(($totalpercentage * 100),0); ?>%</td>
<td><?echo "$row[totalwins]" ?></td>
<td><?echo "$row[totallosses]" ?></td>
<td><?echo "$row[totalgames]" ?></td>
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

if ($row[totalgames] > 0) { ?>

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
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><? if ($_GET[name] == $row[winner]) { echo  "+ $row[RankMove]";} else { echo  "- $row[RankMove]";} ?></p></td>
<td bgcolor=<?php if ($odd == true) { echo "'#FFFBF0'"; } else { echo "'#E7D9C0'"; } ?> width="<?echo"$width" ?>" bordercolor="<?echo"$color7" ?>" nowrap><p class="text"><?echo "$row[date]" ?></p></td>
</tr>
<?php

if ($odd == true) { $odd = false; } else { $odd = true; }
}
?>
</table>
<?php } 


// echo "<br>$_GET[name] joined $joined";


?>



<?
}


		
function EloNewbWins($oldratingloser, $oldratingwinner) {

		
		
		$constant = 24;	
		$rw1 = $oldratingwinner - $oldratingloser;
		$rw2 = -$rw1/400;		
		$rw3 = pow(10,$rw2);		
		$rw4 = $rw3 + 1;		
		$rw5 = 1/$rw4;		
		$rw6 = 1 - $rw5;		
		$rw7 = $constant * $rw6;		
		return round($rw7); // hur många p man får, $ratingdifferent

}

function EloMasterWins($oldratingloser, $oldratingwinner) {	

		

		$constant = 24;	
		$rw1 = $oldratingloser - $oldratingwinner;
		$rw2 = -$rw1/400;		
		$rw3 = pow(10,$rw2);		
		$rw4 = $rw3 + 1;		
		$rw5 = 1/$rw4;		
		$rw6 = 1 - $rw5;		
		$rw7 = $constant * $rw6;		
		return round($rw7); // hur många p man får, $ratingdifferent

}

require('bottom.php');
?>