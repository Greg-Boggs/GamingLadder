<?php
session_start();
require_once '../conf/variables.php';
// Checks if we have logged in, and will return the appropriate page if we haven't.  Therefor it may not return, it may exit.
$GLOBALS['prefix'] = "../";

require_once 'security.inc.php';
require('../top.php');

if (isset($_POST['rerank'])) {
    require_once '../include/elo.class.php';

    echo "<h2>Rerank the Ladder</h2>";
    $query = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable ORDER BY reported_on";
    $result = mysql_query($query) or die ("query failed");
    $elo = new Elo($db);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $winner = $row['winner'];
        $loser = $row['loser'];

        if (!$elo->RankGameInDB($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not report game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
        }
    }
    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
    mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);	

    echo "<p>The ladder has been reranked using the games table. Check the current <a href=\"../ladder.php\">ladder standings</a></p>";
} else if ($_POST['recache']) {
    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
    mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);	
    echo "<h2>Recache the Ladder</h2>";
    echo "<p>The ladder has been recached using the games table. Check the current <a href=\"../ladder.php\">ladder standings</a></p>";
} else {
?>
<form name="form1" method="post" action="rerankladder.php">
<h2>Rerank the Ladder</h2>
<p>
<b>Warning: this will rerank the entire ladder from the games history</b>
</p>
<p>
This should be used if there has been a corruption of elo or win/loss/streak information.
</p>
<input type="submit" name="rerank" value="Rerank Ladder" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text" onclick="return confirm('Are you sure you want to rerank the ladder?');">
<input type="submit" name="recache" value="Update corrupted cache" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text" onclick="return confirm('Are you sure you want to redo the cache?');">
</form>
<?php
}
require('../bottom.php');
?>
