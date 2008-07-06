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
        global $gamestable;

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
        foreach ($GLOBALS['kArray'] as $kRating => $kValue) {
            if ($rating >= $kRating) {
                return $kValue;
            }
        }
    }

    function CalcElo($playerRating, $opponentRating, $winState, $k, $protection) 
    {
        // Calculate the win expectancy of the player.
        $winExpectancy = 1/(1 + pow(10,($opponentRating - $playerRating)/400));

        if ($protection == true) {
            $protection = PROVISIONAL_PROTECTION;
        } else {
            $protection = 1;
        }

        // Calculate the new rating
        $ratingChange = ($k * ($winState - $winExpectancy)) / $protection;

        return round($ratingChange);
    }

    function RankGame($winner, $loser, $reportedTime, $draw = false)
    {
        $result = array();

        $loserStats = $this->GetRating($reportedTime, $loser);
        $winnerStats = $this->GetRating($reportedTime, $winner);

        // Set the scores for the players vs their expected results for this game.
        if ($draw === true) {
            $winnerState = 0.5;
            $loserState = 0.5;
        } else {
            $winnerState = 1;
            $loserState = 0;
        }

        if ($winnerStats['provisional'] || $loserStats['provisional']) {
            $provisional = true;
        } else {
            $provisional = false;
        }

        $kValWinner = $this->ChooseKVal($winnerStats['rating']);
        $result['winnerChange'] = $this->CalcElo($winnerStats['rating'], $loserStats['rating'], $winnerState, $kValWinner, $provisional);
        $result['winnerRating'] = $winnerStats['rating'];

        $kValLoser = $this->ChooseKVal($loserStats['rating']);
        $result['loserChange'] = $this->CalcElo($loserStats['rating'], $winnerStats['rating'], $loserState, $kValLoser, $provisional);
        $result['loserRating'] = $loserStats['rating'];

        if ($draw === true) {
            $result['winnerStreak'] = 0;
            $result['loserStreak'] = 0;
            $result['winnerWins'] = $winnerStats['wins'];
            $result['winnerLosses'] = $winnerStats['losses'];
            $result['loserWins'] = $loserStats['wins'];
            $result['loserLosses'] = $loserStats['losses'];
        } else {
            $drawnGame = 1;
            $result['winnerStreak'] = $winnerStats['streak'] < 0 ? 1 : $winnerStats['streak'] + 1;
            $result['loserStreak'] = $loserStats['streak'] > 0 ? -1 : $loserStats['streak'] - 1;
            $result['winnerWins'] = $winnerStats['wins'] + 1;
            $result['winnerLosses'] = $winnerStats['losses'];
            $result['loserWins'] = $loserStats['wins'];
            $result['loserLosses'] = $loserStats['losses'] + 1;
        }
        $result['winnerGames'] = $winnerStats['games'] + 1;
        $result['loserGames'] = $loserStats['games'] + 1;
        $result['reportedTime'] = $reportedTime;

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
                                       winner_games = ".$result['winnerGames'].",
                                       winner_wins = ".$result['winnerWins'].",
                                       winner_losses = ".$result['winnerLosses'].",
                                       winner_streak = ".$result['winnerStreak'].",
                                       loser_elo = ".$result['loserRating']." + ".$result['loserChange'].",
                                       loser_points = ".$result['loserChange'].",
                                       loser_games = ".$result['loserGames'].",
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
