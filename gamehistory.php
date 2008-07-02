<?
session_start();
// v 1.01
require('conf/variables.php');
require_once 'autologin.inc.php';
require('top.php');

if (isset($_REQUEST['selectname'])) {
    $playerquerystring = "&selectname=".$_REQUEST['selectname'];
} else {
    $playerquerystring = "";
}
?>
<p class="header">Played games.</p>
<form method="post" action="gamehistory.php?startplayed=0&finishplayed=<?echo"$numgamespage"?>">
<p class="text"><a href="gamehistory.php?startplayed=0&finishplayed=<?echo"$numgamespage"?>"><font color="<?echo"$color1"?>">View all games</font></a> |
View games from: <select size="1" name="selectname" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
$sortby = "name ASC";
$sql = "SELECT name FROM $playerstable ORDER BY $sortby";
$result = mysql_query($sql,$db);

while ($row = mysql_fetch_array($result)) {
    if ($_REQUEST[selectname] == $row[name]) {
        echo"<option selected>$row[name]</option>";
    } else {
        echo"<option>$row[name]</option>";
    }
}
?>
</select> <input type="Submit" name="submit2" value="View." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
</form>
<?
if (isset($_REQUEST['selectname'])) {
    $where = "WHERE winner = '".$_REQUEST['selectname']."' OR loser = '".$_REQUEST['selectname']."' ";
} else {
    $where = "";
}
$sql="SELECT count(*) FROM $gamestable ".$where;
$result = mysql_query($sql, $db);
$row = mysql_fetch_row($result);
$yo = $row[0];

$number = 0;
$link = 1;
$finishnumber = $numgamespage;
$startnext = $_GET[startplayed] + $numgamespage;
$startprevious = $_GET[startplayed] - $numgamespage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
    echo "&nbsp;|&nbsp;<a href='gamehistory.php?startplayed=$startprevious&finishplayed=$finishnumber".$playerquerystring."'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
    echo "&nbsp;<a href='gamehistory.php?startplayed=$number&finishplayed=$finishnumber".$playerquerystring."'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
    $number = $number + $numgamespage;
    $link = $link + 1;
}
if ($startplayed < $yo - $numgamespage) {
    echo "<a href='gamehistory.php?startplayed=$startnext&finishplayed=$finishnumber".$playerquerystring."'><font color='$color1'>></font></a>&nbsp;|";
}

if ($approvegames == "yes") {
    $tablewidth = "80%";
    $width = "25%";
} else {
    $width = "33%";
    $tablewidth = "60%";
}
?>
<br /><br />

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#games").tablesorter({sortList: [[0,1]], widgets: ['zebra'] }); 
    } 
); 
</script>

<table width="<?echo"$tablewidth" ?>" id="games" class="tablesorter">
<thead>
<tr>
<th>Reported Time</th>
<th>Winner</th>
<th>Loser</th>
<th>Winner New Rating</th>
<th>Loser New Rating</th>
<th>Replay</th>
<?
if ($approvegames == "yes") {
?>
<th>Status</th>
<?
}
?>
</tr>
</thead>
<tbody>
<?
if ($_REQUEST[selectname]) {
    $sql = "SELECT withdrawn, contested_by_loser, DATE_FORMAT(reported_on, '".$GLOBALS['displayDateFormat']."') as report_time, reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as is_replay, replay_downloads FROM $gamestable WHERE winner = '$_REQUEST[selectname]' OR loser = '$_REQUEST[selectname]'  ORDER BY reported_on DESC LIMIT $_GET[startplayed], $_GET[finishplayed]";
} else {
    $sql = "SELECT withdrawn, contested_by_loser, DATE_FORMAT(reported_on, '".$GLOBALS['displayDateFormat']."') as report_time, reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, length(replay) as is_replay, replay_downloads FROM $gamestable ORDER BY reported_on DESC LIMIT $_GET[startplayed], $_GET[finishplayed]";
}
$result = mysql_query($sql,$db);
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
?>

<tr>
<td><?echo $sdel.$row['report_time'].$edel ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[winner]\">$row[winner]</a>".$edel ?></td>
<td><?echo $sdel."<a href=\"profile.php?name=$row[loser]\">$row[loser]</a>".$ededl ?></td>
<td><?echo $sdel.$row['winner_elo']." (".$row['winner_points'].")".$edel ?></td>
<td><?echo $sdel.$row['loser_elo']." (".$row['loser_points'].")".$edel ?></td>
<td>
<?php
    if ($row['is_replay']  > 0) {
       echo $sdel."<a href=\"download-replay.php?reported_on=".urlencode($row['reported_on'])."\">Download</a> (".$row['replay_downloads'].")".$edel;
    } else {
       echo $sdel."No".$edel;
    }
?>
</td>
<?php
    if ($approvegames == "yes") {
?>
<td><?echo $sdel."$status".$edel ?></td>
<?
    }
?>
</tr>
<?
}
?>
</tbody>
</table>

<br />
<?
require('bottom.php');
?>
