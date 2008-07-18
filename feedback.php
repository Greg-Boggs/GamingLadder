<?php
session_start();
require('conf/variables.php');
require('autologin.inc.php');

if (NONPUBLIC_REPLAY_COMMENTS == 1) {
	require('logincheck.inc.php');
}

require('top.php');

$sql = "SELECT reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as replay, replay_downloads, winner_comment, loser_comment, winner_stars, loser_stars FROM $gamestable WHERE winner = '$_GET[winner]' AND reported_on = '$_GET[reported_on]' ORDER BY reported_on";
$result = mysql_query($sql,$db);
$row = mysql_fetch_array($result); 

if (isset($_POST['SendFeedback']) && ($row['loser']  == $_SESSION['username'])) {
	
$sportsmanship = trim($_POST['sportsmanship']); 
$comment = trim($_POST['comment']);
$winner = $_GET['winner'];
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
				 }	else {			
				$uploadefilesizekb= ($_FILES["uploadedfile"]["size"]/1000);
							
				$uploadefileoversizedkb = ($uploadefilesizekb - $maxfilesizekb);
				$error = "You attempted to upload a replay but failed since it wasn't small enough. We only allow replays that are <= $maxfilesizekb Kb. Yours was $uploadefilesizekb Kb, which is $uploadefileoversizedkb Kb too large. Better luck with next replay....<br /><br />";
				}
			}

    } else {
        $replay = null;
    }

	
 if ($replay != null) {
	
		
	}

	if ($sportsmanship != "") { 
			$query2 = "UPDATE $gamestable SET winner_stars = '$sportsmanship' WHERE  winner = '$winner' AND reported_on = '$reported_on'";	
	}
	
	if ($comment != "") { 
			$query2 = "UPDATE $gamestable SET loser_comment = '$comment' WHERE  winner = '$winner' AND reported_on = '$reported_on'";	
	}
	
	if ($sportsmanship != "" && $comment != "") { 
			$query2 = "UPDATE $gamestable SET loser_comment = '$comment', winner_stars = '$sportsmanship' WHERE winner = '$winner' AND reported_on = '$reported_on'";
	}
	
	// Now lets apply it if there was a comment or sportsmanship point given.
		
	if ($sportsmanship != "" || $comment != "" || $replay != "") { 
		$result2 = mysql_query($query2) or die("fail");
    
	echo "<br>Yarr. Your feedback was added.<br />";
	
	}
	
	if ($failure) {
        echo "<p>ERROR: ".$error."</p>";
    }

require('bottom.php');
exit;
}

?>

<!-- This javascript will check the number of chars eneterd in the comment tex box.Amount is speciufied in the comment field creation. -->
<script language="javascript" type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
</script>



<table>
	<tr>
		<th>
		date
		</th>
		<th>
		winner
		</th>
		<th>
		loser
		</th>
		<th>
		replay
		</th>				
	</tr>

	<tr>
	
	<td>
	<?php echo $row['reported_on']; ?>
	</td>

	
	<td>
	<?php echo $row['winner'] ." ". $row['winner_elo'] . " (+". $row['winner_points'].")"; ?>
	</td>
	
	<td>
	<?php echo $row['loser'] . " ".  $row['loser_elo']. " (". $row['loser_points'].")"; ?>
	</td>
	
		<td>
		
	<?php
	
	 if ($row[replay] != 0) {
		echo "<a href=\"download-replay.php?reported_on=$row[reported_on]\">Download</a> (". $row['replay_downloads'] .")"; 
	
	} else {
		echo "No";
	}
	?>

	</td>
</tr>
</table>


<?php 


if (trim($row['winner_comment']) != "") {
	echo "<h1>". $row['winner'] .":</h1><br />";
	echo  $row['winner_comment'];
}

if (trim($row['loser_comment']) != "") {
	echo "<br /><h1>". $row['loser'] .":</h1><br />";
	echo $row['loser_comment'];
}



if (($row['loser']  == $_SESSION['username']) && ($row['loser_comment'] == "" || $row['winner_stars'] == "")) {

?>

<br /><br /><hr>
<form name="form1" enctype="multipart/form-data" method="post" action="<?php echo "feedback.php?reported_on=". urlencode($row['reported_on'])."&winner=".urlencode($row['winner']) ?>">
<h1>Feedback</h1>



<table>

<?php 

if ($row[replay] == 0)  { ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_REPLAYSIZE;?>" />
    
	<tr><td>.<?php echo $replayfileextension." ";?> replay to upload</td><td><input name="uploadedfile" type="file" /></td></tr><br />

<?php } 


if (trim($row['winner_stars']) == "") { ?>

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
		
	<br/>
<?php 
}

if ($row['loser_comment'] == "") {
?>

<tr><td valign="top">
<p valign="top">game comment</p></td>
<td valign="top"><textarea name="comment" rows="5" cols="60"  onKeyDown="limitText(this.form.comment,this.form.countdown,499);"></textarea> </td>
	
<?php } ?>	

</tr>
<tr><td>
	<input type="submit" name="SendFeedback" value="Send Feedback" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>"/>
	</td></tr>

</table>
</form>

<?php
}
echo "<br /><br />";
require('bottom.php');
?>
