<?
session_start();
// v 1.01
require('conf/variables.php');

// Handle all of the cookie management before we attempt to do anything else
$searchArray = unserialize($_COOKIE['gamehistoryoptions']);
if (isset($_GET['reporteddirection'])) $searchArray['reporteddirection'] = $_GET['reporteddirection'];
if (isset($_GET['reportdate'])) $searchArray['reportdate'] = $_GET['reportdate'];
if (isset($_GET['reportdate'])) $searchArray['reportdate'] = $_GET['reportdate'];
if (isset($_GET['winner'])) $searchArray['winner'] = $_GET['winner'];
if (isset($_GET['loser'])) $searchArray['loser'] = $_GET['loser'];
if (isset($_GET['loserratingdirection'])) $searchArray['loserratingdirection'] = $_GET['loserratingdirection'];
if (isset($_GET['winnerratingdirection'])) $searchArray['winnerratingdirection'] = $_GET['winnerratingdirection'];
if (isset($_GET['winnerrating'])) $searchArray['winnerrating'] = $_GET['winnerrating'];
if (isset($_GET['loserrating'])) $searchArray['loserrating'] = $_GET['loserrating'];
if (isset($_GET['replay'])) $searchArray['replay'] = $_GET['replay'];
if (isset($_GET['playerand'])) $searchArray['playerand'] = $_GET['playerand'];
if (isset($_GET['ratingand'])) $searchArray['ratingand'] = $_GET['ratingand'];

if ($searchArray['reporteddirection'] <> "<=" && $searchArray['reporteddirection'] <> ">=") $searchArray['reporteddirection'] = "";
if ($searchArray['winnerratingdirection'] <> "<=" && $searchArray['winnerratingdirection'] <> ">=") $searchArray['winnerratingdirection'] = "";
if ($searchArray['loserratingdirection'] <> "<=" && $searchArray['loserratingdirection'] <> ">=") $searchArray['loserratingdirection'] = "";
if ($searchArray['playerand'] <> "AND" && $searchArray['playerand'] <> "OR") $searchArray['playerand'] = "AND";
if ($searchArray['ratingand'] <> "AND" && $searchArray['ratingand'] <> "OR") $searchArray['ratingand'] = "AND";

setcookie ("gamehistoryoptions", serialize($searchArray), time()+7776000); 

require_once 'autologin.inc.php';
require('top.php');

if (isset($_REQUEST['selectname'])) {
    $playerquerystring = "&selectname=".$_REQUEST['selectname'];
} else {
    $playerquerystring = "";
}
?>
<p class="header">Played games.</p>
<p>A maximum of 250 games will be displayed at any one time.  If games are missing, please refine your search further. They are selected
in reported on order descending, so any missing games will be the oldest games from the selection criteria.</p>
<form method="get" action="gamehistory.php">
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#games").tablesorter({sortList: [[0,1]], widgets: ['zebra'] }); 
    } 
); 
</script>

