<?php
session_start();
require 'autologin.inc.php';
require 'logincheck.inc.php';
$winner_name = $_SESSION['username'];
require 'top.php';
?>
<h2>Restore Game</h2>
<?php
// Are we restoring a contested by loser, or a withdrawn game.  At the moment, we only have support for withdrawn games in the GUI.
if (isset($_POST['submit'])) {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE reported_on='".$_GET['reported_on']."' ";

	$result = mysql_query($sql) or die("failed to select game to restore");
	$row = mysql_fetch_array($result);

    // Select if we are the winner or loser and undo the appropriate flag for the game
    if ($row['winner'] == $_SESSION['username']) {
	    $sql = "UPDATE $gamestable SET withdrawn = 0 WHERE reported_on = '".$row['reported_on']."'";
    } else if ($row['loser'] == $_SESSION['username']) {
	    $sql = "UPDATE $gamestable SET contested_by_loser = 0 WHERE reported_on = '".$row['reported_on']."'";
    }

	$result = mysql_query($sql) or die("failed to restore game");

    // Rerank the ladder from the deleted game upwards
    require_once 'include/elo.class.php';

    $query = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable WHERE reported_on > '".$row['reported_on']."' ORDER BY reported_on";
    $result = mysql_query($query) or die ("query failed");
    $elo = new Elo($db);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $winner = $row['winner'];
        $loser = $row['loser'];

        if (!$elo->RankGameInDB($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not rerank game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
        }
    }
    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
    mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);
	require_once 'include/morecachestandings.inc.php';       

    echo "<p>The ladder has been reranked after your game restoration. If there were errors, please <a href=\"contactus.php\">contact us</a>. Check the current <a href=\"ladder.php\">ladder standings</a> or return to your <a href='profile.php?name=".$_SESSION['username']."'>profile</a></p>";
} else {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE reported_on = '".$_GET['reported_on']."'";

	$result = mysql_query($sql) or die("failed to select last game");
	$row = mysql_fetch_array($result);

    echo "<p>You can only restore your agreement that the result is correct.  If you cannot restore this game and would like too, please contact the other player and ask them to restore the game from their perspective.</p>";
	echo "Restore Game: ".$row['winner']." vs ".$row['loser']." on ".$row['reported_on']."? ";
?>
<form method="post">
<input type="submit" value="Restore Game" name="submit">
</form>

<?php
	include ("bottom.php");
}
?>
