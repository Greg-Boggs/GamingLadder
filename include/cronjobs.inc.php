<?php
/*

CRON JOBS

This file contains is a "task list" and contains all the tasks that we want to make sure will be executed once in a while by using the fake cron. 
The cron will run every task once when at least x minutes have passed since the last time the task was run. The fake cron is as the name suggests
not an actual cron - we've avoided using real cron jobs since most normal users won't have access to them on their web hosts.

As such the fake cron relies on somebody actually _visiting_ whatever php page that includes this file. In our default ladder setup that would be
the index.php.

Syntax for adding a new cron is:

	$x=new virtualcron(y,"z.txt");
	if ($x->allowAction()) { 
		echo "this triggered";
	}

Where $x is a unique variable identifying that task, y is minutes that have to pass until it's executed again, z is any unique filename. 

Current tasks that we use the fake cron for are:

	[ Rank Cache ] - Whenever a player reports a game or contests/withdraws a game the rank cache table is updated and displays the correct rank of everyone. However, what would happen if nobody playes a game in a day or two? And nobody contests one either? Rankings would then stand unchanged, but that's a problem since a part of how to calculate the rank of a player (actually if he has one at all) is to check for his activity during the x most recent days. So, if nobody playes for a couple of days it means that people that should be passive and lose their ranking are still seen as active and with a ranking since the last time the cache was update they were so. Because time moves along even when nobody reports a game, we need to insure that the rankings cache table is updated at least once a day. And thus we use the fake cron. Result logged if all went well is "Ok"

	[ Player Purge ] Once a week or so (configurable in config file) we want an automatic deletion of all players that have registered an account but not played a single game since they did so. Players with 0 games played can easily be removed from the db and really should within some intervalls since they add nothign to the anything except crap info to the db. Player purge can also be shut off alltogether from the config file. The result logged is "x removed".

	[ Daily Statistics ] Every day (1440 minutes) various statistics about the ladder are saved into a stats table. Result is various depending on the statistics.

	[Ladder Snapshot] Every day the cache table is archieved into the same or other database. Every archieved copy gets the name of the year_mont_date and enables correct display of the ladder at any date in history.



*/

require './virtualcron.php';
$cronbottommsg = "";
// [ Rank Cache ]
// 1440
$vcron2 = new virtualcron(1440, CRONCHECK_PATH . "cachecron.txt");

if ($vcron2->allowAction()) {

// Input in the log that we began this task....
    $logdate = date('Y-m-d H:i:s');
    mysqli_query($db, "INSERT INTO $cronlogtable (Cron, Time, Message) VALUES('Rank Cache', '$logdate', 'Started' ) ") or die(mysqli_error($db));

    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    $resultTruncate = mysqli_query($db, "TRUNCATE TABLE $standingscachetable") or die(mysqli_error($db));
    $resultInsert = mysqli_query($db, "INSERT INTO $standingscachetable " . $cacheSql) or die(mysqli_error($db));
    require_once 'include/morecachestandings.inc.php';

    // Update the log that it completed..
    mysqli_query($db, "UPDATE $cronlogtable SET Message='Ok' WHERE Time='$logdate' AND Cron = 'Rank Cache'") or die(mysqli_error($db));

    $cronbottommsg = $cronbottommsg . " [updated rankings]";
}


// [ Player Purge ]

if (PURGE_GHOST_PLAYERS == 1) {

    $vcron3 = new virtualcron(PURGE_GHOST_MINUTES, CRONCHECK_PATH . "ghostcron.txt");

    if ($vcron3->allowAction()) {

        // Input in the log that we began this task....
        $logdate = date('Y-m-d H:i:s');
        mysqli_query($db, "INSERT INTO $cronlogtable (Cron, Time, Message) VALUES('Ghost Purge', '$logdate', 'Started' ) ") or die(mysqli_error($db));


        // Creates temp table, fills it with the names of everyone with 0 games played, and then deletes those players from the webl_players table.

        mysqli_query($db, "CREATE TEMPORARY TABLE purgees (
		name VARCHAR( 255 ) NOT NULL
		) ENGINE = MYISAM ;") or die(mysqli_error($db));


        mysqli_query($db, "INSERT INTO purgees( name ) (
		SELECT $playerstable.name
		FROM $playerstable
		WHERE name NOT
		IN (

		SELECT name
		FROM $standingscachetable 
		)
		) ;") or die(mysqli_error($db));

        // Get the number of players in the temp table... this is the amount of players that are going to be removed. We want the number for jotting it down into the log.

        $sql = mysqli_query($db, "SELECT count(*) FROM purgees") or die(mysqli_error($db));
        $numberofghosts = mysqli_fetch_row($sql);

        // Remove the players form the players table...
        mysqli_query($db, "DELETE $playerstable FROM $playerstable INNER JOIN purgees WHERE $playerstable.name=purgees.name") or die(mysqli_error($db));

        // Update the logs status message that mission is complete...
        $ghostmsg = $numberofghosts[0] . " removed";

        mysqli_query($db, "UPDATE $cronlogtable SET Message='$ghostmsg' WHERE Time='$logdate' AND Cron = 'Ghost Purge'") or die(mysqli_error($db));

        $cronbottommsg = $cronbottommsg . " [purged accounts w/ 0 games]";

    }

}

// [ Daily statistics ]

$vcron4 = new virtualcron(1440, CRONCHECK_PATH . "dailystatscron.txt");

