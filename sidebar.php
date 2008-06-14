<?php
// We dont want to show the login form if we're logged in alread, so:
If ($loggedin == 0) {
?>


	<form action=index.php method=post> 
	<input type=text name=user size=15>
	<input type=password name=pass size=15>
	<input type=submit value=Login>
	</form>

<?php } 
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

if ((mysql_num_rows($result)==0) && ($loggedin == 1)) {

echo "<div align='left'><a href='playnow.php'><img border='0' src='graphics/waiting.gif'></a></div><br />";

// If people were in the list we dont display the cock-teazing picture.. instead we show the names and causal links

} elseif (mysql_num_rows($result)!=0) {
	echo "<b>Looking for a game</b><ol>";
	
	while ($row = mysql_fetch_array($result)) {
	
		$timeleft = $row[entered]-(time()-(60*60*$row[time]));
	
	
	TimeConvert("$timeleft");
	
	
		print("<li><a href=\"profile.php?name=$row[username]\">$row[username]</a> ($row[rating])<br> $beenconverted - $row[meetingplace]</li>
		");
	}
	echo "</ol><br />";
	
	// Let's display proper edit / del links if the user is in the waiting list and then show them below it..,..
	
	$sql = "SELECT id FROM $waitingtable WHERE username = '$nameincookie'";
	$intable = mysql_query($sql);
	
		if (mysql_num_rows($intable)!=0) {
		echo "<div align='right'><a href='playnow.php'>edit</a> | <a href='playnow.php?del=$nameincookie'>del</a></div><br>";
		
		} else {
		
		if ($loggedin == 1) {
			echo "<div align='right'><a href='playnow.php'>add me </a></div>";
			}
		}
		
}
	
	
	
// Show latest played games:	
	
	$sql ="SELECT winner, loser, date FROM $gamestable ORDER BY game_id DESC LIMIT 0,$numindexresults";
	$result = mysql_query($sql,$db);
	//$bajs = mysql_fetch_array($result); 
	

	echo "<b>Latest results</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show latest joined and verified players...

	
	// $sql ="SELECT name FROM $playerstable ORDER BY player_id DESC";
	$sql ="SELECT name FROM $playerstable WHERE Confirmation = 'Ok' ORDER BY player_id DESC LIMIT 0,$numindexnewbs";
	$result = mysql_query($sql,$db);

	echo "<br><b>Newcomers</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a></li>";
	}
	echo "</ol>";
	
	
	
// Show the top x players
	
	echo "<br /><br /><b>Top $numindexhiscore players</b><ol>";
	

	// Only get the payers that show in the ladder.... its defined by the minimum amount of games they have to played and a minimal rating

	$sql="SELECT * FROM $playerstable WHERE games >= $gamestorank AND rating >= $ladderminelo and active = 1 ORDER BY rating DESC, games DESC  LIMIT 0,$numindexhiscore";
	//old $sql ="SELECT * FROM $playerstable ORDER BY rating DESC, totalgames DESC";
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

// Show number of games played (excluding deleted ones since they're in another table)
$sql=mysql_query("SELECT * FROM  $gamestable");
$number2=mysql_num_rows($sql);
echo "<br /><b>Played Games:</b> $number2";

// Display average number of games per user...
echo "<br /><b>Games/Player: </b>". round($number2/$number,2);
	
	
// Display number of games played within x amount of days...

$sql="SELECT date FROM $gamestable ORDER BY game_id DESC LIMIT 0,200";
$result = mysql_query($sql,$db);
	
	$daysthismonth = date("t") ; // number of days in the current month 
	if ($daysthismonth == 31) { $dayspreviousmonth = 30; } else {$dayspreviousmonth = 31 ;}
	$currentmonth = date("m") ;// number of this month
	
	if ($currentmonth == 03) {$dayspreviousmonth = 28;} //fix for februari.. this will be broken every 4th year with a day..
	
	$currentdate =  date("d") ;// current date
	

	
	while ($row = mysql_fetch_array($result)) {
		// The dates of each game are stored as folloes in the db: 21:44 12-06-08  ... hence we need to rip out the date and the month to compare with the date & montj of today
		$dateofgame = substr($row[date], 6, 2);
		$monthofgame = substr($row[date], 9, 2);
			
	// Now we take count eery game that was played within the same month, not on todays date, and within the x most recent days.
	// This of course presents a problem when we're on the 1:st of a month and want the games for the 5 most recent days, as none would be counted.
			if ( ($currentmonth == $monthofgame ) && ($currentdate != $dateofgame) && ($dateofgame >= ($currentdate - COUNT_GAMES_OF_LATEST_DAYS))) {
					$recentgames++;
			}
			
	// Lets fix theproblem with the change of the month...
	// The below fix should work but hasn't been tested yet. Please check it out on the 1;st or 2:nd the coming month and adjust accordingly...
	
	if ( ($currentdate - COUNT_GAMES_OF_LATEST_DAYS <= 0) && ( ($monthofgame == ($currentmonth-1) ) || ($monthofgame == 12 && $currentmonth == 1) ) && ($dateofgame >= ($dayspreviousmonth + ($currentdate - COUNT_GAMES_OF_LATEST_DAYS))) ) {
		$recentgames++;
	}

}
// Display x amount of games played the	most recent y days...
	
	if ($recentgames >= MIN_COUNT_GAMES_OF_LATEST_DAYS) { echo "<br><b>Games latest ". COUNT_GAMES_OF_LATEST_DAYS ." days: </b>". $recentgames; }
	

	
	
// Show x  deleted games...
	
	$sql ="SELECT winner, loser, date, elo_change FROM $deletedgames ORDER BY game_id DESC LIMIT 0,$numindexdeled";
	$result = mysql_query($sql,$db);

	echo "<br /><br><b>Deleted reports</b><br><ol>";
	
	while ($bajs = mysql_fetch_array($result)) { 
	
		echo "<li><a href=\"profile.php?name=$bajs[0]\">$bajs[0]</a> beats <a href=\"profile.php?name=$bajs[1]\">$bajs[1]</a><br>$bajs[date] / $bajs[elo_change] p.</li>";
	}
	echo "</ol>";

	
	If ($loggedin == 1)  {
		echo "<br><br><a href='logout.php'>Log out</a>";	
	}
	
	?>


</td>	
	
	

	
	
	
	


	</tr>
</table>
</div>
 </div></div></div></div></div></div></div></div>
<hr>
