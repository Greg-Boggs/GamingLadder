<?php
session_start();
require 'autologin.inc.php';
require 'logincheck.inc.php';
$winner_name = $_SESSION['username'];
require 'top.php';
?>
<h2>Undo Game</h2>
<?php
if ( isset($_POST['submit']) ) {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE `winner` LIKE '$winner_name' AND withdrawn = 0 " .
		"ORDER BY reported_on DESC " .
		"LIMIT 0 , 1" ;

	$result = mysql_query($sql) or die("failed to select last game");
	$row = mysql_fetch_array($result);
	$last_game = $row['reported_on'];

	$sql = "UPDATE $gamestable SET withdrawn = 1 WHERE reported_on = '".$row['reported_on']."'";

	$result = mysql_query($sql) or die("failed to remove the last game");

    // Rerank the ladder from the deleted game upwards
    require_once 'include/elo.class.php';

    $query = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable WHERE reported_on > '".$row['reported_on']."' ORDER BY reported_on";
    $result = mysql_query($query) or die ("query failed");
    $elo = new Elo($db);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $winner = $row['winner'];
        $loser = $row['loser'];

        if (!$elo->RankGame($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not rerank game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
        }
    }

    echo "<p>The ladder has been reranked after your game deletion. If there were errors, please <a href=\"contactus.php\">contact us</a>. Check the current <a href=\"ladder.php\">ladder standings</a></p>";
} else {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE `winner` = '$winner_name' AND withdrawn = 0 " .
		"ORDER BY reported_on DESC " .
		"LIMIT 0 , 1" ;

	$result = mysql_query($sql) or die("failed to select last game");
	$row = mysql_fetch_array($result);

	echo "Undo last game: ".$row['winner']." vs ".$row['loser']." on ".$row['reported_on']."? ";
?>
<form method="post">
<input type="submit" value="Undo Game" name="submit">
</form>

<?php
	include ("bottom.php");
}
?>
