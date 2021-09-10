<?php
// It is implied that variables.php is included before this class is included or used.

class Elo
{

    private $dbConn;

    public function __construct(&$db)
    {
        $this->dbConn = $db;
    }

    public function GetRating($reportTime, $player)
    {
        global $gamestable;
        $sql = "SELECT CASE WHEN winner = '$player' THEN winner_elo ELSE loser_elo END as rating,
                 CASE when winner = '$player' THEN winner_games ELSE loser_games END as games,
                 CASE when winner = '$player' THEN winner_wins ELSE loser_wins END as wins,
                 CASE when winner = '$player' THEN winner_losses ELSE loser_losses END as losses,
                 CASE when winner = '$player' THEN winner_streak ELSE loser_streak END as streak
                 FROM $gamestable
                 WHERE reported_on < '" . $reportTime . "' AND contested_by_loser = 0 AND withdrawn = 0
                 AND (winner = '$player' OR loser = '$player')
                 ORDER BY reported_on DESC LIMIT 1";

        $result = mysqli_query($this->dbConn, $sql);

        if (!$result) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            $row['rating'] = BASE_RATING;
            $row['games'] = 0;
            $row['wins'] = 0;
            $row['losses'] = 0;
            $row['streak'] = 0;
        } else {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        }

        if (($row['games'] <= PROVISIONAL) && (PROVISIONAL_SYSTEM > 0)) {
            $row['provisional'] = true;
        } else {
            $row['provisional'] = false;
        }
        return $row;
    }

    function ReportNewGame($winner, $loser, $draw = false, $replay = NULL, $faction1, $faction2)
    {
        global $gamestable;

        $reportTime = date('Y-m-d H:i:s');

        if ($draw === true) {
            $insertDraw = 'true';
        } else {
            $insertDraw = 'false';
        }

        $sql = "INSERT INTO $gamestable (winner, loser, faction1, faction2, reported_on, contested_by_loser, draw, withdrawn)
                VALUES ('$winner', '$loser', '$faction1', '$faction2', '$reportTime', 0, " . $insertDraw . ", 0)";

        $result = mysqli_query($this->dbConn, $sql);

        if (!$result) {
            return false;
        }

        $result = $this->RankGameInDB($winner, $loser, $reportTime, $draw);

        if (!$result) {
            $sql = "DELETE FROM $gamestable WHERE reported_on = '$reportTime'";
            mysqli_query($this->dbConn, $sql);
            return false;
        }
        return $result;
    }

    function ChooseKVal($rating, $provisional)
    {
        if (!$provisional) {
            $k = $GLOBALS['kArray'];
        } else {
            $k = $GLOBALS['kArrayProvisional'];
        }

        foreach ($k as $kRating => $kValue) {
            if ($rating >= $kRating) {
                return $kValue;
            }
        }
    }

    function CalcElo($playerRating, $opponentRating, $winState, $k, $protection)
    {
        // Calculate the win expectancy of the player.
        $winExpectancy = 1 / (1 + pow(10, ($opponentRating - $playerRating) / ELO_DIVIDE_FACTOR));

        if ($protection == true) {
            $protection = PROVISIONAL_PROTECTION;
            //echo "2. $ protection is true and set to:  $protection";
        } else {
            $protection = 1;
        }

        If ((PROVISIONAL_SYSTEM == 1) || (PROVISIONAL_SYSTEM == 2)) {
            // Calculate the new rating when one of the prov systems are in place.
            $ratingChange = ($k * ($winState - $winExpectancy)) / $protection;
        }

        if (PROVISIONAL_SYSTEM == 0) {
            // Let's allow people to turn off prov systems as well...
            $ratingChange = ($k * ($winState - $winExpectancy));
        }


        return round($ratingChange);
    }

    function ApplyAntiMatchspam($k)
    {
        global $recentgames;
//        print_r($recentgames);
        // Apply the anti-matchspam penalty to the (winner's) k value and return it
        if ($GLOBALS['recentgames'] < ANTI_MATCHSPAM_NUMGAMES) return $k;

        switch (ANTI_MATCHSPAM_METHOD) {

            case 0: // Don't punish match spam
            case 1: // Games cap; implemented in report.php
            default: // No config?
                return $k;

            case 2: // If played more games than cap, reduce k * fixed value
                return $k * ANTI_MATCHSPAM_FACTOR;

            case 3: // reduce k by dynamic value
                $excess = $GLOBALS['recentgames'] - ANTI_MATCHSPAM_NUMGAMES;
                return $k * pow(2, -$excess / (ANTI_MATCHSPAM_FACTOR * 10));
        }
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


        if (PROVISIONAL_SYSTEM == 1) {
            // Only protect non-provisional players to help new players find their rank faster
            if ($winnerStats['provisional'] && $loserStats['provisional']) {
                $loserProtection = false;
                $winnerProtection = false;
                //echo "<br>System  1: Nobody is protected, loserProtection = false, winnerProtection = false.";
            } else if ($winnerStats['provisional']) {
                $winnerProtection = false;
                $loserProtection = true;
                //echo "<br>System  1: Loser is protected, loserProtection = true";
            } /*
	  // At this time, if the non-provisional player wins, they get the normal points.
	  // If you are too far away from a provisional player, you won't get many points anyway. If you
	  // are close, you will push the provisional player away a long way.  So you don't want to play
	  // them too many times.
		   } else if ($loserStats['provisional']) {
			  $winnerProtection = true;
			  $loserProtection = false;
	*/
            else {
                $winnerProtection = false;
                $loserProtection = false;
                // echo "<br>System 1: Both loser and winner protectuib = false, nobody is protected.";
            }
        }


        if ((PROVISIONAL_SYSTEM == 2) && ($winnerStats['provisional'] || $loserStats['provisional'])) {
            // eyerouges version of the provisional thing...it looks crude but eye has faith ; )
            $loserProtection = true;
            $winnerProtection = true;
            // echo "<br>System 2: Both loser and winner protection hgas been set to true. Both are protected.<br> winnerstatsProvisional = ".$winnerStats['provisional']. "<br>loserStatsProvisional = ". $loserStats['provisional'];
        }

        if (PROVISIONAL_SYSTEM == 0) {
            // No provisional system is being used
            $loserProtection = false;
            $winnerProtection = false;
            //echo "<br><b>Provisonal systems are offline.</b>";
        }

        $kValWinner = $this->ApplyAntiMatchspam($this->ChooseKVal($winnerStats['rating'], $winnerStats['provisional']));
        $result['winnerChange'] = $this->CalcElo($winnerStats['rating'], $loserStats['rating'], $winnerState, $kValWinner, $winnerProtection);
        $result['winnerRating'] = $winnerStats['rating'];

        $kValLoser = $this->ChooseKVal($loserStats['rating'], $loserStats['provisional']);
        $result['loserChange'] = $this->CalcElo($loserStats['rating'], $winnerStats['rating'], $loserState, $kValLoser, $loserProtection);
        $result['loserRating'] = $loserStats['rating'];


        // Now we have the ratings and all calculated, so it's a good time to do a final adjustment of them with the harcap/smoothcap modifiers if they are enabled in the config.

        // First the bottom hardcap one.
        // If the cap system is enabled and winners rating is bigger than losers then check how much bigger it is and set the points earned accordingly
        if ((ENABLE_MAX_DIFFERENCE_SYSTEM == 1 || ENABLE_MAX_DIFFERENCE_SYSTEM == 2) && ($result['winnerRating'] > $result['loserRating']) && (($result['winnerRating'] - $result['loserRating']) >= HARDCAP_BOTTOM_RATING_DIFFERENCE)) {

            $result['winnerChange'] = HARDCAP_BOTTOM_RATING_DIFFERENCE_POINTS;

            // Now we check for the secondary limit....if its been reached then another point can be rewarded...

            if (($result['winnerRating'] > $result['loserRating']) && (($result['winnerRating'] - $result['loserRating']) >= HARDCAP_SECONDARY_LIMIT)) {
                $result['winnerChange'] = HARDCAP_SECONDARY_LIMIT_POINTS;
            }

        }

        // Then the top hardcap..
        // Like the previous but on the other end of the line
        if ((ENABLE_MAX_DIFFERENCE_SYSTEM == 2) && ($result['winnerRating'] < $result['loserRating']) && (($result['loserRating'] - $result['winnerRating']) >= HARDCAP_TOP_RATING_DIFFERENCE)) {

            $result['winnerChange'] = HARDCAP_TOP_RATING_DIFFERENCE_POINTS;

            if (($result['winnerRating'] < $result['loserRating']) && (($result['loserRating'] - $result['winnerRating']) >= HARDCAP_SECONDARY_LIMIT)) {
                $result['winnerChange'] = HARDCAP_SECONDARY_LIMIT_POINTS;
            }
        }

        //The last type of caps, the 3:d one:
        if ((ENABLE_MAX_DIFFERENCE_SYSTEM == 3) && ((($result['winnerRating'] - $result['loserRating']) >= HARDCAP_SYS3_RATING_DIFFERENCE) || ((($result['winnerRating'] - $result['loserRating']) * -1) >= HARDCAP_SYS3_RATING_DIFFERENCE))) {

            $result['winnerChange'] = HARDCAP_SYS3_POINTS;
            $result['loserChange'] = HARDCAP_SYS3_POINTS;

            if ((($result['winnerRating'] - $result['loserRating']) >= HARDCAP_SECONDARY_LIMIT) || ((($result['winnerRating'] - $result['loserRating']) * -1) >= HARDCAP_SECONDARY_LIMIT)) {

                $result['winnerChange'] = HARDCAP_SECONDARY_LIMIT_POINTS;
                $result['loserChange'] = HARDCAP_SECONDARY_LIMIT_POINTS;
            }

        }


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
        $sql = "UPDATE $gamestable SET winner_elo = " . $result['winnerRating'] . " + " . $result['winnerChange'] . ",
                                       winner_points = " . $result['winnerChange'] . ",
                                       winner_games = " . $result['winnerGames'] . ",
                                       winner_wins = " . $result['winnerWins'] . ",
                                       winner_losses = " . $result['winnerLosses'] . ",
                                       winner_streak = " . $result['winnerStreak'] . ",
                                       loser_elo = " . $result['loserRating'] . " + " . $result['loserChange'] . ",
                                       loser_points = " . $result['loserChange'] . ",
                                       loser_games = " . $result['loserGames'] . ",
                                       loser_wins = " . $result['loserWins'] . ",
                                       loser_losses = " . $result['loserLosses'] . ",
                                       loser_streak = " . $result['loserStreak'] . "
                       WHERE reported_on = '$reportedTime'";

        if (mysqli_query($this->dbConn, $sql)) {
            return $result;
        } else {
            return false;
        }
    }
}

?>
