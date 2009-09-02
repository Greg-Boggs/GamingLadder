<?
session_start();
$time = time();
require('conf/variables.php');
require('include/smileys.inc.php');
require('include/genericfunctions.inc.php');
date_default_timezone_set("$cfg_ladder_timezone");

?>
<?php
if ($_POST[user] AND $_POST[pass]) {

	$fname = $_POST[user];
	$fpass = $_POST[pass];
	
	
// Lets hinder players who havent verified their mail from loging in / creating logged in cookies...
//eye Check if the player has verified his email by clicking the activation link...

$sql="SELECT * FROM $playerstable WHERE name = '$fname'";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);
	
		
	if ($row[Confirmation] != "" AND $row[Confirmation] == "Deleted") {
		require('top.php');
		echo "<b>You cant login because your account was deleted either on your request or by admin.</b><br><br>Feel free to contact us if you want to re-enable your account: All the data associated with it has been saved and can easily be restored by admin.";
		require('bottom.php');
		exit;		
		}
	
	
	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok") {
		require('top.php');
		echo "<b>You cant login because you have not activated your account.</b><br /><br />When you registered a mail was sent to you containing a unique <i>activation link</i>. Please find that mail and click the activation link. Dont forget to check your spam box, as some services wrongly flag our activation mail as spam. <br><br>Feel free to contact us if you are sure that you have missplaced your activation email.";
		require('bottom.php');
		exit;		
		}

// Okey, we have no check that the user is verified and can let him in...
// Lets generate the encrypted pass, after all, its the one thats stored in the database... we do it by applying the salt and hashing it twice.
// We need to take the users real pass, "encrypt" it the same way we did when he registered, and then compare the results.
// The salt is read from the config file.

$passworddb = $salt.$fpass;
$passworddb = md5($passworddb); 
$passworddb = md5($passworddb); 

	

	//echo "<br>Form: $fname / $fpass";
	$sql = "SELECT * FROM $playerstable WHERE name='$fname' AND passworddb='$passworddb'";
	$result = mysql_query($sql,$db);
	$bajs = mysql_fetch_array($result); 
	//echo "<br>Test 2";
	 if ("$bajs[player_id]" > 0) {
		
		// Set cookies... 776000 sec = 3 months before they expire.
		setcookie ("LadderofWesnoth1", $bajs[name], $time+7776000); 
		setcookie ("LadderofWesnoth2", $bajs[passworddb], $time+7776000); 
        $_SESSION['username'] = $bajs['name'];
        $_SESSION['real-username'] = $bajs['name'];
		header("Location: index.php");
		//echo $sql;
		exit;
		
		//header("Location: http://www.example.com/");

		//DEB print_r($_COOKIE);
		//DEB echo "<div align='right'>// $bajs[name]</div>";

	 } else {
				$login_error= true; 
				// Show error msg if the login failed...
			require('top.php');
			echo "<h1>Login Failed.</h1><br><p>Please make sure that you're registered and that you typed in the correct username/password. If you've checked everything 5 times and the problem still remains please contact us and we'll assist you.</p>";
			require('bottom.php');
			
			exit;
				
				}
}

// You must have autologin before top.php
require 'autologin.inc.php';
require('top.php');

?> 


<br />
<table border=0 width="100%" style="smallinfo">
	<tr>
	
	
	<td width="50%" valign="top" padding-right="20px">

<?php

