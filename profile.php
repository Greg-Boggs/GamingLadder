<?php
session_start();
require 'conf/variables.php';
require_once 'autologin.inc.php';

require 'top.php';

// Get My Ladder Rank
$sql = "select * from (select a.name, g.reported_on, 
       CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating,
       CASE WHEN g.winner = a.name THEN g.winner_wins ELSE g.loser_wins END as wins,
       CASE WHEN g.winner = a.name THEN g.winner_losses ELSE g.loser_losses END as losses,
       CASE WHEN g.winner = a.name THEN g.winner_games ELSE g.loser_games END as games,
       CASE WHEN g.winner = a.name THEN g.winner_streak ELSE g.loser_streak END as streak,
       withdrawn, contested_by_loser, latest_game
       FROM (select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = winner OR name = loser)  WHERE contested_by_loser = 0 AND withdrawn = 0 GROUP BY 1) a JOIN $gamestable g ON (g.reported_on = a.latest_game)) standings join $playerstable USING (name) WHERE
       reported_on > now() - interval $passivedays day AND rating >= $ladderminelo AND games >= $gamestorank ORDER BY 3 desc, 6 desc LIMIT $playersshown";
$result=mysql_query($sql,$db);

// Loop through and find me
$cur = 1;
$rank = "";
while ($row = mysql_fetch_array($result)) {
    if ($row['name'] == $_GET['name']) {
        $rank = $cur;
        break;
    }
    $cur++;
}

// If the player has no rank he is passive, so make a second query grabbing the passive players
if ($rank == "") {
    $sql = "select * from (select a.name, g.reported_on, 
       CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating,
       CASE WHEN g.winner = a.name THEN g.winner_wins ELSE g.loser_wins END as wins,
       CASE WHEN g.winner = a.name THEN g.winner_losses ELSE g.loser_losses END as losses,
       CASE WHEN g.winner = a.name THEN g.winner_games ELSE g.loser_games END as games,
       CASE WHEN g.winner = a.name THEN g.winner_streak ELSE g.loser_streak END as streak,
       withdrawn, contested_by_loser, latest_game
       FROM (select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = winner OR name = loser) WHERE contested_by_loser = 0 AND withdrawn = 0 GROUP BY 1) a JOIN $gamestable g ON (g.reported_on = a.latest_game)) standings right join $playerstable USING (name) WHERE name = '".$_GET['name']."'";


	$result=mysql_query($sql,$db) or die ("Failed to  select current player information");
	$row = mysql_fetch_array($result);
    $rank = "unranked";
}

// PASSIVE CHECK: Lets get to know how many days the player has left before hes put into passive mode -----------------
$sql = "SELECT reported_on + '".$passivedays." days' < now() AS passive, ".$passivedays." - (to_days(now()) - to_days(reported_on)) as daysleft  from $gamestable WHERE (winner = '$_GET[name]' OR loser = '$_GET[name]') AND contested_by_loser = 0 AND withdrawn = 0 ORDER BY reported_on DESC LIMIT 1";
$result = mysql_query($sql, $db);
list($passive, $daysleft) = mysql_fetch_row($result);

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
?>
<table width="100%" cellpadding="1px">
<tr>
<td valign="top">

<h1><?php


if ($_SESSION['username'] == $_GET[name]) {
	    echo "<a href='edit.php'>$row[name] $blocked</a>";
	} else {
	    echo "$row[name] $blocked";
	}
?>
</h1>


<?php 
// Show the players title if he has one...
if ( $row["Titles"]  != "" ) {
	echo "<b>" . $row["Titles"] . "</b><br>";
}

// Show if he's provisional...
if ( $row["provisional"]  == "1" ) {
	echo "<a href=\"faq.php#provisional\">provisional player</a><br>";
}


if ( $row["latest_game"]  != "" ) { 
    echo $row["latest_game"];

    if (($daysleft >= 0)) {
	echo " ($daysleft days left)";
    }
} 



?>
</td>
<td valign="top">
<img src='avatars/<? echo "$row[Avatar].gif'>"; ?>
<?php 
	echo "<br/> <p class='text'><img src='graphics/flags/$row[country].bmp' align='absmiddle' border='1'> $row[country] </p>"; 
?>
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

$sqlavpl="SELECT loser_points FROM $gamestable WHERE loser = '$_GET[name]' ORDER BY reported_on DESC";
$resultavpl=mysql_query($sqlavpl,$db);

while ($rowavpl = mysql_fetch_row($resultavpl)) {

	if ($rowavpl[0] !="") {
	$avpl = $avpl + $rowavpl[0]; 
	$numlosses++;

	}
}

