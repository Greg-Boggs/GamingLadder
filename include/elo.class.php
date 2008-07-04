<?php
// It is implied that variables.php is included before this class is included or used.

class Elo {

    private $dbConn;

    function Elo(&$db)
    {
        $this->dbConn = $db;
    }

    function GetRating($reportTime, $player)
    {
        global $gamestable;
        $sql = "SELECT CASE WHEN winner = '$player' THEN winner_elo ELSE loser_elo END as rating,
                 CASE when winner = '$player' THEN winner_games ELSE loser_games END as games,
                 CASE when winner = '$player' THEN winner_wins ELSE loser_wins END as wins,
                 CASE when winner = '$player' THEN winner_losses ELSE loser_losses END as losses,
                 CASE when winner = '$player' THEN winner_streak ELSE loser_streak END as streak
                 FROM $gamestable
                 WHERE reported_on < '".$reportTime."' AND contested_by_loser = 0 AND withdrawn = 0
                 AND (winner = '$player' OR loser = '$player')
                 ORDER BY reported_on DESC LIMIT 1";

        $result = mysql_query($sql, $this->dbConn);

        if (!$result) {
           return false;
        }
        if (mysql_num_rows($result) == 0) {
            $row['rating'] = BASE_RATING;
			$row['games'] = 0;
			$row['wins'] = 0;
			$row['losses'] = 0;
			$row['streak'] = 0;
        } else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
        }

        if ($row['games'] < PROVISIONAL) {
            $row['provisional'] = true;
        } else {
            $row['provisional'] = false;
        }
        return $row;
    }

    function ReportNewGame($winner, $loser, $draw = false, $replay = NULL)
    {
        global $gamestable, $reportTime;

        $reportTime = date('Y-m-d h:i:s'); 

        if ($draw === true) {
            $insertDraw = 'true';
        } else {
            $insertDraw = 'false';
        }
        // We add a new game with all the details we know up to the point of this game being reported.
        if ($replay == null) {
            $replay = "NULL";
        } else {
            $replay = "'".mysql_real_escape_string($replay, $this->dbConn)."'";
        }

        $sql = "INSERT INTO $gamestable (winner, loser, reported_on, contested_by_loser, draw, withdrawn, replay)
                VALUES ('$winner', '$loser', '$reportTime', 0, ".$insertDraw.", 0,".$replay.")";

        $result = mysql_query($sql, $this->dbConn);

        if (!$result) {
            return false;
        }

        $result = $this->RankGameInDB($winner, $loser, $reportTime, $draw);

        if (!$result) {
            $sql = "DELETE FROM $gamestable WHERE reported_on = '$reportTime'";
            mysql_query($sql);
            return false;
        }
        return $result;
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

    function RankGame($winner, $loser, $reportedTime, $draw = false)
    {
        $result = array();

        $loserStats = $this->GetRating($reportedTime, $loser);
        $winnerStats = $this->GetRating($reportedTime, $winner);

        if ($winnerStats['provisional'] || $loserStats['provisional']) {
            $provisional = true;
        } else {
            $provisional = false;
        }

        $kValWinner = $this->ChooseKVal($winnerStats['rating']);
        $result['winnerChange'] = $this->CalcElo($loserStats['rating'], $winnerStats['rating'], $kValWinner, false, $provisional);
        $result['winnerRating'] = $winnerStats['rating'];

        $kValLoser = $this->ChooseKVal($loserStats['rating']);
        $result['loserChange'] = $this->CalcElo($loserStats['rating'], $winnerStats['rating'], $kValLoser, true, $provisional);
        $result['loserRating'] = $loserStats['rating'];

        // At the moment, we count a draw as a half win, half loss.
        // Here we adjust our Change by if the result was the other way around.
        // $drawnGame allows us to only add to wins or losses when the game is not drawn
        if ($draw === true) {
            $result['winnerChange'] += $this->CalcElo($winnerStats['rating'], $loserStats['rating'], $kValWinner, true, $provisional);
            $result['winnerChange'] = intval($result['winnerChange']/2);
            $result['loserChange'] += $this->CalcElo($winnerStats['rating'], $loserStats['rating'], $kValLoser, false, $provisional);
            $result['loserChange'] = intval($result['loserChange']/2);
            $drawnGame = 0;
            $result['winnerStreak'] = 0;
            $result['loserStreak'] = 0;
            $result['winnerWins'] = $winnerStats['wins'];
            $result['winnerLosses'] = $winnerStats['losses'];
            $result['loserWins'] = $loserStats['wins'];
            $result['loserLosses'] = $loserStats['losses'];
            $result['winnerGames'] = $winnerStats['games'];
            $result['loserGames'] = $loserStats['games'];
        } else {
            $drawnGame = 1;
            $result['winnerStreak'] = $winnerStats['streak'] < 0 ? 1 : $winnerStats['streak'] + 1;
            $result['loserStreak'] = $loserStats['streak'] > 0 ? -1 : $loserStats['streak'] - 1;
            $result['winnerWins'] = $winnerStats['wins'] + 1;
            $result['winnerLosses'] = $winnerStats['losses'];
            $result['loserWins'] = $loserStats['wins'];
            $result['loserLosses'] = $loserStats['losses'] + 1;
            $result['winnerGames'] = $winnerStats['games'];
            $result['loserGames'] = $loserStats['games'];
        }
        return $result;
    }

    function RankGameInDB($winner, $loser, $reportedTime, $draw = false)
    {
        global $playerstable, $gamestable;

        $result = $this->RankGame($winner, $loser, $reportedTime, $draw);
        // Add the ratings into the game information
        // We also use this opportunity to insert the initial rating if it doesn't exist.
        $sql = "UPDATE $gamestable SET winner_elo = ".$result['winnerRating']." + ".$result['winnerChange'].",
                                       winner_points = ".$result['winnerChange'].",
                                       winner_games = ".$result['winnerGames']." + 1,
                                       winner_wins = ".$result['winnerWins'].",
                                       winner_losses = ".$result['winnerLosses'].",
                                       winner_streak = ".$result['winnerStreak'].",
                                       loser_elo = ".$result['loserRating']." + ".$result['loserChange'].",
                                       loser_points = ".$result['loserChange'].",
                                       loser_games = ".$result['loserGames']." + 1,
                                       loser_wins = ".$result['loserWins'].",
                                       loser_losses = ".$result['loserLosses'].",
                                       loser_streak = ".$result['loserStreak']."
                       WHERE reported_on = '$reportedTime'";

        if (mysql_query($sql, $this->dbConn)) {
           return $result;
        } else {
           return false;
        }
    }
}

?>