If (INDEX_COMMENT_HILITE == 1) {



		$sql ="SELECT winner, loser, replay_filename is not null as is_replay, reported_on, winner_comment, loser_comment, winner_elo, loser_elo FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND replay_filename != '' AND (winner_comment != '' || loser_comment != '') ORDER BY reported_on DESC LIMIT 0,1";
		
		$result = mysql_query($sql,$db);
		$row = mysql_fetch_array($result);
		
		

	echo "<div class=\"spotlight\"><h1 class=\"spotlight\">Spotlight</h1><br /> <b>".$row['winner']." (".$row['winner_elo'].") / ".$row['loser']." (".$row['loser_elo'].")</b>";

	 if ($row[is_replay] != 0) {
				echo " <a href=\"download-replay.php?reported_on=$row[reported_on]\">®</a><br /><br />";
			}

	// We don't want to show the comments to members that are not logged in if comments are set to only display to logged in members...
	if ((NONPUBLIC_REPLAY_COMMENTS == 0) || ((NONPUBLIC_REPLAY_COMMENTS == 1) && (isset($_SESSION['username'])))){


		if (trim($row['winner_comment']) != "") {
			echo "<i>\"".Linkify($row['winner_comment']) ."\"  </i>~".$row['winner']."<br /><br />";
			}


		if (trim($row['loser_comment']) != "") {
			echo "<i>\"".Linkify($row['loser_comment']) ."\"  </i>~".$row['loser'];
			}
	} else { echo "<i>Please login to read game comments.</i>"; }
	
	
// Magic Commentator starts here ---------------------------------------------------------------
if  ($MagicComGotEloSettings['Comments'] > 0){
echo "<br><br>";
$sql="SELECT * FROM $gamestable WHERE contested_by_loser = '0' AND withdrawn = '0' ORDER BY reported_on DESC LIMIT 0,1000";

	$result=mysql_query($sql,$db);
	while ($RowAutoMent = mysql_fetch_array($result)){
		
			// Get random messages for the magic commentator... (these are set in config)
			$MagicComRandTopX = array_rand($MagicComRandTopXMsgs,2);
			$MagicComRandTop1st = array_rand($MagicComRandTop1stMsgs,2);
			$MagicComRandTop2 = array_rand($MagicComRandTop2Msgs,2);
			$MagicComRandTop5 = array_rand($MagicComRandTop5Msgs,2);
			$MagicComRandElo = array_rand($MagicComRandEloMsgs,2);
			
		// Don't touch the order of the IF statements, they're exectud the mist interesting first and should be kept like they are unless you really have some wierd desire.
		
		
				// Took 1:st place
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'Q') && (($RowAutoMent['w_rank'] > 1) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] == 1) && ($RowAutoMent['w_new_rank'] > 0))) {
				echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTop1stMsgs[$MagicComRandTop1st[0]];
						// Remember the persons involved in the last display, to not show info twice in a row about the same person
		$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
		$MagicCounter++;
		}
		
				// Took 2:nd place
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'W') && (($RowAutoMent['w_rank'] > 2) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] == 2) && ($RowAutoMent['w_new_rank'] > 0))) {
				echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTop2Msgs[$MagicComRandTop2[0]]. " 2:nd place.";
						// Remember the persons involved in the last display, to not show info twice in a row about the same person
		$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
		$MagicCounter++;
		}


		// Took 3:d place
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'E') && (($RowAutoMent['w_rank'] > 3) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] == 3) && ($RowAutoMent['w_new_rank'] > 0))) {
				echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTop2Msgs[$MagicComRandTop2[0]]. " 3:d place.";
						// Remember the persons involved in the last display, to not show info twice in a row about the same person
		$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
		$MagicCounter++;
		}


		// Entered Top 5
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'R') && (($RowAutoMent['w_rank'] > 5) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] <= 5) && ($RowAutoMent['w_new_rank'] > 0) && ($RowAutoMent['w_new_rank'] > 3))) {
				echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTop5Msgs[$MagicComRandTop5[0]]. " Top 5.";
						$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
		$MagicCounter++;
		}
		

		// Entered Top 10
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'T') && (($RowAutoMent['w_rank'] > 10) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] <= 10) && ($RowAutoMent['w_new_rank'] > 0))) {

			echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTopXMsgs[$MagicComRandTopX[0]]. " Top 10.";
					$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
			$MagicCounter++;
		}
		
		// Entered Top 20
		
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'Y') && (($RowAutoMent['w_rank'] > 20) || ($RowAutoMent['w_rank'] == 0)) && (($RowAutoMent['w_new_rank'] <= 20) && ($RowAutoMent['w_new_rank'] > 0))) {

			echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandTopXMsgs[$MagicComRandTopX[0]]. " Top 20.";
					$PreviousWinner = $RowAutoMent['winner'];
		$PreviousLoser = $RowAutoMent['loser'];
			$MagicCounter++;
		
		}
		
		if (($PreviousWinner != $RowAutoMent['winner']) && stristr(INDEX_MAGIC_COMMENTATOR,'L')){
		
		// Check if user has reachead a rating that's, for example, gone from being 15xx to 16xx, or 19xx to 20xx. Ranges are is set in the config file.
		
			$MCcurrentrating = $MagicComGotEloSettings['FirstRating'];
			$WinnersEloBeforeGame = ($RowAutoMent['winner_elo'] - $RowAutoMent['winner_points']);
			
				while (($PreviousWinner != $RowAutoMent['winner']) && ($MCcurrentrating <= $MagicComGotEloSettings['LastRating'])){
			
					if (($PreviousWinner != $RowAutoMent['winner']) && ($WinnersEloBeforeGame < $MCcurrentrating) && ($RowAutoMent['winner_elo'] > $MCcurrentrating) && ($RowAutoMent['winner_elo'] < ($MCcurrentrating + $MagicComGotEloSettings['AddThis']))) {
					
						echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on']) ."] <a href=\"profile.php?name=". $RowAutoMent['winner'] ."\">".$RowAutoMent['winner'] ."</a> ". $MagicComRandEloMsgs[$MagicComRandElo[0]]. " ".$MCcurrentrating ." Elo points."; 
				
						$PreviousWinner = $RowAutoMent['winner'];
						$PreviousLoser = $RowAutoMent['loser'];
							$MagicCounter++;
				}
				
				$MCcurrentrating = $MCcurrentrating + $MagicComGotEloSettings['AddThis'];
			}
		}
		
		/* Show negative comments... 
		if (($RowAutoMent['l_rank'] < 80) && ($RowAutoMent['l_new_rank'] >= 80)) {
			echo "<br>[". GetOnlyMonthDay($RowAutoMent['reported_on'])  ."] ". $RowAutoMent['loser'] ." entered the bottom 80 at rank ". $RowAutoMent['l_new_rank'];
		*/
		// Check if we've shown enough comments
		if ($MagicCounter >= $MagicComGotEloSettings['Comments'])  { break; }
		} // Magic commentator While-loop ends here.
	} // Magoc commentator ends here
	
	
} // If comment hilite is on anything within the } will happen
	


