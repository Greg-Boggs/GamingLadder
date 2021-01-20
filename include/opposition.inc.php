<script type="text/javascript">
    $(document).ready(function () {
            $("#player").tablesorter({sortList: [[1, 0]], widgets: ['zebra']});
        }
    );
</script>

<?php

// Now we will create an Array containg the names of all the players different opponents. Once we get all the names we can then using them for plenty of intreresting stuff. There is probably an SQL way to do this smoother but I don't knoe it, and I also guss PHP gives us more freedom 

// To begin with, let's grab all games in the db where the user is involved

$sql = "SELECT * FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND (loser = '" . $_GET['name'] . "' OR winner = '" . $_GET['name'] . "')";
$result = mysqli_query($db, $sql);
$people = array();

// Now, we go through all those relevant games in the db, one by one, and checking if there is an opponent we havent played yet. If so, we add him to our list (array) of different opponents we've playd. If not, we won't add him since he's already on the list and we woudln't want a duplicate..

while ($bajs = mysqli_fetch_array($result)) {
    $foundaswinner = FALSE;
    $foundasloser = FALSE;


    foreach ($people as $name) {
        if ($bajs[0] == $name) {
            $foundaswinner = TRUE;
        }
        if ($bajs[1] == $name) {
            $foundasloser = TRUE;
        }
    }

    if ($foundaswinner == FALSE) {
        $people [] = $bajs[0];
    }
    if ($foundasloser == FALSE) {
        $people [] = $bajs[1];
    }

}

// That was that. We know have an array ($people) with all the different involved parties in our played games, including the name of the user himself, but that will later be left out. 

// Now let's display a fancy list of all opponents we've played. We do that by going through each entry in the array and outputting it.


// For each entry in the list we will want to look up some more info and then display it... Before starting doing that we'll call fetch info that doesn't change inrelation to which opponent we look at.

// Lets get the amount of games we've played in total.

$sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE  (loser = '$_GET[name]' OR winner = '$_GET[name]') AND contested_by_loser = '0' AND withdrawn = '0'");
$numberofplaydgames = mysqli_fetch_row($sql);

// "Total played games: $numberofplaydgames[0] 
echo "Different opponents: " . (count($people) - 1) . " || Average games/opponent: " . round(($numberofplaydgames[0] / (count($people) - 1)), 2);


// Create and set some variables.. yes yes.. I will gather them all in one array someday...

$numberlosses = array();
$sumofgames = array();
$percentof = array();
$opcurrentelo = array();
$loscore_elo = array();
$lost_points = array();
$won_points = array();
$gained_points = array();
$average_points = array();
$won_percent = array();
$lost_percent = array();
$inflation = array();
$gamesvsoverrated = array();
$gamesvsunderrated = array();
$gamesvsoverratedpercent = array();
$gamesvsunderratedpercent = array();

$z = 0;

// That was all the info we only need to get once. 
// Let's get all the stuff about every individual opponent and do it nicely within a loop:


