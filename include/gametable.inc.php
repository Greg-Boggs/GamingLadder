<?php
require_once 'conf/variables.php';

function gameTableTHead() 
{
    return "
<thead>
<tr>
<th>Reported</th>
<th>Winner</th>
<th>Loser</th>
<th>Winner  Rating</th>
<th>Loser Rating</th>
<th>Winner  Rank</th>
<th>Loser Rank</th>
<th>Feedback</th>
<th>Replay</th>
<th>Game Detail</th>
</tr>
</thead>";
}

function gameTableTBody($result, $playerName = null)
{
    $text = "<tbody>";

    while ($row = mysql_fetch_array($result)) {
        if ($row["recorded"] == "yes") {
    	    $status = "recorded";
	    } else {
    	    $status = "pending";
	    }

	    $undoDeleteLink = "";
	    // Strike through games that aren't counted
	    if ($row['withdrawn'] <> 0 || $row['contested_by_loser'] <> 0) {
	        $sdel = "<del>";
	        $edel = "</del>";
	    } else {
    	    $sdel = "";
        	$edel = "";
    	}

    	$winnerWithdrew = "";
    	$loserContested = "";
    	if ($row['withdrawn'] <> 0) {
        	$winnerWithdrew = "**";
    	} else if ($row['contested_by_loser'] <> 0) {
        	$loserContested = "**";
    	}
    	$winnerProvisional = $row['winner_games'] <= PROVISIONAL ? "p" : "";
    	$loserProvisional = $row['loser_games'] <= PROVISIONAL ? "p" : "";

		$text .= "<tr><td>";
        $text .= $sdel.$row['reported_on'].$edel;
		$text .= "</td><td>";
	    if ($row[winner] == $playerName) { 
  	    	$text .= $sdel.$row['winner'].$winnerWithdrew.$edel;
    	} else {
        	$text .= $sdel."<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>".$winnerWithdrew.$edel;
    	}
		$text .= "</td><td>";

		if ($row[loser] == $playerName) {
			$text .= $sdel."$row[loser]".$loserContested.$edel; 
		} else {
			$text .= $sdel."<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>".$loserContested.$edel;
		}
		$text .= "</td>";
		$text .= "<td>".$sdel.$row['winner_elo'].$winnerProvisional." (".$row['winner_points'].")".$edel."</td>";
		$text .= "<td>".$sdel.$row['loser_elo'].$loserProvisional." (".$row['loser_points'].")".$edel."</td>";

		// Put all the relevant stuff into the feedback column
		// We only want to show stuff if there is something..else we'll show a - 
		if ($row['winner_stars'] == NULL || $row['winner_stars'] == "" || $row['winner_stars'] == "0" ) {
			$winnerstars = "-";
		} else {
		    $winnerstars = $row['winner_stars'];
		}
		if ($row['loser_stars'] == NULL || $row['loser_stars'] == "" || $row['loser_stars'] == "0") {
			$loserstars = "-";
		} else {
			$loserstars = $row['loser_stars'];
		}

		$text .= "<td>".$sdel.$winnerstars." / ". $loserstars.$edel; 

		$commentedby = "";

		if ($row['winner_comment'] != "") { $commentedby = " W";}
		if ($row['loser_comment']  != "") { $commentedby = " L";}
		if ($row['loser_comment']  != "" && $row['winner_comment'] != "") {
		    $commentedby =  " W/L";
		}
		$text .= $sdel.$commentedby.$edel."</td>";

		$text .= "<td>";

		// We insert the span here to allow the table sorter to automatically sort the number of replay downloads
	    if ($row['is_replay']  > 0) {
    	    $downloadDisplay = "";
			// Only show how many times a replay has been downloaded if it has been downloaded to begin with...
			if ($row['replay_downloads'] > 0) {
			    $downloadDisplay = " (".$row['replay_downloads'].")";
			}
			$text .= $sdel."<span style='display: none'>".$row['replay_downloads']."</span><a href=\"download-replay.php?reported_on=".urlencode($row['reported_on'])."\">Download</a>".$downloadDisplay.$edel;
	    } else {
	        $text .= $sdel."<span style='display: none'>-1</span>No".$edel;
	    }
		$text .= "</td>";

		$canProvideFeedback = "";
	
		// If the user hasn't given some kind of feedback and not enough time has passed since the game was played we should show him/her the Feedback-text, indicating he can give some feeback by pressing it.
		if ($row['loser'] == $_SESSION['username'] && $_GET['name'] == $_SESSION['username'] && ($row['is_replay'] <= 0 || $row['loser_comment']  == "" || $row['winner_stars'] == NULL) && (time() < $row['unixtime']+60*60*24*CHANGE_REPORT_DAYS) && $row['contested_by_loser'] == 0 && $row['withdrawn'] == 0 ) {
			
		    $canProvideFeedback = "/Feedback";
		}
		$text .= "<td>".$sdel."<a href=\"gamedetails.php?reported_on=".urlencode($row['reported_on'])."\">Details".$canProvideFeedback."</a>".$edel."</td>";
		$text .= "</tr>";
	}
	$text .= "</tbody>";

	return $text;
}
?>
