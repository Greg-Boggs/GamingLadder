<?php
session_start();
require('conf/variables.php');
require_once 'autologin.inc.php';
require('top.php');
include 'include/avatars.inc.php';


if (isset($_GET['personalladder'])) {
    $personalladder = $_GET['personalladder'];
} else {
    $personalladder = "";
}

$archivemsg = '';
if (isset($_GET['archive'])) {
    // If player wants to view an archived version of the ladder we need to fetch anoither table and perhaps get it from another database. The table name is the date when the ranking is from in the following format: YYYY_MM_DD
    $standingscachetable = $historydatabasename . "." . $_GET['archive'];
    $archivemsg = " on " . $_GET['archive'];
    // Clean up the info...
    $archivemsg = str_replace("_", " ", $archivemsg);
}

?>
<script type="text/javascript">
    $(document).ready(function () {
            $("#ladder").tablesorter({sortList: [[0, 0]], widgets: ['zebra']});
        }
    );
</script>
<?php

// Get everyones ranking for the ladderfrom the standings cache table. There is no need for narrowing down the sql more with criterion for minimum elo and games played in total etc since the cache table has already taken care of all that. 

$myrank = '';
// Fetch players rank...
if (isset($_GET['personalladder'])) {

    $result = mysqli_query($db, "SELECT *
FROM  $standingscachetable 
WHERE recently_played > '0'
AND rank != '0' AND name = '" . $_GET['personalladder'] . "'
ORDER BY rank ASC");

    $personalrow = mysqli_fetch_array($result);

    $myrank = $personalrow['rank'];
}

// If player is not ranked the custom version of the ladder sholdn't be displayed...
if ($myrank == "") {
    $personalladder = "";
}

?>
<h2><?php echo $personalladder . " Ladder Standings $archivemsg</h2>"; ?>

    <table id="ladder" class="tablesorter">
        <thead>
        <tr>
            <th align="left" width='10%'>No.</th>
            <th align="left">Avatar&nbsp; &nbsp;</th>
            <th align="left">Player&nbsp; &nbsp;</th>
            <th align="center">Rating&nbsp; &nbsp;</th>
            <th align="center">Wins% &nbsp; &nbsp;</th>
            <th align="center">Wins&nbsp; &nbsp;</th>
            <th align="center">Losses&nbsp; &nbsp;</th>
            <th align="center">Total&nbsp; &nbsp;</th>
            <th align="center">Streak&nbsp; &nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Reset the result set
        // @mysqli_data_seek($result, 0);

        $RanksAfterMe = "99999999";
        if ($personalladder != "") {
            $RanksBeforeMe = $myrank - 1 - RANKED_ABOVE_PERSONAL_LADDER;
            $RanksAfterMe = $myrank + RANKED_BELOW_PERSONAL_LADDER;
        }
        // Fetch the full ladder rankings list for everyone... also join it with the players table in order to get some crap info like what avatar etc they use.

        $result = mysqli_query($db, "SELECT * 
FROM $standingscachetable
JOIN $playerstable
ON($standingscachetable.name=$playerstable.name)
WHERE rank > 0 AND recently_played > 0
ORDER BY rank ASC LIMIT $RanksAfterMe");

        // If player wants to see a more personal and relevant ladder we need to tell mysql to start displaying the table from another position...
        if ($personalladder != "") {
            @mysqli_data_seek($result, $RanksBeforeMe);
        }


        // $ladderrow = mysqli_fetch_array($result);

        // If I don't have a rank, and requesting a personal ladder, display a message to that effect.
        if (!isset($myrank) && isset($_GET['personalladder'])) {
            echo "<p>You are not ranked. Default ladder will be shown instead of the personal.</p>";
        }

        while ($ladderrow = mysqli_fetch_array($result)) {

            /*
                if (isset($myrank) && ($cur < ($myrank - 10) || $cur > ($myrank + 10))) {
                    $cur++;
                    continue;
                }
            */

// Set graphics for streak....

            if ($ladderrow['streak'] >= $hotcoldnum) {
                $picture = 'images/streakplusplus.gif';
            } else if ($ladderrow['streak'] <= -$hotcoldnum) {
                $picture = 'images/streakminusminus.gif';
            } else if ($ladderrow['streak'] > 0) {
                $picture = 'images/streakplus.gif';
            } else if ($ladderrow['streak'] < 0) {
                $picture = 'images/streakminus.gif';
            } else {
                $picture = 'images/streaknull.gif';
            }

//deb echo "session name: ". $_SESSION['username'] . " and ladderrow name: ". $ladderrow[name] . "<br>";

            if (isset($_SESSION['username']) && $_SESSION['username'] == $ladderrow['name']) {
                echo '<tr class="myrow">';
            } else {
                ?>
                <tr>
                <?php
            }
            ?>
            <td><?php echo $ladderrow['rank']; ?></td>
            <td class="avatar"><?php echo WlAvatar::image($ladderrow['Avatar']) ?></td>
            <td><a href="profile.php?name=<?php echo $ladderrow['name']; ?>"><?php echo $ladderrow['name']; ?> </a></td>
            <td><?php echo $ladderrow['rating']; ?></td>
            <td><?php printf("%.0f", $ladderrow['wins'] / $ladderrow['games'] * 100); ?></td>
            <td><?php echo $ladderrow['wins'] ?> </td>
            <td><?php echo $ladderrow['losses'] ?></td>
            <td><?php echo $ladderrow['games']; ?></td>
            <td><?php echo $ladderrow['streak'] ?></td>
            </tr>
            <?php

        }
        ?>
        </tbody>
    </table>

    <br>
    <p class="copyleft">To get a ranking and compete on the ladder a player needs > <?php echo "$gamestorank"; ?> games,
        an Elo rating of >= <?php echo "$ladderminelo"; ?> & have played >= <?php echo " " . GAMES_FOR_ACTIVE . " "; ?>
        within <?php echo "$passivedays"; ?> days. Don't worry if you haven't played for a while. All it takes is one
        game to become active again. Your rating doesn't decay while you are gone. 1500 is the rating of a <i>skilled
            average</i> player, new players will have less and vets more.</p>
    <br>
    <?php
    require('bottom.php');
    ?>