echo "<h1>News</h1>";
if ($_GET[readnews]) {
$sql="SELECT * FROM $newstable WHERE news_id = '$_GET[readnews]' ORDER BY news_id DESC LIMIT 0, $newsitems";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$news = nl2br($row["news"]);
$news = addSmileys($news);

print("
<p class=header>$row[title]</p>
<p class=text>$news</p>
<hr size=1><br>
");

}else{
	
$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT 0, $newsitems";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
$news = nl2br($row["news"]);
$news = addSmileys($news);

print("
<p class=header>$row[title]</p>
<p class=text>$news</p>
<br>
<hr size=1<br />
");
}

}
print("
<p class=header>Other news articles:</p>
<p class=text>
");


/* If the user has clicked on a specific news he only sees that one and we need to change the list of old news below it,
   so it shows news that are older than the news item he is viewing. So, if he reads newsitem 45, we want a list below it with
   news with a lower id than 45. That's what the following does, and we simply alter the sql query depending on if he views the
  index page or if he has clicked a specific news item...
*/
if ($_GET[readnews]) {

$query = "SELECT COUNT(*) FROM $newstable"; 
$result = mysql_query($query,$db) or die(mysql_error());
$row = mysql_fetch_array($result);
// Let's count the number of news items in the database. Rumors say this is a faster method than getrows, but I don't know.
$numindexnews2 = $row['COUNT(*)'] - $_GET[readnews];

$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT $numindexnews2, $numindexnews";
	} else {
		// This is what happens when he hasn't clicked a specific news item, not complicated at all, the variables are all in the config file.
	$sql="SELECT * FROM $newstable ORDER BY news_id DESC LIMIT $newsitems, $numindexnews"; }

	$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"<a href='index.php?readnews=$row[news_id]'>$row[date] - $row[title]</a><br>";
}
?>
	
	</td>
	
	


<?php 

include ('sidebar.php'); 

echo"<br>";
require_once('include/cronjobs.inc.php');
require('bottom.php');
?>
