<?php
@session_start(); // Don't know how to fix the warning it generates, nor what causes it...so let's supress it in all my wisdom ~eyerouge 
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');
require '../include/genericfunctions.inc.php';
require '../include/elo.class.php';
// Set filename for the logfile...
$reranklogfile = "../logs/".date(y_m_d) ."_rerank_logs";
// Initialise misc. variables needed to handle the batching
$CurrentBatch = 1; 
$CurrentStart = 0;
$NextStart = RERANK_PER_BATCH;

// Either the user has a) pressed re-rank button just now or b) pressed it earlier and is in the middle of re-ranking / batching or c) didn't press the re-rank button and pressed the re-cache button instead, in whcih case this first long if below isn't relevant. Let's start by lookin at what happens when a user presses the re-rank button:


if (isset($_POST['rerank']) || ((isset($_GET['batch'])) && (isset($_GET['start'])))) {
	
	// HTML
	echo "<h2>Rerank the Ladder</h2>";

	// If we'ew doing atotal re-rank lets set this to 1. This variable will be used elsewhere  in this and other file(s) to test for this.
	$AdminIsReRankingTheWholeThing = 1;
	
	// If it doesnt say in the url $GET how many entries we are about to re-rank we need to find out now. It will be the total sum of all games ever played in the $gamestable. We'll use this number to inform the user how long there is left aprox and to know when we're supposed to break the looping. 
	
	if (!isset($_GET['entries'])){
		$sqlcount=mysql_query("SELECT count(*) FROM $gamestable"); 
		$numbergames=mysql_fetch_row($sqlcount);
		$NumberGamesInTotal = $numbergames[0];
	}

	// If game entries exist and we have them in the URL we can read them from there.
	if ($_GET['entries'] > 0){ $NumberGamesInTotal = $_GET['entries'] ; } 		
	
	// Check what batch we should work with now and which we should set as the next in line. Do the same with the offset of the entry in the db.
	if (isset($_GET['batch']))  { $CurrentBatch = $_GET['batch']; }
	
	// Find fetch from the url where in the gametable we should start readin from in the next batch.
	if (isset($_GET['start']))  { 
			$CurrentStart = $_GET['start'];
			$NextStart = $CurrentStart + RERANK_PER_BATCH;
	}
		
	// If a batch is under way then we should inform the user about which one it is.	
	if ($_GET['batch'] >= 1){ 
		echo "Current batch: $CurrentBatch <br>Started batch on entry: $CurrentStart <br>Total entries to re-rank: $NumberGamesInTotal";
	}
		
	// Open up the logfile and start scribbling in it. Text will be appended, and one new file will be created for every unique date the re-rank is ran.
	$fh = fopen($reranklogfile, 'a') or die("Can't open log file for the re-rank. Check write permissions.");
	
	// If it's the first line we need an intro in the logg file.... else we'll use a standard message.
	if (	$CurrentBatch == 1) { $stringData = "\n[". date('y-m-d ') . udate('H:i:s.u') ."]   o1. Rerank initiated by ". $_SESSION['username'] ." - Batch 1\n\n"; } 
	else if  ($CurrentBatch > 1) {
		$stringData = "\n\n[". date('y-m-d ') . udate('H:i:s.u') ."]   o0. Continuing with batch ". $CurrentBatch ." >> \n\n"; } 
	fwrite($fh, $stringData); // Write the $string to $file

	// Get the game entries that we're going to work with in _this_ specific batch. When we re-rank we always start with the oldest games first and work our towards the newere ones, one by one. How many games that are fetched from the database depends on  RERANK_PER_BATCH whih is at the config file.
	$rpquery = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable ORDER BY reported_on ASC LIMIT $CurrentStart,". RERANK_PER_BATCH;

	// Pamper the sql from above...    
	$rpresult = mysql_query($rpquery) or die (mysql_error());
	
	// Init. a class? needed for the rating we'll be doing very soon... 
	$elo = new Elo($db);
	
	// Update the log file
	$stringData = "[". date('y-m-d ') . udate('H:i:s.u') ."]   o2. Content in $gamestable selected as batch \n";
	fwrite($fh, $stringData);

	// Needed to keep count of what entry we should input in the log file. 
	$logcounter = $CurrentStart; 

// While there is content in the batch we selected we will re-rank every game in it _one by one_. First a game is _rated_, then if enabled, it is also _ranked_ according to whatever ruleset happens to exist in the current config file. This while.loop contains most of the work done in this file.

	while ($rprow = mysql_fetch_array($rpresult, MYSQL_ASSOC)) {
			
		$winner = $rprow['winner'];
		$loser = $rprow['loser'];
		$logcounter++;  
			
		// Following re-rates the player and updates the game table accordingly
		if (!$elo->RankGameInDB($winner, $loser, $rprow['reported_on'], $rprow['draw'])) {
			echo "Error: could not report game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
			} else {
				echo "<hr><h2>[". $rprow['reported_on']. "] $winner / $loser</h2>";
			// Logg it	
			$stringData = "[". date('y-m-d ') . udate('H:i:s.u') ."]   o3. Processed Rating:   $logcounter. ".  $rprow['reported_on'] . "   ". $winner ." / ". $loser ."\n";
			fwrite($fh, $stringData);
			} 
		
		// That almost all that relates to the Elo rating and re-rating the ladder. Most of the comming deals with getting each players _rank_. Getting the players rank during the re-rank process could have been done smoother or maybe with php but it's better to prefer uniformty in code and it's already using sql in other places, which is way faster in those cases even if it's  disaster here. What we want to avoid is to do some (re)ranking with php and some with Sql since that will break sooner or later. The below SQL queries can be optimized, but it's not a pripority since the feature will seldom be used.
				
		if (RERANK_CREATES_RANK_HISTORY == 1){		
		// Only time this will happn is if admin has set it so in the config file.
		
			// Get the ranking of the players before their game, fetching it from cache table: 

			$wranksql = "SELECT name, reported_on, rank FROM $standingscachetable WHERE name= '".$winner."' LIMIT 1";
			$resultwrank = mysql_query($wranksql) or die(mysql_error());
			$rowwrank = mysql_fetch_array($resultwrank);
					
			$lranksql = "SELECT name, reported_on, rank FROM $standingscachetable WHERE name= '".$loser."' LIMIT 1";
			$resultlrank = mysql_query($lranksql) or die(mysql_error());
			$rowlrank = mysql_fetch_array($resultlrank);
						

			// Sometimes there are not enough games played for the passivity checking to be reliable, so we need to make sure that on such rare occasions we can still get to know if we need to set a player to passive or not as his previous ranking.

				// Set the winners rank to whatever was found in the cache table...
				$winnersoldrank = $rowwrank['rank'];
				
				// If his rank in the cache was 0 (passive) then we don't need to check if we should set him to passive or not. Else, if he had anything else as a ranking, we should check to see that he has really played at least x games within the most recent y days, to see if he is active or not.
				if ($rowwrank['rank'] != 0) {	
				
					$playedpast=mysql_query("SELECT COUNT( * ) FROM $gamestable WHERE (winner = '$winner' OR loser = '$winner') AND reported_on < '".$rprow['reported_on']."' AND reported_on > '".$rprow['reported_on']."' - INTERVAL $passivedays DAY") or die(mysql_error());	
					$xplayed=mysql_fetch_row($playedpast);
					$xplayedclean = $xplayed[0];
					// In the case he had a rank in the cache but out check reveled he has played to few games within the timespan, we set his rank to 0.
					if (	$xplayedclean < GAMES_FOR_ACTIVE) {
						$winnersoldrank = 0;
						}
				}
				// Rinse & repeat for the loser....
				$losersoldrank = $rowlrank['rank'];
				if ($rowlrank['rank'] != 0) {	
				
					$playedpast=mysql_query("SELECT count(*) FROM $gamestable WHERE (winner = '$loser' OR loser = '$loser') AND reported_on < '".$rprow['reported_on']."' AND reported_on > '".$rprow['reported_on']."' - INTERVAL $passivedays DAY") or die(mysql_error());	
					$xplayed=mysql_fetch_row($playedpast);
					$xplayedclean = $xplayed[0];
					
					if (	$xplayedclean < GAMES_FOR_ACTIVE) {
						$losersoldrank = 0;
						}
				}
				
				echo "<br><b>Rank before game:</b><br>$winner: $winnersoldrank<br>$loser: $losersoldrank";
			// Now that we know for sure what rank the players hade before/when they played their game let's put it into the game data:
			
				$UpdateWinnerSql = "UPDATE $gamestable SET w_rank = '$winnersoldrank' WHERE winner = '".$rowwrank['name']."'  AND reported_on = '".$rprow['reported_on']."'";	
					$UpdateWinnerResult = mysql_query($UpdateWinnerSql) or die(mysql_error());	
		
				$UpdateLoserSql = "UPDATE $gamestable SET l_rank = '$losersoldrank' WHERE loser = '".$rowlrank['name']."' AND reported_on = '".$rprow['reported_on']."'";	
				$UpdateLoserResult = mysql_query($UpdateLoserSql) or die(mysql_error());	
					
				//Ditch the cache table, we're going to re-use it in a moment to get the players rankings directly _after_ they played the game.
				mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
				
			// Re-fill the cache table with info.... This will fill it with info up until the date/time the current game report was done, only counting whatever games were reported before it.
			
			mysql_query("INSERT INTO $standingscachetable 
			(name, reported_on, rating, wins, losses, games, streak)
			select * from (select a.name, g.reported_on, 
				   CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating,
				   CASE WHEN g.winner = a.name THEN g.winner_wins ELSE g.loser_wins END as wins,
				   CASE WHEN g.winner = a.name THEN g.winner_losses ELSE g.loser_losses END as losses,
				   CASE WHEN g.winner = a.name THEN g.winner_games ELSE g.loser_games END as games,
				   CASE WHEN g.winner = a.name THEN g.winner_streak ELSE g.loser_streak END as streak
				   FROM (
			select name, max(latest_game) as latest_game from (
			select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = winner) WHERE contested_by_loser = 0 AND withdrawn = 0 AND reported_on <= '".$rprow['reported_on']."' GROUP BY 1
			UNION ALL
			select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = loser) WHERE contested_by_loser = 0 AND withdrawn = 0 AND reported_on <= '".$rprow['reported_on']."' GROUP BY 1
			) latest_game GROUP BY 1
			) a JOIN $gamestable g ON (g.reported_on = a.latest_game)) standings") or die(mysql_error());

				// Since we have re-filled the cache table we can now use it once again to get everyones rankings. However, this time around we will get the rankings directly _after_ the game was reported. In some cases (several) players rankings will change just because of one game.
				require '../include/morecachestandings.inc.php';
				
			// Each players rank has now been updated in the cache. Let's take it from the cache and put it into the games table, in the game info. First we we fetch from the cache table:

				$wranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$winner."' LIMIT 1";
				$resultwrank = mysql_query($wranksql) or die(mysql_error());
				$rowwrank = mysql_fetch_array($resultwrank);
				
				$lranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$loser."' LIMIT 1";
				$resultlrank = mysql_query($lranksql) or die(mysql_error());
				$rowlrank = mysql_fetch_array($resultlrank);
				
			// Then we update the gamestable with the new rank.....

				$UpdateWinnerSql = "UPDATE $gamestable SET w_new_rank = '". $rowwrank['rank'] ."' WHERE  winner = '".$rowwrank['name']."' AND reported_on = '".$rprow['reported_on'] ."'";	
				$UpdateWinnerResult = mysql_query($UpdateWinnerSql) or die(mysql_error());	
				
				$UpdateLoserSql = "UPDATE $gamestable SET l_new_rank = '". $rowlrank['rank'] ."' WHERE  loser = '".$rowlrank['name']."' AND reported_on = '".$rprow['reported_on']."'";	
				$UpdateLoserResult = mysql_query($UpdateLoserSql) or die(mysql_error());	

			// The temp table is used to store some results while doing the sql queries. Let's insure it's not around and generates nasty errors when it's time to re-use it.
			if ($AdminIsReRankingTheWholeThing == 1) {
				mysql_query("DROP TEMPORARY TABLE IF EXISTS $databasename.temp_played");
			}
			
			// Logg it	
			$stringData = "[". date('y-m-d ') . udate('H:i:s.u') ."]   o3. Processed Ranking:   $logcounter. ".  $rprow['reported_on'] . "   ". $winner . " #". $rowwrank['rank'] . " ($winnersoldrank) / $loser #". $rowlrank['rank'] ." ($losersoldrank)\n";
			fwrite($fh, $stringData);
			
			echo  "<br><br><b>Rank after the game:</b><br>$winner: ". $rowwrank['rank']  ."<br>$loser: ". $rowlrank['rank'];
		} // All code that relates to the reranking with this curl.
			
		
		// When we have come to the lastr entry it's time to leave the loop...
		if (	$logcounter >= $NumberGamesInTotal) {
				$FinishedRerank = 1;
				$stringData = "\n[". date('y-m-d ') . udate('H:i:s.u') ."]   o4. Finished on entry $logcounter of $NumberGamesInTotal \n";
				fwrite($fh, $stringData);
				break;
		}
		
	} // While loop ends here.

	// We've finished one batch,but we need to use this to tell the $_GET which we're on and move along to the next one:
	$CurrentBatch++;
		
		// If the re-rank is still in process we'll need to refresh the page so it continues with the next batch automatically.  If the borwser however fails to understand this there is also a link properly displayed. Upon pressing it the next batch in line would be re-ranked, and so on.
		
		if ($FinishedRerank  !=1){
		
			if (RERANK_AUTOBATCH == 1) {
			// Auto-batching can be shut of from config file, but it should work in any sane browser
				echo "<meta http-equiv=\"refresh\" content=\"".RERANK_BATCH_DELAY.";url=rerankladder.php?batch=".	$CurrentBatch  ."&start=". $NextStart ."&entries=". $NumberGamesInTotal ."\">";
				echo "<br><a href=\"rerankladder.php?batch=".	$CurrentBatch  ."&start=". $NextStart ."&entries=". $NumberGamesInTotal ."\">[Please wait at least 30 - 60 sec. and press me to manualy continue re-ranking if the automagic fails]</a>";
			} else {
		echo "<br><a href=\"rerankladder.php?batch=".	$CurrentBatch  ."&start=". $NextStart ."&entries=". $NumberGamesInTotal ."\">[Please click to continue re-ranking the next batch.]</a>"; }
		}

		// When it has all been re-ranked we do a final cache of it using the casual way of caching it, so all players statistics in the cache are up to date.
		
	    if ($FinishedRerank == 1){
			// Finally we recache the ladder a final time, this time using the casual way ($cacheSql) and not the "historical" version of it in this file. 
			mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
			mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);
			require_once '../include/morecachestandings.inc.php'; 
			
			// Close the log file
			fclose($fh);
			// On some systems the file gets sucky r/w permissions, so we fix it here. This maybe has to be fixed for security.
			chmod("$reranklogfile", 0666);
			
			// Display message that we're done.
			echo "<p><br><b>Operation Completed Successfully</b><br>The ladder has been reranked using the history found in the database. Check out the current <a href=\"../ladder.php\">ladder standings</a></p>";
			
			
		}    


