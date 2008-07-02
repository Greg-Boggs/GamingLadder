<?php
// We dont want to show the login form if we're logged in alread, so:
if (!isset($_SESSION['username']))  {
?>


	<form action=index.php method=post> 
	<input type=text name=user size=15>
	<input type=password name=pass size=15>
	<input type=submit value=Login>
	</form>

<?php } 

function TimeConvert($ToConvert) 
{
    $min = floor($ToConvert/60);
    $h = floor($min/60);
    $min2 = $min;

    if ($h >= 1) {$min2 = ($min - ($h * 60));}

    if ($h >= 1) {
        return $h."h ".$min2."min";
    } else {
        return $min2."min";
    }
}

?>
  <div class="border_left"><div class="border_right"><div class="border_bottom">
  <div class="corner_bottomleft"><div class="corner_bottomright">
  <div class="border_top"><div class="corner_topleft"><div class="corner_topright">

<div class="sidebar">
<?php


// Lets erase all waiting players who are no longer waiting...

$sql="SELECT * FROM $waitingtable ORDER BY id DESC";
$result=mysql_query($sql,$db);

while ($row = mysql_fetch_array($result)) {

	// Set the time they wanted to search for a game...

	$inactive = time()-(60*60*$row[time]);
	// DEB echo "inactive: $inactive<br>";	

	// Delete the entry if the time has passed...

	if ($row[entered] < $inactive) {
	
		$sql3="DELETE FROM $waitingtable WHERE username = '$row[username]'";
		$result3=mysql_query($sql3,$db);
	}
	


}

//$db->query ("DELETE FROM online WHERE lastactive < $inactive");



$sql="SELECT * FROM $waitingtable ORDER BY id ASC";
$result=mysql_query($sql,$db);


// If nobody at all is looking for a game at this moment we want a special teazer pic to show up...

if ((mysql_num_rows($result)==0) && isset($_SESSION['username'])) {

echo "<div align='left'><a href='playnow.php'><img border='0' src='graphics/waiting.gif'></a></div><br />";

// If people were in the list we dont display the cock-teazing picture.. instead we show the names and causal links

} elseif (mysql_num_rows($result)!=0) {
	echo "<b>Looking for a game</b><ol>";
	
	while ($row = mysql_fetch_array($result)) {
	
		$timeleft = $row[entered]-(time()-(60*60*$row[time]));
		print("<li><a href=\"profile.php?name=$row[username]\">$row[username]</a> ($row[rating])<br> ".TimeConvert($timeleft)." - $row[meetingplace]</li>
		");
	}
	echo "</ol><br />";
	
	// Let's display proper edit / del links if the user is in the waiting list and then show them below it..,..
	
	$sql = "SELECT id FROM $waitingtable WHERE username = '".$_SESSION['username']."'";
	$intable = mysql_query($sql);
	
		if (mysql_num_rows($intable)!=0) {
		echo "<div align='right'><a href='playnow.php'>edit</a> | <a href='playnow.php?del=".$_SESSION['username']."'>del</a></div><br>";
		
		} else {
		
	    if (isset($_SESSION['username']))  {
			echo "<div align='right'><a href='playnow.php'>add me </a></div>";
			}
		}
		
}
	
	
	
// Show latest played games:	
	
	$sql ="SELECT winner, loser FROM $gamestable WHERE withdrawn = 0 and contested_by_loser = 0 ORDER BY reported_on DESC LIMIT $numindexresults";
	$result = mysql_query($sql,$db);
	//$bajs = mysql_fetch_array($result); 
	

	echo "<b>Latest results</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show latest joined and verified players...

	
	// $sql ="SELECT name FROM $playerstable ORDER BY player_id DESC";
	$sql ="SELECT name FROM $playerstable WHERE Confirmation = 'Ok' ORDER BY player_id DESC LIMIT $numindexnewbs";
	$result = mysql_query($sql,$db);

	echo "<br><b>Newcomers</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show the top x players
	
	echo "<br /><br /><b>Top $numindexhiscore players</b><ol>";
	

	// Only get the payers that show in the ladder.... its defined by the minimum amount of games they have to played and a minimal rating
	
    $sql = "select * from (select a.name, g.reported_on, 
       CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating,
       CASE WHEN g.winner = a.name THEN g.winner_wins ELSE g.loser_wins END as wins,
       CASE WHEN g.winner = a.name THEN g.winner_losses ELSE g.loser_losses END as losses,
       CASE WHEN g.winner = a.name THEN g.winner_games ELSE g.loser_games END as games,
       CASE WHEN g.winner = a.name THEN g.winner_streak ELSE g.loser_streak END as streak
       FROM (select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = winner OR name = loser) WHERE contested_by_loser = 0 AND withdrawn = 0 GROUP BY 1) a JOIN $gamestable g ON (g.reported_on = a.latest_game)) standings join $playerstable USING (name) WHERE
       reported_on > now() - interval $passivedays day AND rating >= $ladderminelo AND games >= $gamestorank ORDER BY 3 desc, 6 desc LIMIT 10";

	$result = mysql_query($sql,$db);

	while ($row = mysql_fetch_array($result)) {
		echo "<li><a href=\"profile.php?name=$row[name]\">$row[name]</a> ($row[rating])</li>"; 
	}
	echo "</ol>";
	




echo "</ol><br><div align='left'><a href='friends.php'><img border='0' src='graphics/friendslist.jpg'></a></div><br />";

echo "</ol><br><div align='left'><a href='http://chaosrealm.net/wesnoth/index.php?readnews=-1'><img border='0' src='graphics/mod.jpg'></a></div><br />";
	
// Show  number of registered CONFIRMED users
$sql=mysql_query("SELECT * FROM $playerstable WHERE Confirmation = \"Ok\" OR Confirmation = ''");
$number=mysql_num_rows($sql);
echo "<br /><br /><b>Confirmed Players:</b> $number";

$sql=mysql_query("SELECT * FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0");
$number2=mysql_num_rows($sql);
echo "<br /><b>Played Games:</b> $number2";

// Display average number of games per user...
echo "<br /><b>Games/Player: </b>". round($number2/$number,2);
	
	
// Display number of games played within x amount of days...
$sql="SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) >= cast(now() as date) - ".COUNT_GAMES_OF_LATEST_DAYS." AND withdrawn = 0 AND contested_by_loser = 0";
$result = mysql_query($sql,$db);
$recentgames = mysql_fetch_row($result);

if ($recentgames[0] >= 1) {
    echo "<br><b>Games latest ". COUNT_GAMES_OF_LATEST_DAYS ." days: </b>". $recentgames[0]; 
}
	
// Show x  deleted games...
	
	$sql ="SELECT winner, loser, DATE_FORMAT(reported_on,'".$GLOBALS['displayDateFormat']."') FROM $gamestable WHERE contested_by_loser <> 0 OR withdrawn <> 0 ORDER BY reported_on DESC LIMIT $numindexresults";
	$result = mysql_query($sql,$db);

	echo "<br /><br><b>Deleted reports</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a><br>$bajs[2]</li>";
	}
	echo "</ol>";

	
	If (isset($_SESSION['username']))  {
		echo "<br /><br /><a href='logout.php'>Log out</a>";	
	}
	
	?>


</td>	
	
	

	
	
	
	


	</tr>
</table>
</div>
 </div></div></div></div></div></div></div></div>
<hr>