if ($numlosses != 0) {
	$avpl = round(($avpl / $numlosses),0);
}


$sqlavpw="SELECT winner_points FROM $gamestable WHERE winner = '$_GET[name]'  ORDER BY reported_on DESC";
$resultavpw=mysql_query($sqlavpw,$db);


while ($rowavpw = mysql_fetch_row($resultavpw)) {
	if ($rowavpw[0] !="") {
	$avpw = $avpw + $rowavpw[0]; 
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
// classrating is not defined anywhere, until its use is known, it has been commented out
//if (($row[games] >= $gamestorank) && ($daysleft >= 0)) { echo " ($classrating)"; } ?></td>

<td><?echo round(($totalpercentage * 100),0); ?>%</td>
<td><?echo "$row[wins]" ?></td>
<td><?echo "$row[losses]" ?></td>
<td><?echo "$row[games]" ?></td>
<td><?echo "$avpw / $avpl / $avep" ?></td>
<td><?echo "$row[streak]" ?></td>

</tr>
</table>
</p>
<br><br>
<table width="100%" bgcolor="#E7D9C0"><tr><td>
		<?php 
		If ($row[MsgMe] == "Yes") {echo "<b><font color=\"#0D3D02\">Contact me to play!</font></b>";}
		else {echo "<font color=\"#9E005D\">Please don't message me asking for a game.</font>";}
		
		
				if ($_SESSION['username'] && $row[MsgMe] == "Yes"){
			echo "  <a href=\"challenge.php?challenger=".urlencode($_SESSION['username'])."&challenged=$_GET[name]\">[Challenge]</a>";
						
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

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#games").tablesorter({sortList: [[0,1]], widgets: ['zebra'] }); 
    } 
); 
</script>

<table width="<?echo"$tablewidth" ?>" id="games" class="tablesorter">
<thead>
<tr>
<th>Reported Time</th>
<th>Winner</th>
<th>Loser</th>
<th>Winner New Rating</th>
<th>Loser New Rating</th>
<th>Replay</th>
<?
if ($approvegames == "yes") {
?>
<th>Status</th>
<?
}
?>
</tr>
</thead>
<tbody>
<?
    $sql = "SELECT reported_on, DATE_FORMAT(reported_on, '".$GLOBALS['displayDateFormat']."') as report_time, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as is_replay, replay_downloads, withdrawn, contested_by_loser FROM $gamestable WHERE winner = '$_GET[name]' OR loser = '$_GET[name]'  ORDER BY reported_on DESC LIMIT 20";

$result = mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
    if ($row["recorded"] == "yes") {
        $status = "recorded";
    } else {
        $status = "pending";
    }

    $undoDeleteLink = "";
    // Strike through games that aren't counted
    if ($row['withdrawn'] <> 0 || $row['contested_by_loser'] <> 0) {
        $sdel = "<del>";
        $edel = "</del>";
        // We use withdrawn and contested_by_loser to detect if the game should be allowed to restored.
        // There is no limit on restoring games at this point in time. However only a certain number are displayed on this screen.
        if ($row['withdrawn'] <> 0 && $row['winner'] == $_SESSION['username']) {
            $undoDeleteLink = " <a href='restoregame.php?reported_on=".urlencode($row['reported_on'])."'>Restore</a>";
        } else if ($row['contested_by_loser'] <> 0 && $row['loser'] == $_SESSION['username']) {
            $undoDeleteLink = " <a href='restoregame.php?reported_on=".urlencode($row['reported_on'])."'>Restore</a>";
        }
    } else {
        $sdel = "";
        $edel = "";
    }
?>
<tr>
<td><?echo $sdel.$row['report_time'].$edel.$undoDeleteLink ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>".$edel ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>".$ededl ?></td>
<td><?echo $sdel.$row['winner_elo']." (".$row['winner_points'].")".$edel ?></td>
<td><?echo $sdel.$row['loser_elo']." (".$row['loser_points'].")".$edel ?></td>
<td>
<?php
    if ($row['is_replay']  > 0) {
       echo $sdel."<a href=\"download-replay.php?reported_on=".urlencode($row['reported_on'])."\">Download</a> (".$row['replay_downloads'].")".$edel;
    } else {
       echo $sdel."No".$edel;
    }
?>
</td>
<?php
    if ($approvegames == "yes") {
?>
<td><?echo $sdel."$status".$edel ?></td>
<?
    }
?>
</tr>
<?
}
?>
</tbody>
</table>
<br>
<?php
}
require('bottom.php');
?>
