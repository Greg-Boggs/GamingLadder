<?php

/* This functions only use is to see:

		a) how many games a player has played within a certain amount of days
		b) if the player is considered to be active, how many days until he becomes passive if he does not play any more games
	
	...and to dump	that and related info into a global array for use wherever. The following is dumped into the array:



*/

function GetExactActivity($PlayersName, $GamesUntilPassive, $DaysUntilPassive, $GamesTable)
{
    global $ExactActivity, $db;

// Let's see how many games the user has played within the latest x days....X is set in the config file. 

    $sqlplayed = mysqli_query($db, "SELECT count(*) FROM $GamesTable WHERE  (winner = '$PlayersName' OR loser = '$PlayersName') AND contested_by_loser = '0' AND withdrawn = '0' AND reported_on > (now( ) - INTERVAL '$DaysUntilPassive' DAY)");

    if (!$sqlplayed) {
        echo 'Could not run query: ' . mysqli_error($db);
        exit;
    }

//echo "Playersname: $PlayersName  || Gamesuntill passive cfg: $GamesUntilPassive   || Days to passive cfg:  $DaysUntilPassive <br>";
//DEB echo "SQL:n >> " . $sqlplayed . "<br> ";

    $numberplayed = mysqli_fetch_row($sqlplayed);
    $hasplayedgames = $numberplayed[0];

// DEB echo "$ hasplayedgames: " . $hasplayedgames. "<br>";
    $gamesactivitysurplus = ($hasplayedgames - $GamesUntilPassive);


// We already now know if the player is active or not. We need to understand when he would become inactive if he is not already.

    /*  Imagine a player needs to play 3 games within the 6 latest days to be conisdered as acive and not be marked as passive.
       Let's say he has played 6 games within the 6 latest days. Suppose he distrubuted them as seen in:

       Date					Games played
       1 July Mon		  	1
       2 July Tue			2
       3 July Wed			1
       4 July Thur			2

   Today is Friday, the 5:th of July. He has never played games before 1 July and won't play any more games. Is he acive or passive? He is active. The 6 most recent days (30:th June to 5:th of July) he has played a total of 6 games.

   But what happens if we ask the same question the next day, on Saturday the 6:th of July - is he active then? Yes, if it is the 6 July and we look at the most 6 recent days the last thay we'd include would be 1:st of July, so no change there.

   Again, what happens the next day - on 7:th of July? Now we must not count the result of 1 July since that date isn't part of the 6 most recent days any longer. So he has now played only 5 games since the games played on 1 July are not included any more.

   By counting this way and by cutting away one day (and it's games) at a time we can answer questions like "How many days does he have, today, until he becomes passive?" That's what we'll do in the below while-loop.

   */


    $rcounter = 0;
    $buffertdays = 0;
    $searchingpassivedays = $DaysUntilPassive;
    //DEB echo "(66) $ gamesactivitysurplus: $gamesactivitysurplus <br>";

    while (($gamesactivitysurplus >= 0) && ($searchingpassivedays > 0)) {

        $sqlplayed = mysqli_query($db, "SELECT count(*) FROM $GamesTable WHERE  (winner =  '$PlayersName' OR loser =  '$PlayersName' ) AND contested_by_loser = '0' AND withdrawn = '0' AND reported_on > (now( ) - INTERVAL $searchingpassivedays DAY)");

        $numberplayed = mysqli_fetch_row($sqlplayed);


        // Cut away the games that were played furthest away the current date. (This variable is used in the sql below)
        $searchingpassivedays--;

        $hasplayedgames2 = $numberplayed[0];

        if ($hasplayedgames2 >= $GamesUntilPassive) {
            $buffertdays++;

        } else {
            break;
        }
    }

// Let's put all the info in the global array so it can be used elsewhere...


    // DEB echo "(91) $ PlayersName: ". $PlayersName . "<bR>";
    $ExactActivity = array("PlayerName" => $PlayersName, "GamesSurplus" => $gamesactivitysurplus, "GamesPlayed" => $hasplayedgames, "DaysUntilPassive" => $buffertdays);
    //DEB echo "(93) ExactActivity-PlayerName: ". $ExactActivity["PlayerName"] ."<br>";
    //DEB echo "(94) ExactActivity-GamesSurplus: ". $ExactActivity["GamesSurplus"] ."<br>";
    //DEB echo "(95) ExactActivity-GamesPlayed: ". $ExactActivity["GamesPlayed"] ."<br>";
    //DEB echo "(96) ExactActivity-DaysUntilPassive: ". $ExactActivity["DaysUntilPassive"] ."<br>";

    if ($gamesactivitysurplus < 0) {
        $ExactActivity ["Active"] = 0;
    } else {
        $ExactActivity["Active"] = 1;
    }


//DEB echo "(97) ExactActivity-Active: ". $ExactActivity["Active"] ."<br>";
}

?>