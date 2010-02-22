<?php
session_start();
require('autologin.inc.php');
require('logincheck.inc.php'); //this file calls variable.conf.php	
date_default_timezone_set("$cfg_ladder_timezone");

// We have ajax lower down on the page, we handle it here and then exit.
// This keeps the ajax code with the page that is calling it.
// Unfortunately we are stuck with 'q' as the query variable as it's hardcoded into the jquery autocomplete module.
// We also only show valid players on the list, meaning players that have their Confirmation set to 'Ok'.
if (isset($_GET['q'])) {
    $q = strtolower($_GET["q"]);
    $query = "SELECT player_id, name from $playerstable WHERE name like '$q%' AND Confirmation = 'Ok' ORDER BY name";
    $result = mysql_query($query) or die("fail");
    while ($row = mysql_fetch_array($result)) {
	    echo $row['name'] . "|" . $row ['player_id'] . "\n";
    }
    exit;
}


require('top.php');

// Now let's check if the player is allowed to report a victory at all. Players are only allowed to report x amount of games within y amount of days.
// Example: 3 reports per 1 day, or 5 reports within a 2 day period. Any numbers go, they can be set in the configfile.


if (ANTI_MATCHSPAM_METHOD != 0) { // we need $recentgames even if METHOD is 2 or 3 (in elo.class.php)
	$sql = "select count(winner) from $gamestable WHERE reported_on > now() - interval ". ANTI_MATCHSPAM_DAYS." day AND (winner = '". $_SESSION['username']."' OR loser = '".$_SESSION['username'] ."') AND withdrawn= 0 AND contested_by_loser = 0";
	$result = mysql_query($sql) or die("fail");
	$row = mysql_fetch_row($result);
	$recentgames = $row[0];
}

if (ANTI_MATCHSPAM_METHOD == 1) { // fixed match amount cap
	if ($recentgames < ANTI_MATCHSPAM_NUMGAMES) {
		"<br>You have played ". $recentgames  ." of ". ANTI_MATCHSPAM_NUMGAMES ." allowed games within the recent ". ANTI_MATCHSPAM_DAYS ." days.";
	}
	else {
		echo "<h1>No more games for today...</h1><br>Sorry ".$_SESSION['username'].", but you have played ". $recentgames  ." of ". ANTI_MATCHSPAM_NUMGAMES ." games within the recent ". ANTI_MATCHSPAM_DAYS ." days. This means that you are not allowed to play any more ladder games <i>today</i>. Please try again tomorrow!<br><br>Notice that current server date & time is ". date('d/m H:m')."<br><br>"; 
		include("bottom.php");
		exit;
	}
}

	// We'll fetch the time of the latest report the user made:
	$sql = "SELECT winner, reported_on, winner_wins FROM $gamestable WHERE winner = '".$_SESSION['username']."'  ORDER BY reported_on DESC LIMIT 0,1";
	$result87 = mysql_query($sql,$db);
	$row87 = mysql_fetch_array($result87);
	

