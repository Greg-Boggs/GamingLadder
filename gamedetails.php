<?php
session_start();
require('conf/variables.php');
require('autologin.inc.php');
require_once 'include/genericfunctions.inc.php';

// If it's an admin that's impersonating a player we need to always display the revokebuttons, thus we make sure they wont "time out" for admins :
$approved = false;
if (isset($_SESSION['real-username'])) {
    if ($_SESSION['real-username'] != $_SESSION['username']) {
        $reportdays = 999999999999;
        $approved = true;
    } else {
        // check if player is approved
        $real_name = mysqli_escape_string($db, $_SESSION['real-username']);
        $sql = "SELECT * FROM $playerstable WHERE name = '$real_name' and approved='yes'";
        $result = mysqli_query($db, $sql);
        $approved = !!mysqli_num_rows($result);
    }
}

if (NONPUBLIC_REPLAY_COMMENTS == 1) {
    require('logincheck.inc.php');
}

if (isset($_POST['SendFeedback'])) {
    $sportsmanship = check_plain(trim($_POST['sportsmanship']));
    $comment = mysqli_real_escape_string($db, trim($_POST['comment']));
    $reported_on = check_plain($_GET['reported_on']);

    // Now we'll decide how the sql query should look like. We only want to update whatever the user changed:

    if ($sportsmanship != "") {
        $query2 = "UPDATE $gamestable SET winner_stars = '$sportsmanship' WHERE reported_on = '$reported_on' AND loser = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
    }

    if ($comment != "") {
        $query2 = "UPDATE $gamestable SET loser_comment = '$comment' WHERE reported_on = '$reported_on' AND loser = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
    }

    if ($sportsmanship != "" && $comment != "") {
        $query2 = "UPDATE $gamestable SET loser_comment = '$comment', winner_stars = '$sportsmanship' WHERE reported_on = '$reported_on' AND loser = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
    }


    // Now lets apply it if and only if there was a comment /sportsmanship point/replay given.

    if ($sportsmanship != "" || $comment != "")
        $result2 = mysqli_query($db, $query2) or die("mysql failed somehow");

    // Save replay into system and name into db
    // We use the tmp_name to detect if somebody actually filled in a file for upload.
    if ((isset($_FILES["uploadedfile"]["name"]) && $_FILES['uploadedfile']['name'] != "") && (ALLOW_REPLAY_UPLOAD == 1)) {
        // To the the file extension of the file we use the handy pathinfo php function/array.
        $file_info = pathinfo($_FILES["uploadedfile"]["name"]);
        // Only save the file if it's right size and right extension and the replay upload feature is ENABLED:
        if (
                ($_FILES["uploadedfile"]["size"] <= MAX_REPLAYSIZE)
                && in_array($file_info['extension'], ALLOWED_REPLAYS_EXTENSION)
        ) {
            $filename = preg_replace("(\:|\s|\-)", "", $reported_on, -1) . ".".$file_info['extension'];
            if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $path_file_replay . $filename)) {
                $query2 = "UPDATE $gamestable SET replay_filename = '" . $filename . "' WHERE reported_on = '" . $reported_on . "'";
                $result2 = mysqli_query($db, $query2) or die("fail");
            }
        } else {
            $failure = true;
            $maxfilesizekb = (MAX_REPLAYSIZE / 1000);
            if (in_array($file_info['extension'], ALLOWED_REPLAYS_EXTENSION)) {
                $error = "You attempted to upload a replay but failed. The file you uploaded wasn't of the correct type. Instead of a " . implode(' or ', ALLOWED_REPLAYS_EXTENSION) . " file you uploaded a " . $file_info['extension'] . "-file. Please only upload valid replays.<br /><br /><b>Notice:</b> The game has <i>not</i> been reported. Try again.";
            }
            if ($_FILES["uploadedfile"]["size"] > MAX_REPLAYSIZE) {
                $uploadefilesizekb = ($_FILES["uploadedfile"]["size"] / 1000);
                $uploadefileoversizedkb = ($uploadefilesizekb - $maxfilesizekb);
                $error = "You attempted to upload a replay but failed since it wasn't small enough. We only allow replays that are <= $maxfilesizekb Kb. Yours was $uploadefilesizekb Kb, which is $uploadefileoversizedkb Kb too large. Better luck with next replay....<br /><br /><b>Notice:</b> The game has <i>not</i> been reported. Try again.";

            }
        }
    }


    // We continue out of this loop the display the default page after we have completed the updates to the database.
}