if ($vcron4->allowAction()) {


    // Get number of registered CONFIRMED users
    $sql = mysqli_query($db, "SELECT count(*) FROM $playerstable WHERE Confirmation = \"Ok\" OR Confirmation = ''");
    $number = mysqli_fetch_row($sql);
    $confirmed = $number[0];

    // Get no. played games
    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0");
    $playedgames2 = mysqli_fetch_row($sql);
    $playedgames = $playedgames2[0];


    // Amount of games played today....
    $sql = "SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) = cast(now() as date) AND withdrawn = 0 AND contested_by_loser = 0";
    $result = mysqli_query($db, $sql);
    $todaygames = mysqli_fetch_row($result);
    $test = $todaygames[0];


    // Display number of games played within 7 most recent days...
    $sql = "SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) <> cast(now() as date) AND cast(reported_on as date) >= cast(now() as date) - interval 7 day AND withdrawn = 0 AND contested_by_loser = 0";
    $result = mysqli_query($db, $sql);
    $recentgames = mysqli_fetch_row($result);


    // Display number of games played within 30 most recent days...
    $sql = "SELECT count(*) FROM $gamestable WHERE cast(reported_on as date) <> cast(now() as date) AND cast(reported_on as date) >= cast(now() as date) - interval 30 day AND withdrawn = 0 AND contested_by_loser = 0";
    $result = mysqli_query($db, $sql);
    $recentgames2 = mysqli_fetch_row($result);

    // Get # ranked players...
    $sql = "SELECT count(*) FROM $standingscachetable WHERE rank > 0";
    $result = mysqli_query($db, $sql);
    $rankedPlayers = mysqli_fetch_row($result);

    // Get replay downloads
    $sql = "SELECT SUM(replay_downloads) FROM $gamestable";
    $result = mysqli_query($db, $sql);
    $replaydownloads = mysqli_fetch_row($result);


    // Now we need to gather some info about the ratings...

    // Number of winner ratings given....
    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND winner_stars != 'NULL'");
    $numberwinnerratings = mysqli_fetch_row($sql);


    // Number of loser ratings given....
    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND loser_stars != 'NULL'");
    $numberloserratings = mysqli_fetch_row($sql);

    //Number games with at least one rating
    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND winner_stars != 'NULL' OR loser_stars != 'NULL'");
    $gamesrated = mysqli_fetch_row($sql);

    // Totalsum winner ratings...
    $sql = "SELECT SUM(winner_stars) FROM $gamestable";
    $result = mysqli_query($db, $sql);
    $sumwinnerratings = mysqli_fetch_row($result);

    // Totalsum loser ratings...
    $sql = "SELECT SUM(loser_stars) FROM $gamestable";
    $result = mysqli_query($db, $sql);
    $sumloserratings = mysqli_fetch_row($result);

    // Here's the average:
    @$avgsportsmanship = ($sumwinnerratings[0] + $sumloserratings[0]) / ($numberloserratings[0] + $numberwinnerratings[0]);


    $GamesWithRating = @round((($gamesrated[0] / $playedgames2[0]) * 100), 3);
    $AverageSporsmanship = @round($avgsportsmanship, 3);


    // Get amount of contested, withdrawn and revoked games (revoked = the sum of both the previous)


    // Now we'll show how many % of all games are contested:
    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE contested_by_loser = 1");
    $contested = mysqli_fetch_row($sql);
    $contestedgames = @round((($contested[0] / $playedgames2[0]) * 100), 3);

    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE withdrawn = 1");
    $withdrawn = mysqli_fetch_row($sql);
    $withdrawngames = @round((($withdrawn[0] / $playedgames2[0]) * 100), 3);

    $revokedgames = @round(((($withdrawn[0] + $contested[0]) / $playedgames2[0]) * 100), 3);

    $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE winner_comment != '' OR loser_comment != ''");
    $commented = mysqli_fetch_row($sql);
    $commentedgames = @round((($commented[0] / $playedgames2[0]) * 100), 3);


    // Input all info in the statistcis table....
    $logdate = date('Y-m-d H:i:s');
    mysqli_query($db, "INSERT INTO $statstable (Time, Confirmed_Players, Played_Games, Games_Today, Games_Recent_7, Games_Recent_30, Ranked_Players, Replay_Downloads, Games_Rated, Avg_Sportsmanship, Contested_Games, Withdrawn_Games, Revoked_Games, Commented_Games) VALUES('$logdate','$confirmed', '$playedgames', '$test', '$recentgames[0]', '$recentgames2[0]', $rankedPlayers[0],$replaydownloads[0],$GamesWithRating,$AverageSporsmanship, $contestedgames, $withdrawngames, $revokedgames,$commentedgames)") or die(mysqli_error($db));

    $cronbottommsg = $cronbottommsg . " [ladder stats logged]";


}


// [ Ladder Snapshots ]
if (KEEP_LADDER_HISTORY == 1) {
    $vcron5 = new virtualcron(1440, CRONCHECK_PATH . "ladderhistorycron.txt");

    if ($vcron5->allowAction()) {

        $logdate = date('Y_m_d');
        $target = $historydatabasename . "." . $logdate;
        $source = $databasename . "." . $standingscachetable;

        mysqli_query($db, "DROP TABLE IF EXISTS $target;") or die(mysqli_error($db));
        mysqli_query($db, "CREATE TABLE  $target SELECT * FROM $source WHERE rank > 0;") or die(mysqli_error($db));
        $cronbottommsg = $cronbottommsg . " [archieved ladder rankings]";
    }

}
?>