if (isset($_POST['report'])) {
	
	// Before we allow the player to do anything at all, let's check if he isn't a "spammer" - we'll only allow players to report a game every x minute. 
	// In strategy 4x games a value of 20 - 30 minutes would probably be okey. In a FPS where the games can be quite fast 5 or 10 minutes would be better.
	

	
		
	// Get the current time as unix epoch
	$currenttime = date('U');

	// The format of the date in the mysql is 2008-08-23 03:12:14

	$dateoflastgame = strtotime($row87['reported_on']);

	if (((($currenttime - $dateoflastgame )/60) < SPAM_REPORT_TIME_PROTECTION) && ($row87['winner_wins'] < SPAM_REPORT_TIME_PROTECTION_UNLOCKED)) {

		echo "<h1>poopage in the pants...<br></h1><b><br>Please notice that the game was unreported!</b><br> Your last report was made ". $row87['reported_on'] . ". You have to wait at least ". SPAM_REPORT_TIME_PROTECTION ." minutes between new reports. <br>However, currently only ". floor(($currenttime - $dateoflastgame )/60) . " minutes have passed. Pleased wait " .(SPAM_REPORT_TIME_PROTECTION -  floor(($currenttime - $dateoflastgame )/60) ) ." more minutes before trying to report again.";
				
		echo "<br /><br />";
		require('bottom.php');
		exit;
	} 
	
?>
<h3>Report Game Results</h3>
<?php    


	// Make sure the user selected a loser, this should be done in javascript.
	if ($_POST['losername'] == "") {
		echo "<p><b>You must select the name of the loser.</b></p><p>Please return the the <a href='report.php'>report</a> page and select a name.</p>";
		require('bottom.php');
		exit;
	}
    // Make sure the selected user is actually a ladder member

    $current_player = $_SESSION['username'];
    $sql = "SELECT name FROM $playerstable WHERE name = '".$_POST['losername']."' and Confirmation = 'Ok' ";
    $result = mysql_query($sql, $db);
    
    if (mysql_num_rows($result) == 0) {
        echo "<p><b>You must select a valid and confirmed ladder player.</b></p><p>Please return the the <a href='report.php'>report</a> page and select a valid opponent.</p>";
        require 'bottom.php';
        exit;
    }

	$winner = $current_player;
	$rowLoser = mysql_fetch_array($result);
	$loser = $rowLoser['name'];
	
	if ($winner == $loser) { 
		echo "No playing with yourself! ";
		echo '<a href="report.php">Go back.</a>';
		require('bottom.php');	
        exit;
	}

    $draw = false;
    $failure = false;
    $error = "";

    if (!$failure) {
        require_once 'include/elo.class.php';
    
        $elo = new Elo($db);
        $result = $elo->ReportNewGame($winner, $loser, $draw);
	$cloneresult = $result;
        if ($result === false) {
            $failure = true;
            $error = "There was a failure in reporting your game, please try again.";
        }
    }
    if ($failure == true) {
        echo "<p>ERROR: ".$error."</p>";
		
		exit;
    } else {
      
		
			// Save replay into system and name into db
			// We use the tmp_name to detect if somebody actually filled in a file for upload.
			if ((isset($_FILES["uploadedfile"]["name"]) && $_FILES['uploadedfile']['name'] != "") && (ALLOW_REPLAY_UPLOAD == 1)) {
				// To the the file extension of the file we use the handy pathinfo php function/array. 
				$file_info = pathinfo($_FILES["uploadedfile"]["name"]);
				// Only save the file if it's right size and right extension and the replay upload feature is ENABLED:
				if (($_FILES["uploadedfile"]["size"] <= MAX_REPLAYSIZE) && ($file_info['extension'] == $replayfileextension) && (ALLOW_REPLAY_UPLOAD == 1) ){
					$filename = preg_replace ( "(\:|\s|\-)", "", $result['reportedTime'] , -1).'.'.$replayfileextension;
					if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $path_file_replay.$filename)) {
						$query2 = "UPDATE $gamestable SET replay_filename = '".$filename."' WHERE reported_on = '".$result['reportedTime']."'";	
						$result2 = mysql_query($query2) or die("fail");
					}
				} 
				else {
					$failure = true;
					$maxfilesizekb = (MAX_REPLAYSIZE / 1000);
					if ($file_info['extension'] != $replayfileextension) { 
						 $error = "You attempted to upload a replay but failed. The file you uploaded wasn't of the correct type. Instead of a *.". $replayfileextension ." file you uploaded a ". $file_info['extension'].  "-file. Please only upload valid replays.<br /><br /><b>Notice:</b> The game has <i>not</i> been reported. Try again."; 
					}	
					if ($_FILES["uploadedfile"]["size"] > MAX_REPLAYSIZE) {			
						$uploadefilesizekb = ($_FILES["uploadedfile"]["size"]/1000);
						$uploadefileoversizedkb = ($uploadefilesizekb - $maxfilesizekb);
						$error = "You attempted to upload a replay but failed since it wasn't small enough. We only allow replays that are <= $maxfilesizekb Kb. Yours was $uploadefilesizekb Kb, which is $uploadefileoversizedkb Kb too large. Better luck with next replay....<br /><br /><b>Notice:</b> The game has <i>not</i> been reported. Try again.";
					
					}
				}	
			}
	
	// Now that the Elo has been added into the games table, let's update the entry so that it also includes the comment & sportsmanship rating, and the rank of the player at the time the game was played and the rank of the player after the game was played.
		

// Enter the players rankings when they actually played the game....