foreach ($people as $name) {

    // Only time we wont want that is when it's the info if for the user himself.
    if (strtolower($name) != strtolower($_GET['name'])) {

        // So now we have the name of the oppoenent we want to look up. Let's look up some basic info that could be nice to know about him in order to make this tabel usefull.

        // For starters we'll want to know how many games in total we have played against him. Let's count the number of wins and losses against him, and then add it. That way we'll get 3 different numbers with just sql 2 calls. First the amounts of game the user lost against him

        $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE  loser = '$_GET[name]' AND contested_by_loser = '0' AND withdrawn = '0' AND winner = '$name'");
        $x = mysqli_fetch_row($sql); // We could have created a deeper $people-array instead of using several arrays, but I personally like this better as I'm no coder & get confused already..

        $numberlosses[] = $x[0];

        // Now we'll get the amount  of games the user won against the opponent

        $sql = mysqli_query($db, "SELECT count(*) FROM $gamestable WHERE  winner = '$_GET[name]' AND contested_by_loser = '0' AND withdrawn = '0' AND loser = '$name'");
        $x = mysqli_fetch_row($sql);
        $numberwins[] = $x[0];
        // echo "number of wins: ". $numberwins[$z]." <br>";


        // The total sum of games we played against the user is:
        $sumofgames[] = ($numberwins[$z] + $numberlosses[$z]);

        // How many % of the games we played did we win against him/her?
        $won_percent[] = @round(($numberwins[$z] / $sumofgames[$z]) * 100, 1);


        // How many % of the games we played did we lose against him/her?
        $lost_percent[] = @round(($numberlosses[$z] / $sumofgames[$z]) * 100, 1);

        // How many % of the games we've playd in total have been against this opponent?
        $i = @round((($sumofgames[$z] / $numberofplaydgames[0]) * 100), 0);
        // echo $sumofgames[$z] ." // ". $numberofplaydgames[0] ."<br>";
        $percentof[] = $i;

        // What elo does the opponent have today?

        $sql = "SELECT rating FROM $standingscachetable WHERE name = '$name' LIMIT 0,1";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);
        $x = $row['rating'];
        $opcurrentelo[] = $x;


        // Let's figure out, in each game we played, if the user was overrated or underrated when we played against him. This was chains idea, and we do that by taking ( his current Elo - the elo he had in the game). If we get a positive number he was that much overrated, if we get a negative then he was that much underrated. 0 means he was correctly rated. Example: 1600 (elo he had that specific game) - 1700 (his current elo) = 100.

        $sql = "SELECT winner, loser, winner_elo, loser_elo FROM $gamestable WHERE (loser = '$_GET[name]' OR winner = '$_GET[name]') AND (loser = '$name' OR winner = '$name') AND contested_by_loser = '0' AND withdrawn ='0' ORDER BY reported_on DESC";
        $result = mysqli_query($db, $sql);

        $inflationpoint = 0;
        $overratedgames = 0;
        $underratedgames = 0;

        while ($row = mysqli_fetch_array($result)) {

            // We are only interested in the _opponent_. Somtimes he is the winner, sometimes he's the loser. So...we'll filter out the user whos info we're looking at ($_GET['name']))  We'll take the inflation in the opponents rating each time we played against him and all those points up together. We will also keep track of how many of the games against each op

            if ($row['loser'] == $_GET['name']) {
                // If we're the loser, then the opponent must be the winner...
                $inflationpoint = $inflationpoint + ($row['winner_elo'] - $opcurrentelo[$z]);

                // Now let's see if the opponent was over or underrated during that specific game. If he was, then we count _the game_ as a game against an overrated opponent.
                if ($row['winner_elo'] > ($opcurrentelo[$z] + DELTA_OVER_UNDER_RATED)) {
                    $overratedgames++;
                }
                if ($row['winner_elo'] < ($opcurrentelo[$z] - DELTA_OVER_UNDER_RATED)) {
                    $underratedgames++;
                }


            }

            //DEB echo "<br>loser: " . $row['loser'] . " // winner: ".$row['winner'] ." (". $opcurrentelo[$z] .")  <br> ".  $row['winner'] ." Elo in game: ".$row['winner_elo'] . "  // current total inflationpoint: $inflationpoint <br><hr><br>" ;		}

            if ($row['winner'] == $_GET['name']) {
                // If we're the winner, then the opponent must be the loser...
                $inflationpoint = $inflationpoint + ($row['loser_elo'] - $opcurrentelo[$z]);

                if ($row['loser_elo'] > ($opcurrentelo[$z] + DELTA_OVER_UNDER_RATED)) {
                    $overratedgames++;
                }
                if ($row['loser_elo'] < ($opcurrentelo[$z] - DELTA_OVER_UNDER_RATED)) {
                    $underratedgames++;
                }


                //DEB 	echo "<br>loser: " . $row['loser'] ." (". $opcurrentelo[$z] .  ")  // winner: ".$row['winner'] ." <br>".  $row['loser'] ." elo in game: ".$row['loser_elo'] . "  // current total inflationpoint: $inflationpoint <br><hr><br>" ;

            }
        }

        $inflation[] = $inflationpoint; // Here we set the inflation array, which will contain the sum of all inflations in the games agianst the oppponents.

        $gamesvsoverrated[] = $overratedgames;
        $gamesvsunderrated[] = $underratedgames;

        $gamesvsoverratedpercent[] = @round((($gamesvsoverrated[$z] / $sumofgames[$z]) * 100), 0);
        $gamesvsunderratedpercent[] = @round((($gamesvsunderrated[$z] / $sumofgames[$z]) * 100), 0);

        // What elo did the opponent have when they played a game most recently?

        $sql = "SELECT winner, loser, winner_elo, loser_elo FROM $gamestable WHERE (loser = '$_GET[name]' OR winner = '$_GET[name]') AND (loser = '$name' OR winner = '$name') AND contested_by_loser = '0' AND withdrawn ='0' ORDER BY reported_on DESC LIMIT 0,1";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $loscore_elo[] = $row['loser_elo'];


        // How many points have we lost to this opponent?

        $sql = "SELECT SUM(loser_points) FROM $gamestable WHERE loser = '$_GET[name]' AND winner = '$name' AND contested_by_loser = '0' AND withdrawn = '0'";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_row($result);
        if (!empty($row)) {
            $lost_points[] = abs($row[0]); // remobe the - so table is sorted correct and looks tidier.
        } else {
            $lost_points[] = 0;
        }

        // How many p. have we won of him?

        $sql = "SELECT SUM(winner_points) FROM $gamestable WHERE loser = '$name' AND winner = '$_GET[name]' AND contested_by_loser = '0' AND withdrawn = '0'";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_row($result);
        if (!empty($row)) {
            $won_points[] = $row[0];
        } else {
            $won_points[] = 0;
        }

        // And now the total sum of gain:
        $gained_points[] = ($won_points[$z] - $lost_points[$z]);

        // And the average win/loss of points per game
        $average_points[] = @round(($gained_points[$z] / $sumofgames[$z]), 1);

        $z++;

    }
}

