<?php
// It is implied that variables.php is included before this class is included or used.

// WARNING: This variant is really glicko-2
class Elo
{

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
                 CASE when winner = '$player' THEN winner_streak ELSE loser_streak END as streak,
                 CASE when winner = '$player' THEN winner_deviation ELSE loser_deviation END as deviation,
                 CASE when winner = '$player' THEN winner_volatility ELSE loser_volatility END as volatility,
              --   (SELECT count(*) from $gamestable gt WHERE gt.reported_on > $gamestable.reported_on AND gt.reported_on < '" . $reportTime . "' ) as lastgamedays
                 (SELECT to_days('$reportTime') - to_days(gt.reported_on) from $gamestable gt WHERE winner = '$player' or loser = '$player' ORDER BY reported_on LIMIT 1) as lastgamedays
                 FROM $gamestable
                 WHERE reported_on < '" . $reportTime . "' AND contested_by_loser = 0 AND withdrawn = 0
                 AND (winner = '$player' OR loser = '$player')
                 ORDER BY reported_on DESC LIMIT 1";

        $result = mysqli_query($this->dbConn, $sql);

        if (!$result) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            $row['rating'] = 1500;
            $row['games'] = 0;
            $row['wins'] = 0;
            $row['losses'] = 0;
            $row['streak'] = 0;
            $row['deviation'] = 350;
            $row['volatility'] = 0.06;
            $row['lastgamedays'] = 0;
        } else {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
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

        $reportTime = date('Y-m-d H:i:s');

        if ($draw === true) {
            $insertDraw = 'true';
        } else {
            $insertDraw = 'false';
        }
        // We add a new game with all the details we know up to the point of this game being reported.
        if ($replay == null) {
            $replay = "NULL";
        } else {
            $replay = "'" . mysqli_real_escape_string($replay, $this->dbConn) . "'";
        }

        $sql = "INSERT INTO $gamestable (winner, loser, reported_on, contested_by_loser, draw, withdrawn, replay)
                VALUES ('$winner', '$loser', '$reportTime', 0, " . $insertDraw . ", 0," . $replay . ")";

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

    // These are two supporting function for the CalcGlicko.
    // g and E are using the calculation expressions and as they are complicated
    // I have attempted to keep the same names so you can check my code.
    function g($thy)
    {
        return 1 / sqrt(1 + ((3 * $thy * $thy) / (M_PI * M_PI)));
    }

    function E($u, $ux, $thy)
    {
        return 1 / (1 + exp(-1 * $this->g($thy) * ($u - $ux)));
    }

    /**
     * $player is an array including, ranking, volatility, deviation
     * $opponent is the same as $player
     */
    function CalcGlicko($player, $opponent, $gameScore)
    {
        $r = $player['rating'];
        $ratingDeviation = $player['deviation']; // RD
        $ratingVolatility = $player['volatility']; // This is thetor in the forumle

        $opponentRating = $opponent['rating'];
        $opponentRatingDeviation = $opponent['deviation'];

        // Tor is a game constant.  Should be a constant in the conf file
        $tor = 0.5; // tor

        // Convert the player details into glicko for updating
        $meu = ($r - 1500) / 173.7178;
        $thy = $ratingDeviation / 173.7178;

        // Opponent Information into glicko
        $ux = ($opponentRating - 1500) / 173.7178;
        $thyx = $opponentRatingDeviation / 173.7178;

        // ratingDeviation increases over time when a player
        // Plays a game, increase the deviation.
        // Currently we use 1 days as a rating period as there aren't a lot of games.
        for ($i = 0; $i < $player['lastgamedays']; ++$i) {
            $thy = sqrt(pow($thy, 2) + pow($ratingVolatility, 2));
        }
        // If the deviation gets out of control, put it back to be beginner value.
        if ($thy * 1737178 > 350) {
            $thy = 350 / 173.7178;
        }
        for ($i = 0; $i < $opponent['lastgamedays']; ++$i) {
            $thyx = sqrt(pow($thyx, 2) + pow($opponent['volatility'], 2));
        }

        $funcOfE = $this->E($meu, $ux, $thyx);
        $funcOfgOpponent = $this->g($thyx);

        $v = pow($funcOfgOpponent, 2) * $funcOfE * (1 - $funcOfE);
        $v = 1 / $v;

        $delta = $v * $funcOfgOpponent * ($gameScore - $funcOfE);

        $a = log(pow($ratingVolatility, 2));
        $calculated = $a;

        // The following should converge quickly, we don't have trial time so we are limiting the converence to 25 loops.
        $i = 0;
        do {
            $stored = $calculated;
            $d = pow($thy, 2) + $v + exp($stored);
            $h1 = -($stored - $a) / pow($tor, 2) - 0.5 * exp($stored) / $d + 0.5 * exp($stored) * pow($delta / $d, 2);
            $h2 = -1 / pow($tor, 2) - 0.5 * exp($stored) * (pow($thy, 2) + $v) / pow($d, 2) + 0.5 * pow($delta, 2) * exp($stored) * (pow($thy, 2) + $v - exp($stored)) / pow($d, 3);

            $calculated = $stored - $h1 / $h2;
            ++$i;
        } while ($stored != $calculated && $i < 25);
        echo $stored . " " . $calculated . "<br />";
        $newRatingVolatility = exp($stored / 2);
        $thyStar = sqrt(pow($thy, 2) + pow($newRatingVolatility, 2));

        $newThy = 1 / (sqrt(1 / pow($thyStar, 2) + 1 / $v));
        $newMeu = $meu + pow($newThy, 2) * $funcOfgOpponent * ($gameScore - $funcOfE);

        // Convert rating back to something people understand
        $newRating = 173.7178 * $newMeu + 1500;
        $newRatingDeviation = 173.7178 * $newThy;

        $returnPlayer['rating'] = round($newRating, 0);
        $returnPlayer['deviation'] = round($newRatingDeviation, 0);
        $returnPlayer['volatility'] = $newRatingVolatility;

        return $returnPlayer;
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

        $newStats = $this->CalcGlicko($winnerStats, $loserStats, $winnerState);
        $result['winnerChange'] = $newStats['rating'] - $winnerStats['rating'];
        $result['winnerRating'] = $newStats['rating'];
        $result['winnerDeviation'] = $newStats['deviation'];
        $result['winnerVolatility'] = $newStats['volatility'];

        $newStats = $this->CalcGlicko($loserStats, $winnerStats, $loserState);
        $result['loserChange'] = $newStats['rating'] - $loserStats['rating'];
        $result['loserRating'] = $newStats['rating'];
        $result['loserDeviation'] = $newStats['deviation'];
        $result['loserVolatility'] = $newStats['volatility'];

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
                                       loser_streak = " . $result['loserStreak'] . ",
                                       winner_deviation = " . $result['winnerDeviation'] . ",
                                       winner_volatility = " . $result['winnerVolatility'] . ",
                                       loser_deviation = " . $result['loserDeviation'] . ",
                                       loser_volatility = " . $result['loserVolatility'] . "

                       WHERE reported_on = '$reportedTime'";

        if (mysqli_query($this->dbConn, $sql)) {
            return $result;
        } else {
            return false;
        }
    }
}

?>
