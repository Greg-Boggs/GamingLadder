<?php
require_once('../conf/variables.php');

class Elo {

    private $dbConn;

    function Elo(&$db)
    {
        $this->dbConn = $db;
    }

    function GetRating($player)
    {
        global $playerstable;
        $query  = "SELECT rating, games FROM $playerstable WHERE name = '$player'";

        $result = mysql_query($query, $this->dbConn);

        if (!$result) {
           return false;
        }
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $stats[0] = $row['rating'];
        if ($stats[0] == null) {
            return null;
        }

        if ($row['games'] < PROVISIONAL) {
            $protection = PROVISIONAL - $row['games'] - 1; // The Current game hasn't yet been deducted from the provisional total
            $stats[1] = true;
        } else {
            $stats[1] = false;
        }
        return $stats;
    }

    function ReportGame($winner, $loser, $date)
    {
        global $playerstable;

        if ($this->RankGame($winner, $loser) === false) {
            // Something went wrong with the ranking process
            return false;
        }
        $sql = "UPDATE $playerstable SET losses = losses + 1, games = games + 1, streakwins = 0, " .
               "streaklosses = streaklosses + 1, LastGame = '$date - Loss vs $winner' WHERE name='$loser'";
        $result = mysql_query($sql, $this->dbConn);

        $sql = "UPDATE $playerstable SET wins = wins + 1, games = games + 1, streakwins = streakwins + 1, ".
               "streaklosses = 0, LastGame = '$date - Win vs $loser' WHERE name = '$winner'";
        $result = mysql_query($sql, $this->dbConn);

        return true;
    }

    function updateRating ($player, $rating)
    {
        global $playerstable;
        $query = "UPDATE $playerstable SET rating = $rating WHERE name = '$player'";
        return mysql_query($query, $this->dbConn);
    }

    function ChooseKVal($rating) 
    {
        if ($rating < BOTTOM_RATING) {
            return BOTTOM_K_VAL;
        } else if($rating < MIDDLE_RATING) {
            return MIDDLE_K_VAL;
        } else {
            return TOP_K_VAL;
        }
    }

    function CalcElo($loserRating, $winnerRating, $k, $forLoser, $provisional) 
    {
        if($winnerRating < $loserRating) {
            $rw1 = $winnerRating - $loserRating;
            $rw2 = -$rw1/400;
        } else {
            $rw1 = $loserRating - $winnerRating;
            $rw2 = $rw1/400;
        }

        if ($rw1 > MAX_DIFFERENCE || $rw1 < -MAX_DIFFERENCE) {    
            return null;
        }

        $rw3 = pow(10, $rw2);
        $rw4 = $rw3 + 1;
        $rw5 = 1/$rw4;
        $rw6 = 1 - $rw5;
        $rw7 = $k * $rw6;

        if (PROVISIONAL_PROTECTION != 0) {
            if ($provisional == true) {
                $rw7 = $rw7/PROVISIONAL_PROTECTION;
            }
        }
        if ($forLoser == true) {
            return -round($rw7);
        } else {
            return round($rw7);
        }
    }

    function RankGame($winner, $loser)
    {
        $loserStats = $this->GetRating($loser);
        $winnerStats = $this->GetRating($winner);
        if ($winnerStats[1] || $loserStats[1]) {
            $provisional = true;
        } else {
            $provisional = false;
        }

        $kVal = $this->ChooseKVal($winnerStats[0]);
        $winnerChange = $this->CalcElo($loserStats[0], $winnerStats[0], $kVal, false, $provisional);

        $kVal = $this->ChooseKVal($loserStats[0]);
        $loserChange = $this->CalcElo($loserStats[0], $winnerStats[0], $kVal, true, $newbie);

        $winnerRating = $winnerStats[0] + $winnerChange;
        $loserRating = $loserStats[0] + $loserChange;
        $this->updateRating($winner, $winnerRating);
        $this->updateRating($loser, $loserRating);
    }
}

?>