// First we get the old rank from the cach table

	//$OldRankWinnerResult = ;	
	$wranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$winner."' LIMIT 1";
	$resultwrank = mysql_query($wranksql) or die(mysql_error());
	$rowwrank = mysql_fetch_array($resultwrank);
	
	$lranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$loser."' LIMIT 1";
	$resultlrank = mysql_query($lranksql) or die(mysql_error());
	$rowlrank = mysql_fetch_array($resultlrank);
	
// Then we update the gamestable with the old rank.....

	$UpdateWinnerSql = "UPDATE $gamestable SET w_rank = '". $rowwrank['rank'] ."' WHERE  winner = '".$rowwrank['name']."' AND reported_on = '".$result['reportedTime']."'";	
	$UpdateWinnerResult = mysql_query($UpdateWinnerSql) or die(mysql_error());	
	
		$UpdateLoserSql = "UPDATE $gamestable SET l_rank = '". $rowlrank['rank'] ."' WHERE  loser = '".$rowlrank['name']."' AND reported_on = '".$result['reportedTime']."'";	
	$UpdateLoserResult = mysql_query($UpdateLoserSql) or die(mysql_error());	
			
//	}					
							



// If the winner left a comment or a sportsmanship rating we now want to update the tables, that already have the game result in them,. to include it/them. Lets choose a sql statement...
	
	$username =  $_SESSION['username'];
	$sportsmanship = trim($_POST['sportsmanship']); 
	$comment = trim($_POST['comment']);

	if ($sportsmanship != "") { 
		
		echo "<br>Sportsmanship set. <br>";
		$query2 = "UPDATE $gamestable SET loser_stars = '$sportsmanship' WHERE  winner = '$username' AND reported_on = '".$result['reportedTime']."'";	
	}
	if ($comment != "") { 
	
			$query2 = "UPDATE $gamestable SET winner_comment = '$comment' WHERE  winner = '$username' AND reported_on = '".$result['reportedTime']."'";	
	}
	
	if (($sportsmanship != "") && ($comment != "")) {
			$query2 = "UPDATE $gamestable SET winner_comment = '$comment', loser_stars = '$sportsmanship' WHERE  winner = '$username' AND reported_on = '".$result['reportedTime']."'";	
	}
	
	// Now lets apply it if there was a comment or sportsmanship point given.
		
	if (($sportsmanship != "") || ($comment != "")) { 
		$result2 = mysql_query($query2) or die(mysql_error());	
    }


?>
<p>Congratulations <?php echo $current_player; ?> you have defeated <?php echo $loser; ?></p>

<table border="1" cellpadding="5" cellspacing="0">
	<tr>
        <th></th>

		<th>Provisional Player</th>
		<th>Rating Change</th>   
		<th>Old Ratings</th>   
		<th>New Ratings</th> 
		<th>Sportsmanship</th>   
	</tr>
	
	
	<tr>
  
		<th><?php echo $current_player; ?></th>
        <td><?php echo $cloneresult['winnerProvisional'] ? "Yes" : "No"; ?></td>
         <td><?php echo $cloneresult['winnerChange']; ?></td>
		        <td><?php echo $cloneresult['winnerRating']; ?></td> 
				        <td><?php echo $cloneresult['winnerRating'] + $cloneresult['winnerChange']; ?></td>
							<td>?</td>
    </tr>
	
	
	
	<tr>
			<th><?php echo $loser; ?></th>
			<td><?php echo $cloneresult['loserProvisional'] ? "Yes" : "No"; ?></td>
			        <td><?php echo $cloneresult['loserChange']; ?></td>
		
        <td><?php echo $cloneresult['loserRating']; ?></td>
		
        <td><?php echo $cloneresult['loserRating'] + $cloneresult['loserChange']; ?></td>
		
	<td><?php echo $sportsmanship;?></td>
	<tr>
	


	
    </table>
<?php
	echo "<p>Thank you! Information entered. Check your <a href=\"ladder.php?personalladder=".urlencode($_SESSION['username'])."\">current position.</a><br />Report Id: ". $cloneresult['reportedTime'] . " | " . $winner." / ". $loser ."</p>";
    
  // So the report was done and all that the player entered put into the db. Finally we recache the ladder, it takes about 1-2 seconds with 25000 games
        mysql_query("TRUNCATE TABLE $standingscachetable", $db) or die(mysql_error());
        mysql_query("INSERT INTO $standingscachetable ".$cacheSql, $db) or die(mysql_error());
		require_once 'include/morecachestandings.inc.php';   
		

