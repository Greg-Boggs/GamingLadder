<?php
session_start();
require('conf/variables.php');
require('autologin.inc.php');

if (NONPUBLIC_REPLAY_COMMENTS == 1) {
	require('logincheck.inc.php');
}

if (isset($_POST['SendFeedback'])) {
	$sportsmanship = trim($_POST['sportsmanship']); 
	$comment = trim($_POST['comment']);
	$reported_on = $_GET['reported_on'];

	// Do all the replay stuff to create a replay in the database
    // We use the tmp_name to detect if somebody actually filled in a file for upload.
    if (isset($_FILES["uploadedfile"]["name"]) && $_FILES['uploadedfile']['name'] != "") {    
		// To the the file extension of the file we use the handy pathinfo php function/array. 
		$file_info = pathinfo($_FILES["uploadedfile"]["name"]);
		// Only sabe the file if it's right size and right extension:
		if (($_FILES["uploadedfile"]["size"] <= MAX_REPLAYSIZE) && ($file_info['extension'] == $replayfileextension)){
			$replay = file_get_contents($_FILES['uploadedfile']['tmp_name']);
        } else {
            $failure = true;
			$maxfilesizekb = (MAX_REPLAYSIZE / 1000);
			if ($file_info['extension'] != $replayfileextension) {
				$error = "You attempted to upload a replay but failed. The file you uploaded wasn't of the correct type. Instead of a *.". $replayfileextension ." file you uploaded a ". $file_info['extension'].  "-file. Please only upload valid replays.<br /><br />"; 
			} else {			
				$uploadefilesizekb= ($_FILES["uploadedfile"]["size"]/1000);
				$uploadefileoversizedkb = ($uploadefilesizekb - $maxfilesizekb);
				$error = "You attempted to upload a replay but failed since it wasn't small enough. We only allow replays that are <= $maxfilesizekb Kb. Yours was $uploadefilesizekb Kb, which is $uploadefileoversizedkb Kb too large. Better luck with next replay....<br /><br />";
			}
		}
    } else {
        $replay = null;
    }

	if ($sportsmanship != "") { 
		$query2 = "UPDATE $gamestable SET winner_stars = '$sportsmanship' WHERE reported_on = '$reported_on' AND loser = '".mysql_escape_string($_SESSION['username'])."'";
	}
	
	if ($comment != "") { 
		$query2 = "UPDATE $gamestable SET loser_comment = '$comment' WHERE reported_on = '$reported_on' AND loser = '".mysql_escape_string($_SESSION['username'])."'";	
	}
	
	if ($sportsmanship != "" && $comment != "") { 
		$query2 = "UPDATE $gamestable SET loser_comment = '$comment', winner_stars = '$sportsmanship' WHERE reported_on = '$reported_on' AND loser = '".mysql_escape_string($_SESSION['username'])."'";
	}
	
	// Now lets apply it if there was a comment or sportsmanship point given.
	if ($sportsmanship != "" || $comment != "" || $replay != "") { 
		$result2 = mysql_query($query2) or die("fail");
	}
	
	if ($failure) {
        echo "<p>ERROR: ".$error."</p>";
    }

	// We continue out of this loop the display the default page after we have completed the updates to the database.
}

$reRankLadder = false;
// If a game was withdrawn or restored, process that before we query the game.
if ($_POST['submit'] == "Withdraw Game") {
	$reportedOn = $_POST['reported_on'];
	$reRankLadder = $reportedOn;
	$sql = "UPDATE $gamestable SET withdrawn = 0 WHERE reported_on = '".mysql_escape_string($reportedOn)."' AND UNIX_TIMESTAMP(reported_on) > ".(time()-60*60*24*CHANGE_REPORT_DAYS)." AND winner = '".mysql_escape_string($_SESSION['username'])."'";
	$result = mysql_query($sql) or die("failed to remove the last game");
}
// If we are restoring a withdrawn game
if ($_POST['submit'] == "Restore Game") {
	$reportedOn = $_POST['reported_on'];
	$reRankLadder = $reportedOn;
	$sql = "UPDATE $gamestable SET withdrawn = 0 WHERE reported_on = '".mysql_escape_string($reportedOn)."' AND UNIX_TIMESTAMP(reported_on) > ".(time()-60*60*24*CHANGE_REPORT_DAYS)." AND winner = '".mysql_escape_string($_SESSION['username'])."'";
	$result = mysql_query($sql);
}
// If we are contesting a game
if ($_POST['submit'] == "Contest Game") {
	$reportedOn = $_POST['reported_on'];
	$reRankLadder = $reportedOn;
	$sql = "UPDATE $gamestable SET contested_by_loser = 1 WHERE reported_on = '".mysql_escape_string($reportedOn)."' AND UNIX_TIMESTAMP(reported_on) > ".(time()-60*60*24*CHANGE_REPORT_DAYS)." AND loser = '".mysql_escape_string($_SESSION['username'])."'";
	$result = mysql_query($sql);
}
// If we are contesting a game
if ($_POST['submit'] == "Remove Contest") {
	$reportedOn = $_POST['reported_on'];
	$reRankLadder = $reportedOn;
	$sql = "UPDATE $gamestable SET contested_by_loser = 0 WHERE reported_on = '".mysql_escape_string($reportedOn)."' AND UNIX_TIMESTAMP(reported_on) > ".(time()-60*60*24*CHANGE_REPORT_DAYS)." AND loser = '".mysql_escape_string($_SESSION['username'])."'";
	$result = mysql_query($sql);
}

