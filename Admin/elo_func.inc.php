<?php
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
	echo "One of you is provisional. The protection ends in $protection more games.<br>";
	$stats[1] = true;
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
    	if ($provisional)
	    $rw7 = $rw7/PROVISIONAL_PROTECTION;
    if ($forLoser == true)
        return -round($rw7);
    else			
        return round($rw7);
}
?>