<table width="60%" id="games" class="tablesorter">
<thead>
<tr>
<th>Reported Time</th>
<th>Winner</th>
<th>Loser</th>
<th>Winner New Rating</th>
<th>Loser New Rating</th>
<th>Replay</th>
</tr>
<tr>
<td><select name="reporteddirection">
    <option <?php if ($searchArray['reporteddirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['reporteddirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['reporteddirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['reportdate']; ?>" name="reportdate" size="9" /></td>
<td><input type="text" value="<?php echo $searchArray['winner']; ?>" name="winner" size="10" /></td>
<td><input type="text" value="<?php echo $searchArray['loser']; ?>" name="loser" size="10" /></td>
<td><select name="winnerratingdirection">
    <option <?php if ($searchArray['winnerratingdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['winnerratingdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['winnerratingdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['winnerrating']; ?>" name="winnerrating" size="4" />
</td>
<td><select name="loserratingdirection">
    <option <?php if ($searchArray['loserratingdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['loserratingdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['loserratingdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['loserrating']; ?>" name="loserrating" size="4" />
</td>
<td><select name="replay">
    <option <?php if ($searchArray['replay'] == "") echo "selected='selected'"; ?> value="">All</option>
    <option <?php if ($searchArray['replay'] == "yes") echo "selected='selected'"; ?> value="yes">Yes</option>
    <option <?php if ($searchArray['replay'] == "no") echo "selected='selected'"; ?> value="no">No</option>
    </select>
</td>
</tr>
<tr>
<td style="vertical-align: middle; text-align: center"><input type="submit" name="submit" value="Update Search" /></td>
<td style="vertical-align: middle; text-align: center" colspan="2"><select name="playerand">
   <option <?php if ($searchArray['playerand'] == "AND") echo "selected='selected'"; ?> value="AND">and</option>
   <option <?php if ($searchArray['playerand'] == "OR") echo "selected='selected'"; ?> value="OR">or</option>
   </select> above options together.</td>
<td style="vertical-align: middle; text-align: center" colspan="2"><select name="ratingand">
   <option <?php if ($searchArray['ratingand'] == "AND") echo "selected='selected'"; ?> value="AND">and</option>
   <option <?php if ($searchArray['ratingand'] == "OR") echo "selected='selected'"; ?> value="OR">or</option>
   </select> above options together.</td>
<td>&nbsp;</td>
</tr>
</thead>
<tbody>
<?php
// Select the games we are interested in

// Build where clause
// Store all this in a cookie
$where = "(winner like '%".$searchArray['winner']."%' ".$searchArray['playerand']." loser like '%".$searchArray['loser']."%') ";

// Setup ratings in query
if ($searchArray['winnerratingdirection'] != "" && $searchArray['winnerrating'] != "") {
    $where .= " AND (winner_elo ".$searchArray['winnerratingdirection']." '".$searchArray['winnerrating']."' ".$searchArray['ratingand']." ";
    if ($searchArray['loserratingdirection'] != "" && $searchArray['loserrating'] != "") {
        $where .= " loser_elo ".$searchArray['loserratingdirection']." '".$searchArray['loserrating']."') ";
    } else {
        $where .= " 1=1)";
    }
} else {
   // Still do the loser rating stuff
   if ($searchArray['loserratingdirection'] != "" && $searchArray['loserrating'] != "") {
        $where .= " AND loser_elo ".$searchArray['loserratingdirection']." '".$searchArray['loserrating']."' ";
   }
}

// Add replay restrictions
if ($searchArray['replay'] == "no") {
    $where .= " AND length(replay) IS NULL ";
} else if ($searchArray['replay'] == "yes") {
    $where .= " AND length(replay) > 0 ";
}

// Add reported_on restrictions
if ($searchArray['reporteddirection'] != "" && $searchArray['reportdate'] != "") {
    $where .= " AND reported_on ".$searchArray['reporteddirection']." '".$searchArray['reportdate']."' ";
}

// Build the select
$sql = "SELECT withdrawn, contested_by_loser, DATE_FORMAT(reported_on, '".$GLOBALS['displayDateFormat']."') as report_time, reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as is_replay, replay_downloads FROM $gamestable WHERE $where ORDER BY reported_on DESC LIMIT 250";

$result = mysql_query($sql, $db);
while ($row = mysql_fetch_array($result)) {
    if ($row["recorded"] == "yes") {
        $status = "recorded";
    } else {
        $status = "pending";
    }
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
?>

<tr>
<td><?echo $sdel.$row['report_time'].$edel ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>".$winnerWithdrew.$edel ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>".$loserContested.$edel ?></td>
<td><?echo $sdel.$row['winner_elo']." (".$row['winner_points'].")".$edel ?></td>
<td><?echo $sdel.$row['loser_elo']." (".$row['loser_points'].")".$edel ?></td>
<td>
<?php
    if ($row['is_replay']  > 0) {
        $downloadDisplay = "";
		// Only show how many times a replay has been downloaded if it has been downloaded to begin with...
		if ($row['replay_downloads'] > 0) {
		    $downloadDisplay = " (".$row['replay_downloads'].")";
		}
		echo $sdel."<a href=\"download-replay.php?reported_on=".urlencode($row['reported_on'])."\">Download</a>".$downloadDisplay.$edel;
    } else {
        echo $sdel."No".$edel;
    }
?>
</td>
</tr>
<?
}
?>
</tbody>
</table>
</form>

<br />
<?
require('bottom.php');
?>