$reRankLadder = false;
// If a game was withdrawn or restored, process that before we query the game.
if (isset($_POST['submit'])) {
    if ($_POST['submit'] == "Withdraw Game") {
        $reportedOn = $_POST['reported_on'];
        $reRankLadder = $reportedOn;
        $sql = "UPDATE $gamestable SET withdrawn = 1 WHERE reported_on = '" . mysqli_escape_string($db, $reportedOn) . "' AND UNIX_TIMESTAMP(reported_on) > " . (time() - 60 * 60 * 24 * $reportdays) . " AND winner = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
        $result = mysqli_query($db, $sql) or die("failed to remove the last game");
    }
// If we are restoring a withdrawn game
    if ($_POST['submit'] == "Restore Game") {
        $reportedOn = $_POST['reported_on'];
        $reRankLadder = $reportedOn;
        $sql = "UPDATE $gamestable SET withdrawn = 0 WHERE reported_on = '" . mysqli_escape_string($db, $reportedOn) . "' AND UNIX_TIMESTAMP(reported_on) > " . (time() - 60 * 60 * 24 * $reportdays) . " AND winner = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
        $result = mysqli_query($db, $sql);
    }
// If we are contesting a game
    if ($_POST['submit'] == "Contest Game" && $approved) {
        $reportedOn = $_POST['reported_on'];
        $reRankLadder = $reportedOn;
        $sql = "UPDATE $gamestable SET contested_by_loser = 1 WHERE reported_on = '" . mysqli_escape_string($db, $reportedOn) . "' AND UNIX_TIMESTAMP(reported_on) > " . (time() - 60 * 60 * 24 * $reportdays) . " AND loser = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
        $result = mysqli_query($db, $sql);
    }
// If we are contesting a game
    if ($_POST['submit'] == "Remove Contest") {
        $reportedOn = $_POST['reported_on'];
        $reRankLadder = $reportedOn;
        $sql = "UPDATE $gamestable SET contested_by_loser = 0 WHERE reported_on = '" . mysqli_escape_string($db, $reportedOn) . "' AND UNIX_TIMESTAMP(reported_on) > " . (time() - 60 * 60 * 24 * $reportdays) . " AND loser = '" . mysqli_escape_string($db, $_SESSION['username']) . "'";
        $result = mysqli_query($db, $sql);
    }
}

if ($reRankLadder !== false) {
    // Rerank the ladder from the deleted game upwards
    require_once 'include/elo.class.php';

    $query = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable WHERE reported_on > '" . $reportedOn . "' ORDER BY reported_on";
    $result = mysqli_query($db, $query) or die ("query failed");
    $elo = new Elo($db);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $winner = $row['winner'];
        $loser = $row['loser'];

        if (!$elo->RankGameInDB($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not rerank game between " . htmlentities($winner) . " and " . htmlentities($loser) . " on " . htmlentities($row['reported_on']) . "<br />";
        }
    }
    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    mysqli_query($db, "TRUNCATE TABLE $standingscachetable");
    mysqli_query($db, "INSERT INTO $standingscachetable " . $cacheSql);
    require_once 'include/morecachestandings.inc.php';
}

require('top.php');

if (isset($failure) && $failure) {
    echo "<br /><p><b>ERROR:</b> " . $error . "</p>";
}

// If we have posted this form, the value for GET needs to be set here from the values posted.
if (!isset($_GET['reported_on'])) {
    $_GET['reported_on'] = $reportedOn;
}

$sql = "SELECT unix_timestamp(reported_on) as unixtime, reported_on, winner, loser, faction1, faction2, winner_points, loser_points, winner_elo, loser_elo, replay_filename is not null as is_replay, replay_downloads, winner_comment, loser_comment, winner_stars, loser_stars, withdrawn, contested_by_loser FROM $gamestable WHERE reported_on = '$_GET[reported_on]' ORDER BY reported_on";
$result = mysqli_query($db, $sql);
$game = mysqli_fetch_array($result);
// Reset the result for use by the game table display function
mysqli_data_seek($result, 0);
?>

    <table class="tablesorter">
        <?php
        require_once 'include/gametable.inc.php';

        echo gameTableTHead();
        echo gameTableTBody($result);

        ?>
    </table>

<?php


