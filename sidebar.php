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
$sql = "SELECT * FROM $waitingtable ORDER BY id DESC";
$result = mysql_query($sql, $db);

while ($row = mysql_fetch_array($result)) {
    // Set the time they wanted to search for a game...
    $inactive = time()-(60*60*$row[time]);

    // Delete the entry if the time has passed...
    if ($row['entered'] < $inactive) {
		$sql3 = "DELETE FROM $waitingtable WHERE username = '$row[username]'";
		$result3 = mysql_query($sql3, $db);
	}
}

$sql = "SELECT * FROM $waitingtable ORDER BY id ASC";
$result = mysql_query($sql, $db);


// If nobody at all is looking for a game at this moment we want a special teazer pic to show up...
if ((mysql_num_rows($result)==0) && isset($_SESSION['username'])) {
    echo "<div align='left'><a href='playnow.php'><img border='0' src='graphics/waiting.gif'></a></div><br />";

    // If people were in the list we dont display the picture.. instead we show the names and causal links

} elseif (mysql_num_rows($result) != 0) {
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
	
	$sql ="SELECT winner, loser, length(replay) as replay, reported_on FROM $gamestable WHERE withdrawn = 0 and contested_by_loser = 0 ORDER BY reported_on DESC LIMIT $numindexresults";
	$result = mysql_query($sql,$db);
	//$bajs = mysql_fetch_array($result); 
	

	echo "<b>Latest results (w/l)</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
        echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> / <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a>";
        // show replay link or not?
        if ($bajs[replay] != 0) {
		    echo " <a href=\"download-replay.php?reported_on=$bajs[reported_on]\">®</a></li>";
		}
        echo "</li>";
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
$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime, 5);

	
	echo "<br /><br /><b>Top $numindexhiscore players</b><ol>";
	

	// Only get the payers that show in the ladder.... its defined by the minimum amount of games they have to played and a minimal rating
    // We don't apply the limit in SQL as we use the total number of rows as the number of ladder players, the since query with more data
    // is faster than the same query twice.
	
	$result = mysql_query($standingsSqlWithRestrictions." LIMIT 10",$db);
	while ($row = mysql_fetch_array($result)) {
		echo "<li><a href=\"profile.php?name=$row[name]\">$row[name]</a> ($row[rating])</li>"; 
	}
	echo "</ol>";
	




echo "</ol><br><div align='left'><a href='friends.php'><img border='0' src='graphics/friendslist.jpg'></a></div><br />";

echo "</ol><br><div align='left'><a href='http://chaosrealm.net/wesnoth/index.php?readnews=-1'><img border='0' src='graphics/mod.jpg'></a></div><br />";
	
// Show  number of registered CONFIRMED users
$sql=mysql_query("SELECT count(*) FROM $playerstable WHERE Confirmation = \"Ok\" OR Confirmation = ''");
$number=mysql_fetch_row($sql);
echo "<br /><br /><b>Confirmed Players:</b> ".$number[0];

$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0");
$number2=mysql_fetch_row($sql);
echo "<br /><b>Played Games:</b>".$number2[0];

// Work hours = the total time by all players spent spent on playin. Work days = the same turned into days. We multiply the number by 2 since a game i splayed by 2 people. Hence, if a game takes 1h to play, 2 work hours has been spent on it. (maybe called "Man hours").
$workingdays = round(((($number2[0] * AVERAGE_GAME_LENGTH)/1440)*2),0);
echo "<br /><b>Work days played:</b> $workingdays";

// Display average number of games per user...
echo "<br /><b>Games/Player: </b>". round($number2[0]/$number[0],2);
	
	
// Display number of games played within x amount of days...
$sql="SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) <> cast(now() as date) AND cast(reported_on as date) >= cast(now() as date) - interval ".COUNT_GAMES_OF_LATEST_DAYS." day AND withdrawn = 0 AND contested_by_loser = 0";
$result = mysql_query($sql,$db);
$recentgames = mysql_fetch_row($result);

echo "<br><b>Games last ". COUNT_GAMES_OF_LATEST_DAYS ." days: </b>". $recentgames[0]; 

// Games today
$sql="SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) = cast(now() as date) AND withdrawn = 0 AND contested_by_loser = 0";
$result = mysql_query($sql,$db);
$todaygames = mysql_fetch_row($result);
echo "<br><b>Games today: </b>". $todaygames[0]; 



// Ranked Players
// Use ladder standings from above to general total.
$sql = "SELECT count(*) FROM $standingscachetable";
$result = mysql_query($sql,$db);
$rankedPlayers = mysql_fetch_row($result);
echo "<br /><b>Ranked Players: </b>".$rankedPlayers[0];

// Show number of replay downloads:

// SELECT SUM(replay_downloads) FROM $gamestable
$sql="SELECT SUM(replay_downloads) FROM $gamestable";
$result = mysql_query($sql,$db);
$replaydownloads= mysql_fetch_row($result);
echo "<br><b>Replay downloads: </b>". $replaydownloads[0]; 


if (isset($_SESSION['username']))  {
    echo "<br /><br /><a href='logout.php'>Log out</a>";	
}
	
?>
</td>	
	</tr>
</table>
</div>
 </div></div></div></div></div></div></div></div>
<hr>
