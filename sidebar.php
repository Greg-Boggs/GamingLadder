
<td width="25%" valign="top" class="smallinfo">	

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
    $inactive = time()-(60*60*$row['time']);

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
	
		$timeleft = $row['entered']-(time()-(60*60*$row['time']));
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
	
	$sql ="SELECT winner, loser, replay_filename is not null as is_replay, reported_on FROM $gamestable WHERE withdrawn = 0 and contested_by_loser = 0 ORDER BY reported_on DESC LIMIT $numindexresults";
	$result = mysql_query($sql,$db);
	//$bajs = mysql_fetch_array($result); 
	

	echo "<b>Latest results (w/l)</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
        echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> / <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a>";
        // show replay link or not?
        if ($bajs['is_replay'] != 0) {
		    echo " <a href=\"download-replay.php?reported_on=$bajs[reported_on]\">®</a></li>";
		}
        echo "</li>";
	}
	echo "</ol>";
	

	
// Show latest joined and verified players...
	
	$sql ="SELECT name FROM $playerstable WHERE Confirmation = 'Ok' ORDER BY player_id DESC LIMIT $numindexnewbs";
	$result = mysql_query($sql,$db);

	echo "<br><b>Newcomers</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a></li>";
	}
	echo "</ol>";
	
	
	//date('Y-m-d', strtotime(date('Y-m-1')." -1 month"))
	$prevStartDate = strtotime(date('Y-m-01').' -1 month');
// Show player of the month
$sqlConditions = "g.reported_on >= '".date('Y-m-d 00:00:00',$prevStartDate)."' AND g.reported_on <= '".date('Y-m-01 00:00:00')."'";
$sql = "select gg.player, sum(gg.points) as total, p.Avatar, s.rating
from (
select g.loser as player, g.loser_elo as elo, g.loser_points as points, g.reported_on from $gamestable g where $sqlConditions
union
select g.winner as player, g.winner_elo as elo, g.winner_points as points, g.reported_on from $gamestable g where $sqlConditions
) as gg
join $playerstable as p ON p.name = gg.player
join $standingscachetable as s ON p.name = s.name
group by gg.player
order by total desc
limit 0,1";
$result = mysql_query($sql,$db);
	
echo "<br><b>Player of the month</b> (".date('F Y', $prevStartDate).")<br>";
while ($bajs = mysql_fetch_array($result)) { 
	echo "<img border='0' src='avatars/$bajs[2].gif' alt='avatar' style='margin: 8px 5px'/><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> ($bajs[3] / +$bajs[1]pts)";
}
unset($prevStartDate);

	
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
	

// Link to friendslist: This is probably only usable by Battle for Wesnoth latters - all others would like to comment out/delete the line below:
echo "</ol><br><div align='left'><a href='friends.php'><img border='0' src='graphics/friendslist.jpg'></a></div><br />";

// Show  number of registered CONFIRMED users
$sql=mysql_query("SELECT count(*) FROM $playerstable WHERE Confirmation = \"Ok\" OR Confirmation = ''");
$number=mysql_fetch_row($sql);
echo "<br /><br /><b>Registered Players:</b> ".$number[0];


// Number of players that have played at least one game...
$sql=mysql_query("SELECT count(*) FROM $standingscachetable");
$nonzeroplayers=mysql_fetch_row($sql);



$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0");
$playedgames2=mysql_fetch_row($sql);
echo "<br /><b>Played Games:</b> ".$playedgames2[0];

// Work hours = the total time by all players spent spent on playing. Work days = the same turned into days. We multiply the number by 2 since a game is played by 2 people. Hence, if a game takes 1h to play, 2 work hours has been spent on it. (maybe called "Man hours").
$workingdays = round(((($playedgames2[0] * AVERAGE_GAME_LENGTH)/1440)*2),0);
echo "<br /><b>Work days played:</b> $workingdays";

// Display average number of games per user...
echo "<br /><b>Games/Player: </b>". @round($playedgames2[0]/$nonzeroplayers[0],2);
	
	
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
$sql = "SELECT count(*) FROM ($standingsSqlWithRestrictions) a";
$result = mysql_query($sql,$db);
$rankedPlayers = mysql_fetch_row($result);
echo "<br /><b>Ranked Players: </b>".$rankedPlayers[0];

// Show number of replay downloads:

$sql="SELECT SUM(replay_downloads) FROM $gamestable";
$result = mysql_query($sql,$db);
$replaydownloads= mysql_fetch_row($result);
echo "<br><b>Replay downloads: </b>". $replaydownloads[0]; 


// Do some calcs to get the average sportsmanship rating. Games that have a raing of both the winner and loser will get them added and then divided by 2. Then all game ratings will be summed up and divided with number of games that had a rating.

// Number of winner ratings given....
$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND winner_stars != 'NULL'");
$numberwinnerratings=mysql_fetch_row($sql);


// Number of loser ratings given....
$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND loser_stars != 'NULL'");
$numberloserratings=mysql_fetch_row($sql);

//Number games with at least one rating 

$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND winner_stars != 'NULL' OR loser_stars != 'NULL'");
$gamesrated=mysql_fetch_row($sql);

// Totalsum winner ratings...
$sql="SELECT SUM(winner_stars) FROM $gamestable";
$result = mysql_query($sql,$db);
$sumwinnerratings= mysql_fetch_row($result);

// Totalsum loser ratings...
$sql="SELECT SUM(loser_stars) FROM $gamestable";
$result = mysql_query($sql,$db);
$sumloserratings= mysql_fetch_row($result);

// Here's the average:
@$avgsportsmanship = ($sumwinnerratings[0] + $sumloserratings[0]) / ( $numberloserratings[0] + $numberwinnerratings[0]);


echo "<br><b >Games w. sprtm. rating:</b> ". @round((($gamesrated[0]/$playedgames2[0])*100),0) ."% (". $gamesrated[0] . ")";
echo "<br><b >Avg. sprtm. rating:</b> ". round($avgsportsmanship,2);

// Now we'll show how many % of all games are contested:

$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE contested_by_loser = 1");
$contested=mysql_fetch_row($sql);
echo "<br><b>Contested:</b> ". @round((($contested[0]/$playedgames2[0])*100),0) . "% (". $contested[0] .")";


$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE withdrawn = 1");
$withdrawn=mysql_fetch_row($sql);
echo "<br><b>Withdrawn:</b> ". @round((($withdrawn[0]/$playedgames2[0])*100),0) ."% (". $withdrawn[0] .")";

echo "<br><b>Revoked:</b> ". @round(((($withdrawn[0]+$contested[0])/$playedgames2[0])*100),0) ."% (". ($withdrawn[0]+$contested[0]) .")";


$sql=mysql_query("SELECT count(*) FROM $gamestable WHERE winner_comment != '' OR loser_comment != ''");
$commented=mysql_fetch_row($sql);
echo "<br><b>Commented:</b> ". @round((($commented[0]/$playedgames2[0])*100),0) ."% (". $commented[0] .")";



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