if (isset($_SESSION['username'])) {
    if (isset($_SESSION['username']) && $game['winner'] == $_SESSION['username']) {

        if ($game['withdrawn'] == 1 && time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays) {
            echo "<form method='post' action='gamedetails.php'>";
            echo "<input type='hidden' name='reported_on' value='" . $game['reported_on'] . "' />";
            echo "<input type='submit' name='submit' value='Restore Game' />";
            echo "</form>";
        } else if ($game['withdrawn'] == 0 && time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays) {
            echo "<form method='post' action='gamedetails.php'>";
            echo "<input type='hidden' name='reported_on' value='" . $game['reported_on'] . "' />";
            echo "<input type='submit' name='submit' value='Withdraw Game' />";
            echo "</form>";
        }
    }
    if ($game['loser'] == $_SESSION['username']) {

        if ($game['contested_by_loser'] == 1 && time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays) {
            echo "<form method='post' action='gamedetails.php'>";
            echo "<input type='hidden' name='reported_on' value='" . $game['reported_on'] . "' />";
            echo "<input type='submit' name='submit' value='Remove Contest' />";
            echo "</form>";
        } else if ($approved && $game['contested_by_loser'] == 0 && time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays) {

            echo "<form method='post' action='gamedetails.php'>";
            echo "<input type='hidden' name='reported_on' value='" . $game['reported_on'] . "' />";
            echo "<input type='submit' name='submit' value='Contest Game' />";
            echo "</form>";
        }
    }
}


?>
<?php

// Display the player comments
if (trim($game['winner_comment']) != "") {
    echo "<h2>Comment by " . $game['winner'] . ":</h2><br />";
    echo Linkify(nl2br(htmlentities($game['winner_comment'])));
}

if (trim($game['loser_comment']) != "") {
    echo "<br /><h2>Comment by " . $game['loser'] . ":</h2><br />";
    echo Linkify(nl2br(htmlentities($game['loser_comment'])));
}


// Only display the feedback forms if a) a certain feedback hasn't been given by the loser and b) he tries to give the feedback within x days from the time the game was reported and c) the game isnt withdrawn or contested

if (isset($_SESSION['username'])
    && $game['loser'] == $_SESSION['username']
    && ($game['loser_comment'] == "" || $game['winner_stars'] == "" || $game['is_replay'] == "0") && (ALLOW_REPLAY_UPLOAD == 1)
    && (time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays)
    && $game['contested_by_loser'] == 0
    && $game['withdrawn'] == 0) {

    ?>
    <br/><br/>
    <form name="form1" enctype="multipart/form-data" method="post"
          action="<?php echo "gamedetails.php?reported_on=" . urlencode($game['reported_on']) ?>">
        <h2>Feedback</h2>


        <table>

            <?php
            // Only allow loser to upload replay if the winner hasn't already done so and there is one.

            if (($game['is_replay'] == 0) && (time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays) && (ALLOW_REPLAY_UPLOAD == 1)) { ?>
                <input type="hidden" name="MAX_REPLAYSIZE" value="<?php echo(MAX_REPLAYSIZE * 10); ?>"/>

                <tr>
                    <td>replay to upload (format <?php echo implode(' or ', ALLOWED_REPLAYS_EXTENSION); ?>)</td>
                    <td><input name="uploadedfile" type="file"/></td>
                </tr>

            <?php }

            if ((trim($game['winner_stars']) == "") && (time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays)) { ?>
                <tr>
                    <td>sportsmanship</td>
                    <td><select size="1" name="sportsmanship">
                            <option selected="selected" value="">-- No sportmanship rating --</option>
                            <option value="1">1 - Lousy conduct, the player behaved unacceptable.</option>
                            <option value="2">2 - Not the best conduct, but the player was tolerable.</option>
                            <option value="3">3 - Average conduct, nothing more and nothing less.</option>
                            <option value="4">4 - Good conduct, the player is nice and easy to deal with.</option>
                            <option value="5">5 - Superb conduct, the player is very friendly and co-operative.</option>
                        </select>
                    </td>
                </tr>
                <?php
            }

            if (($game['loser_comment'] == "") && (time() < $game['unixtime'] + 60 * 60 * 24 * $reportdays)) {
                ?>
                <tr>
                    <td valign="top">
                        <p valign="top">Game comment</p></td>
                    <td valign="top"><textarea name="comment" rows="5" cols="60"></textarea></td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    <input type="submit" name="SendFeedback" value="Send Feedback"/>
                </td>
            </tr>
        </table>
    </form>

    <?php
}
echo "<br /><br />";
require('bottom.php');
