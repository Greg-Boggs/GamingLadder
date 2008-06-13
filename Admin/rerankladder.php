<?php
session_start();
echo "session: ". $_SESSION['username'];

require('../conf/variables.php');

//Replace with admin login
//require('ladder_cookie.inc.php');

require('../top.php');

if ( isset($_SESSION['username']) ) {

if ( isset ($_POST['rerank']) ) {

$sql = "UPDATE $playerstable SET wins = 0, losses = 0, games = 0, streakwins = 0, " .
"streaklosses = 0, LastGame = null, provisional = 1, rating = ". BASE_RATING ;
$result = mysql_query($sql) or die("reset failed");

echo "Reranking the Ladder<br>";
$query  = "SELECT winner, loser, game_id, date " . 
	  "FROM $gamestable " .
	  "ORDER BY game_id ";
//echo $query;
$resultr = mysql_query($query) or die ("query failed");
while($row = mysql_fetch_array($resultr, MYSQL_ASSOC))
{
    $winner = $row['winner'];
    $loser = $row['loser'];
    //echo "winner: $winner , loser: $loser <br>";

    $loserStats = GetRating($loser, $playerstable);
    $winnerStats = GetRating($winner, $playerstable);
    if ( $winnerStats[1] || $loserStats[1] ) {
	$newbie = true;
    } else { $newbie = false; }
    $kVal = ChooseKVal($winnerStats[0]);
    //echo "Winner Kval: $kVal<br/>"; 
    $winnerChange = CalcElo($loserStats[0], $winnerStats[0], $kVal, false, $newbie);
    //echo "Elo Change: $winnerChange<br>";
    $kVal = ChooseKVal($loserStats[0]);
    //echo "Loser Kval: $kVal<br/>";
    $loserChange = CalcElo($loserStats[0], $winnerStats[0], $kVal, true, $newbie);
    $winnerRating = $winnerStats[0]+$winnerChange;
    $loserRating = $loserStats[0]+$loserChange;
    updateRating($winner, $winnerRating, $playerstable);
    updateRating($loser, $loserRating, $playerstable);

	//$sql = "UPDATE $gamestable (elo_change) VALUES ('$winnerChange') WHERE game_id = $row['game_id']";
	//echo "game: $sql <br/>";
        //$result = mysql_query($sql) or die ("failed to update elo change");

$date = $row[date];
//echo $date;
$sql = "UPDATE $playerstable SET losses = losses + 1, games = games + 1, streakwins = 0, " .
"streaklosses = streaklosses + 1, LastGame = '$date - Loss vs $winner' WHERE name='$loser'";
//echo "loser: $sql <br/>";
$result = mysql_query($sql) or die("update loser failed");
 
$sql = "UPDATE $playerstable SET wins = wins + 1, games = games + 1, streakwins = streakwins + 1, ".
"streaklosses = 0, LastGame = '$date - Win vs $loser' WHERE name = '$winner'";
//echo "winner: $sql <br/>";
$result = mysql_query($sql) or die("update winner failed");
}
echo "The ladder has been reranked using the games table. Check the current <a href=\"../ladder.php\">ladder standings</a>";

} else {
?>
<p>
<form name="form1" method="post" >
<h3>Rerank the Ladder</h3>

<b>Warning: this will rerank the entire ladder from the games history<br/>
This should be used if there has been a corruption of elo or win/loss/streak information.<br/>

<input type="submit" name="rerank" value="Rerank Ladder" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text" onclick="return confirm('Are you sure you want to rerank the ladder?');">
</form>
</p>
<?php
require('../bottom.php');
}
}

function GetRating($player, $playerstable) {

    $query  = "SELECT rating, games " . 
	  "FROM $playerstable ". 
	  "WHERE name = '$player'";
    //echo $query;
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC) or die("failed to select $player");
    $stats[0] = $row['rating'];
    if ($stats[0] == null) { echo "<a href='report.php'>Go back.</a>";
    require('bottom.php');
    exit;
    }
    if ($row['games'] < PROVISIONAL) {
	$protection = PROVISIONAL - $row['games']-1; //current game hasn't yet been deducted.
	//echo "One of you is provisional. The protection ends in $protection more games.<br>";
	$stats[1] = true;
    } else {
	$sql = "UPDATE $playerstable SET provisional = 0 WHERE name = '$player'";
	//echo $sql;
	mysql_query($sql) or die('Failed to update provisional');
	$stats[1] = false;
    }
    return $stats;
}

function updateRating ($player, $rating, $playerstable) {
    $query = "UPDATE $playerstable SET rating = $rating " . 
	"WHERE name = '$player'";
    //echo $query;
    mysql_query($query) or die('Error, update query failed');
}

function ChooseKVal($rating) {
    if ($rating < BOTTOM_RATING)
        return BOTTOM_K_VAL;
    else if($rating < MIDDLE_RATING)
        return MIDDLE_K_VAL;
    else
	return TOP_K_VAL;
}

function CalcElo($loserRating, $winnerRating, $k, $forLoser, $provisional) 
{
    //echo "Ratings: $loserRating $winnerRating <br/>";
    if($winnerRating < $loserRating)
    {
        $rw1 = $winnerRating - $loserRating;
        $rw2 = -$rw1/400;
    }
    else
    {
        $rw1 = $loserRating - $winnerRating;
        $rw2 = $rw1/400;
    }	
    if ($rw1 > MAX_DIFFERENCE || $rw1 < -MAX_DIFFERENCE) {    
	echo "Skill difference: $rw1 is to great Skipping game. <BR>";
        return 0;
    }
    $rw3 = pow(10,$rw2);
    $rw4 = $rw3 + 1;
    $rw5 = 1/$rw4;
    $rw6 = 1 - $rw5;
    $rw7 = $k * $rw6;
    if (PROVISIONAL_PROTECTION != 0)
    	if ($provisional == true)
	    $rw7 = $rw7/PROVISIONAL_PROTECTION;
    if ($forLoser == true)
        return -round($rw7);
    else			
        return round($rw7);
}

?>