// Now we can update the games table again, this time with the players _new_ and current ranks since we just update the cache table to reflect these newest changes....
	
// First we get the new rank from the cach table

	$wranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$winner."' LIMIT 1";
	$resultwrank = mysql_query($wranksql) or die(mysql_error());
	$rowwrank = mysql_fetch_array($resultwrank);
	
	$lranksql = "SELECT name, rank FROM $standingscachetable WHERE name= '".$loser."' LIMIT 1";
	$resultlrank = mysql_query($lranksql) or die(mysql_error());
	$rowlrank = mysql_fetch_array($resultlrank);
	
// Then we update the gamestable with the new rank.....

	$UpdateWinnerSql = "UPDATE $gamestable SET w_new_rank = '". $rowwrank['rank'] ."' WHERE  winner = '".$rowwrank['name']."' AND reported_on = '".$cloneresult['reportedTime']."'";	
	$UpdateWinnerResult = mysql_query($UpdateWinnerSql) or die(mysql_error());	
	
		$UpdateLoserSql = "UPDATE $gamestable SET l_new_rank = '". $rowlrank['rank'] ."' WHERE  loser = '".$rowlrank['name']."' AND reported_on = '".$cloneresult['reportedTime']."'";	
	$UpdateLoserResult = mysql_query($UpdateLoserSql) or die(mysql_error());	
	
	
	}
		
} else {
?><table>
<form name="form1" enctype="multipart/form-data" method="post" 
<?php if ($row87['winner_wins'] < MIN_GAMES_REPORT_POPUP) { ?> onsubmit="return confirm('Report win against ' + this.losername.value +'?')" <?php } ?> action="report.php">



<p>

<table>

<tr>
<td><?php echo $_SESSION['username']; ?> won over</td>
    
	<td><input type="text" name="losername" id="CityAjax" value="" style="width: 200px;" /></td><br />
</tr>

    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo (MAX_REPLAYSIZE * 10); ?>" />
    
	<?php 
	// only show the replay upload field if we allow replays...
	if (ALLOW_REPLAY_UPLOAD == 1) { 
	?>
	<tr><td>.<?php echo $replayfileextension." ";?> replay to upload</td><td><input name="uploadedfile" type="file" /></td></tr><br />
	<?php } ?>
	
	<tr><td>sportsmanship</td><td><select size="1" name="sportsmanship">
		<option selected="selected" value="">-- No sportmanship rating --</option>
		<option value="1">1 - Lousy conduct, the player behaved unacceptable.</option>
		<option value="2">2 - Not the best conduct, but the player was tolerable.</option>
		<option value="3">3 - Average conduct, nothing more and nothing less.</option>
		<option value="4">4 - Good conduct, the player is nice and easy to deal with.</option>
		<option value="5">5 - Superb conduct, the player is very friendly and co-operative.</option>
	</select>
	</td>
		
	<br/>
	
	
</p>

<tr><td valign="top">
<p valign="top">game comment</p></td>
<td valign="top"><textarea name="comment" rows="5" cols="60"></textarea> </td>
</tr>
<tr><td>
	<input type="submit" name="report" value="Report Game" onclick="lookupAjax();" />
	</td></tr>
</table>

</form>

<br /><br /><b>Warning: If you cheat you will be banned.</b><br />If <i>accidentally</i> reported a false result, use the game details under your profile to withdraw the game.

<?
}
?>
<script type="text/javascript">
var found = true;


function findValue(li) {

	if( li == null ) { return alert("No match!"); }


	// if coming from an AJAX call, let's use the CityId as the value
	if( !!li.extra ) var sValue = li.extra[0];

	// otherwise, let's just display the value in the text box
	else var sValue = li.selectValue;

	//alert("The value you selected was: " + sValue);
}

function selectItem(li) {
	findValue(li);
}

function formatItem(row) {
	return row[0];
}

function lookupAjax(){
	var oSuggest = $("#CityAjax")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

function lookupLocal(){
	var oSuggest = $("#CityLocal")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

$(document).ready(function() {
	$("#CityAjax").autocomplete(
		"report.php",
		{
			delay:10,
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:selectItem,
			onFindValue:findValue,
			formatItem:formatItem,
			autoFill:true
		}
	);

});
</script>
<?
echo "<br /><br />";
require('bottom.php');
?>
