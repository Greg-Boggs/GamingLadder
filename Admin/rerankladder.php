<?php
session_start();
require_once '../conf/variables.php';
// Checks if we have logged in, and will return the appropriate page if we haven't.  Therefor it may not return, it may exit.
$GLOBALS['prefix'] = "../";
require('security.inc.php');

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

        if (!$elo->RankGame($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not report game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
        }
    }

    echo "<p>The ladder has been reranked using the games table. Check the current <a href=\"../ladder.php\">ladder standings</a></p>";
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
</form>
<?php
}
require('../bottom.php');
?>