// If the admin doesn't re-rank the whole ladder but decides to just repaot the cahce button and that button is pressed this would happen instead:
 
} else if ($_POST['recache']) {

// Recache the ladder, it takes about 1-2 seconds with 25000 games

	mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
	
	// And yes, it uses $cachSql which differs from the cacheSql used in the re-rank catching...
	mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);	
	require_once '../include/morecachestandings.inc.php';

	echo "<h2>Recache the Ladder</h2>";
	echo "<p>The ladder has been recached using the games table. Check the current <a href=\"../ladder.php\">ladder standings</a></p>";

} else {
	
	// That was all, the rest is just non-exotic HTML and happens when no button is presse / batch loaded:
	
?>
<form name="form1" method="post" action="rerankladder.php">
<h2>Rerank the Ladder</h2>
<p>
<b>Warning: this will rerank the entire ladder from the games history</b>
</p>
<p>
<ul>
<li>If the cache is corrupted and the ladder rankings/info seem wrong select <i>Update Corrupted Cache. The process is pretty fast and painless.</i></li>
<li>If you have changed some settings in the config file that involve anything that effects how a player's rated, activity, xp etc is counted then those settings only have effect for the games reported <i>after</i> you made changed them. In order to make already played games use the same ruleset as your new settings in the config file (after you changed it) you must re-rank the whole ladder. To do that, please select <i>Rerank Ladder</i>, and be sure to have set up the re-rank process properly in the config file before you do this. This process may take some time and can time out if you have set it up wrong, but the core data can't be damaged by failures and it can be repeated until you succeed.
</ul>
</p>
<input type="submit" name="rerank" value="Rerank Ladder" class="text" onclick="return confirm('Are you sure you want to rerank the ladder?');">
<input type="submit" name="recache" value="Update corrupted cache" class="text" onclick="return confirm('Are you sure you want to redo the cache?');">
</form>
<?php
}
require('../bottom.php');
?>
