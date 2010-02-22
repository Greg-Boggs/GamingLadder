<?php 
require_once 'genericfunctions.inc.php';

/*

v1.0

Notice: This file should be included _directly_ after the standings cache has been done. It is not supposed to be run on it's own, nor is the first part of the cach supposed to be ran without running this directly after.

The original standings cache table creation was written by mr russ.

To avoid code confusion,. keep the files cleaner and easily re-use this code eyerouge decided to put it as an inc since it's supposed to be ran from
several different files. (At least when a game is reported, contested  or withdrawn, and also when the Admin re-createas the cache table)

Now that the ratings scache table has been created we'll get info about how many games each active player has played within the x most recent days and put it in there as well. Keep in mind we won't include such a value/result for those players that don't fulfill the minimum requirement of _at least_ x played games within y most recent days. So, in effect, the only players that will have values in the recently_played field are players we can consider to be active players (and yes, they also fullfill the requirement to have played at least z games to even be on the ladder to begin with, where z and eveyrhting else can be set in the config file..

We will now execute 3 queries where we will a) create a temporary table that is unique and valid for this sql session only. 2) Then select all users that have played >= number of games the most recent y days and also more than z games in total on the ladder, and put them into the temp table. 3) Lastly, update every user which is in the temp table in the standings cache table, which has already been created elsewhere in the code. The update will add/change the played_recently row the standings cache table. */

// 1. Create the temp table where we will store stuff that will later be xfered to the cache table...
TimerOn();


$result = mysql_query("CREATE TEMPORARY TABLE $databasename.temp_played (
name VARCHAR( 255 ) NOT NULL ,
recently_played BIGINT( 11 ) NULL DEFAULT NULL
) ENGINE = MYISAM ;", $db);

// Check result
// This shows the actual query sent to MySQL, and the error. Useful for debugging.
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
} else { if (isset($_POST['recache'])){echo "<br>1. Created temp working table.... [ ". TimerOff() . " sec ]"; }}

// 2. Select every player that has played >= x games within y most recent days and also has played => z in total on the ladder. 

$MySQLgamestorank = $gamestorank;
$MySQLgamestobeactive = GAMES_FOR_ACTIVE;



TimerOn();

if (isset($AdminIsReRankingTheWholeThing) != 1){
	// Please notice that the recently_played column either shows 0 if a player has not managed to play x games within y days, or, if he has played x games or more within y days, it will actually contain the number of games. This means that only time a player has a 0 in this column is if he's considered to be incactive due to not having played enough games. If a player has played 3 games within y amount of days, and the ladder requires the player ti have played 4 games in y amount of days, then this column would show 0, and not 3, as one might easily think.
	$result = mysql_query("insert into temp_played (name, recently_played)
	(

	SELECT userid, count(*) as cnt 
	FROM
	(
	    SELECT winner as userid
	    from  $gamestable g 
	    where g.reported_on > now() - interval $passivedays day AND g.contested_by_loser = 0 AND g.withdrawn = 0
	    UNION ALL 
	    SELECT loser as userid
	    from $gamestable g 
	    where g.reported_on > now() - interval $passivedays day AND g.contested_by_loser = 0 AND g.withdrawn = 0
	) t
	GROUP BY userid
	HAVING COUNT(*) >= $MySQLgamestobeactive
	);");

} else {


// Gamla, vars syntax fungerar >>   where g.reported_on >= '". substr($rprow['reported_on'],0,10) . " 00:00:00' - interval $passivedays day AND g.reported_on <= '". substr($rprow['reported_on'],0,10) . " 00:00:00' AND g.contested_by_loser = 0 AND g.withdrawn = 0

// Nya som ej funkar syntaxen >>  where g.reported_on LIKE '". substr($rprow['reported_on'],0,10) ." %%:%%:%%' OR (g.reported_on >= '". substr($rprow['reported_on'],0,10)."' - interval $passivedays day AND g.reported_on < '". substr($rprow['reported_on'],0,10) ."' AND g.contested_by_loser = 0 AND g.withdrawn = 0

// SELECT * FROM `webl_games` WHERE reported_on LIKE '2007-10-04 %%:%%:%%' OR (reported_on >= ('2007-10-04' - interval 10 day) AND reported_on < '2007-10-04') order by reported_on

	$result = mysql_query("insert into temp_played (name, recently_played)
	(

	SELECT userid, count(*) as cnt 
	FROM
	(
	    SELECT winner as userid
	    from  $gamestable g 
	   where g.reported_on LIKE '". substr($rprow['reported_on'],0,10) ." %%:%%:%%'
OR (
g.reported_on >= ( ' ".substr($rprow['reported_on'],0,10) . "' - INTERVAL $passivedays
DAY )
AND reported_on < '". substr($rprow['reported_on'],0,10) ."'
)
	    UNION ALL 
	    SELECT loser as userid
	    from $gamestable g 
   where g.reported_on LIKE '". substr($rprow['reported_on'],0,10) ." %%:%%:%%'
OR (
g.reported_on >= ( '". substr($rprow['reported_on'],0,10) . "' - INTERVAL $passivedays 
DAY )
AND reported_on < '". substr($rprow['reported_on'],0,10) ."'
)
	) t
	GROUP BY userid
	HAVING COUNT(*) >= $MySQLgamestobeactive
	);");
	
}


if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
} else { if (isset($_POST['recache'])){echo "<br>2. Calculated amount of recently played games & put them into temp table... [ ". TimerOff() . " sec ]"; }}



// 3. Now we have the results, let's update the cahce table and the inumber of most recent played games by every active player. We do that by copying the results we have in the temp table and putting them into the corresponding player in the cach table.

TimerOn();
$result = mysql_query("UPDATE temp_played p, $standingscachetable pp
SET pp.recently_played = p.recently_played
WHERE pp.name = p.name");

if (!$result) {
    $message  = '<br><br>Invalid query: ' . mysql_error() . "\n\n";
    $message .= 'Whole query: ' . $query;
    die($message);
} else { 

if (isset($_POST['recache'])){echo "<br>3. Transfered number of recently played games from temp table to cache table... [ ". TimerOff() . " sec ]";; }}


// Update the active players by adding their ranking into the table... 
TimerOn();

$result = mysql_query("SET @r := 0;");

$result = mysql_query("UPDATE  
$standingscachetable 
SET rank = (@r := @r + 1)
WHERE recently_played >= 1 AND rating >= $ladderminelo AND games > $MySQLgamestorank
ORDER BY rating DESC, games DESC, wins ASC");

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
} else {
	
	if (isset($_POST['recache'])){ echo "<br>4. Updated ranks of active players in cache table. [ ". TimerOff() . " sec ]";}}

?>