if ($reRankLadder !== false) {
    // Rerank the ladder from the deleted game upwards
    require_once 'include/elo.class.php';

    $query = "SELECT winner, loser, CASE draw WHEN 0 THEN 'false' ELSE 'true' END as draw, reported_on FROM $gamestable WHERE reported_on > '".$reportedOn."' ORDER BY reported_on";
    $result = mysql_query($query) or die ("query failed");
    $elo = new Elo($db);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $winner = $row['winner'];
        $loser = $row['loser'];

        if (!$elo->RankGameInDB($winner, $loser, $row['reported_on'], $row['draw'])) {
            echo "Error: could not rerank game between ".htmlentities($winner)." and ".htmlentities($loser)." on ".htmlentities($row['reported_on'])."<br />";
        }
    }
    // Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
    mysql_query("TRUNCATE TABLE $standingscachetable", $db);	
    mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db);	
}

require('top.php');

// If we have posted this form, the value for GET needs to be set here from the values posted.
if (!isset($_GET['reported_on'])) {
	$_GET['reported_on'] = $reportedOn;
}

$sql = "SELECT length(replay) as is_replay, unix_timestamp(reported_on) as unixtime, reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as replay, replay_downloads, winner_comment, loser_comment, winner_stars, loser_stars, withdrawn, contested_by_loser FROM $gamestable WHERE reported_on = '$_GET[reported_on]' ORDER BY reported_on";
$result = mysql_query($sql, $db);
$game = mysql_fetch_array($result); 
// Reset the result for use by the game table display function
mysql_data_seek($result, 0);
?>

<table class="tablesorter">
<?php 
require_once 'include/gametable.inc.php';

echo gameTableTHead();
echo gameTableTBody($result);

?>
</table>

<?php
if ($game['winner'] == $_SESSION['username']) {
	if ($game['withdrawn'] == 1 && time() < $game['unixtime']+60*60*24*CHANGE_REPORT_DAYS) {
		echo "<form method='post' action='gamedetails.php'>";
		echo "<input type='hidden' name='reported_on' value='".$game['reported_on']."' />";
		echo "<input type='submit' name='submit' value='Restore Game' />";
		echo "</form>";
	} else if ($game['withdrawn'] == 0 &&  time() < $game['unixtime']+60*60*24*CHANGE_REPORT_DAYS) {
		echo "<form method='post' action='gamedetails.php'>";
		echo "<input type='hidden' name='reported_on' value='".$game['reported_on']."' />";
		echo "<input type='submit' name='submit' value='Withdraw Game' />";
		echo "</form>";
	}	
}
if ($game['loser'] == $_SESSION['username']) {
	if ($game['contested_by_loser'] == 1 && time() < $game['unixtime']+60*60*24*CHANGE_REPORT_DAYS) {
		echo "<form method='post' action='gamedetails.php'>";
		echo "<input type='hidden' name='reported_on' value='".$game['reported_on']."' />";
		echo "<input type='submit' name='submit' value='Remove Contest' />";
		echo "</form>";
	} else if ($game['contested_by_loser'] == 0 &&  time() < $game['unixtime']+60*60*24*CHANGE_REPORT_DAYS) {
		echo "<form method='post' action='gamedetails.php'>";
		echo "<input type='hidden' name='reported_on' value='".$game['reported_on']."' />";
		echo "<input type='submit' name='submit' value='Contest Game' />";
		echo "</form>";
	}	
}
?>
<?php
// Display the player comments
if (trim($game['winner_comment']) != "") {
	echo "<h2>Comment by ". $game['winner'] .":</h2><br />";
	echo nl2br(htmlentities($game['winner_comment']));
}

if (trim($game['loser_comment']) != "") {
	echo "<br /><h2>Comment by ". $game['loser'] .":</h2><br />";
	echo nl2br(htmlentities($game['loser_comment']));
}

if ($game['loser'] == $_SESSION['username'] && ($game['loser_comment'] == "" || $game['winner_stars'] == "")) {
?>
<br /><br />
	<form name="form1" enctype="multipart/form-data" method="post" action="<?php echo "gamedetails.php?reported_on=".urlencode($game['reported_on']) ?>">
	<h2>Feedback</h2>
	<table>

<?php 

if ($game['replay'] == 0)  { ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_REPLAYSIZE;?>" />
    
	<tr><td>.<?php echo $replayfileextension ?> replay to upload</td>
		<td><input name="uploadedfile" type="file" /></td></tr>

<?php } 

if (trim($game['winner_stars']) == "") { ?>
	<tr><td>sportsmanship</td><td><select size="1" name="sportsmanship">
		<option selected="selected" value="">-- No sportmanship rating --</option>
		<option value="1">1 - Not very likely to play with this person again.</option>
		<option value="2">2 - Not the best experience, but I'd consider playing against this player again.</option>
		<option value="3">3 - A pleasant opponent.</option>
		<option value="4">4 - A more than pleasant player with good chat.</option>
		<option value="5">5 - I made a new friend.</option>
	</select>
	</td>
	</tr>
<?php 
}

if ($game['loser_comment'] == "") {
?>
<tr><td valign="top">
<p valign="top">Game comment</p></td>
<td valign="top"><textarea name="comment" rows="5" cols="60"></textarea> </td>
</tr>
<?php } ?>	
<tr><td>
	<input type="submit" name="SendFeedback" value="Send Feedback" />
</td></tr>
</table>
</form>

<?php
}
echo "<br /><br />";
require('bottom.php');
?>