// Lets sum upp all inflations against all players...	

//variables declaration
$totaloppinflation = 0;
$totalgamesoverrated = 0;
$totalgamesunderrated = 0;
foreach ($inflation as $mumman) {
    $totaloppinflation = $totaloppinflation + $mumman;
}

// Lets calculate how many games of all weä've played that were under / overrated:
foreach ($gamesvsoverrated as $mumman2) {
    $totalgamesoverrated = $totalgamesoverrated + $mumman2;
}

foreach ($gamesvsunderrated as $mumman3) {
    $totalgamesunderrated = $totalgamesunderrated + $mumman3;
}


echo " ||  Total inflation: " . number_format($totaloppinflation, 0, ',', ' ') . " || Games vs overrated: " . round((($totalgamesoverrated / $numberofplaydgames[0]) * 100), 0) . "% || Games vs underrated: " . round((($totalgamesunderrated / $numberofplaydgames[0]) * 100), 0) . "%";

?>


<div id="gamesdiv2">
    <table id="player" class="tablesorter">

        <thead>

        <tr>
            <th>Name</th>
            <th onmouseover="showToolTip('Current Elo','Current Elo of Opp.',event);" onmouseout="hideToolTip();">Elo
            </th>
            <th onmouseover="showToolTip('Elo in last game','Elo of opp. last game played vs this player',event);"
                onmouseout="hideToolTip();">Last vs
            </th>

            <th onmouseover="showToolTip('Inflation','0 means all the players opp. had correct rating when p. played them. The higher the number, the more overrated p:s opp. was, and vice versa',event);"
                onmouseout="hideToolTip();">Infl
            </th>

            <th onmouseover="showToolTip('Elo Earned from Opponent','Total Elo gained or lost after playing all games against opp.',event);"
                onmouseout="hideToolTip();">E. Gain
            </th>
            <th onmouseover="showToolTip('Elo Average','Average amount of Elo won or lost per game',event);"
                onmouseout="hideToolTip();">E. Avr.
            </th>
            <th onmouseover="showToolTip('Elo Won','Amount Elo won from Opp.',event);" onmouseout="hideToolTip();">E.
                W.
            </th>
            <th onmouseover="showToolTip('Elo Loss','Amount of Elo lost to Opp.',event);" onmouseout="hideToolTip();">E.
                L.
            </th>
            <th onmouseover="showToolTip('Number of Games','Amount of games agains this Opp.',event);"
                onmouseout="hideToolTip();">Games
            </th>
            <th onmouseover="showToolTip('% of total Games','Of all games played, these many have been against this Opp.',event);"
                onmouseout="hideToolTip();">G %
            </th>
            <th onmouseover="showToolTip('Overrated Games','The Opp. was overrated in this many games against the player',event);"
                onmouseout="hideToolTip();">Ovr %
            </th>
            <th onmouseover="showToolTip('Underrated Games','The Opp. was underrated in this many games against the player',event);"
                onmouseout="hideToolTip();">Udr %
            </th>
            <th onmouseover="showToolTip('Wins','Amount won games against this Opp.',event);"
                onmouseout="hideToolTip();">Win
            </th>
            <th onmouseover="showToolTip('% of Wins','Player won this many % of the games against this Opp.',event);"
                onmouseout="hideToolTip();">W %
            </th>
            <th onmouseover="showToolTip('Losses','Amount lost games against this Opp.',event);"
                onmouseout="hideToolTip();">Loss
            </th>
            <th onmouseover="showToolTip('% of Losses','Player lost this many % of the games against this Opp.',event);"
                onmouseout="hideToolTip();">L %
            </th>

        </tr>
        </thead>
        <tbody>

        <?php

        // Now we'll display all the info we've gathered and then the process will repeat itself for the next opponent. Once again we use a loop, but this time we dont touch the databse. Instead we just display stuff...
        $y = 0;


        foreach ($people as $name) {
            if (strtolower($name) != strtolower($_GET['name'])) {

                echo "<tr><td><a href=\"profile.php?name=$name\">$name</a></td><td>$opcurrentelo[$y]</td><td>$loscore_elo[$y]</td><td>$inflation[$y]</td><td>$gained_points[$y]</td><td>$average_points[$y]
</td><td>$won_points[$y]</td><td>$lost_points[$y]</td><td>$sumofgames[$y]</td><td>$percentof[$y]</td><td>$gamesvsoverratedpercent[$y]</td><td>$gamesvsunderratedpercent[$y] </td><td>$numberwins[$y]</td><td>$won_percent[$y] </td><td>$numberlosses[$y]</td><td>$lost_percent[$y] </td></tr>";


                $y++;

            }
        }
        ?>

        </tbody>
    </table>
</div>
