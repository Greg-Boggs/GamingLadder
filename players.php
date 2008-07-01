<?
session_start();
require('conf/variables.php');
require_once 'autologin.inc.php';
require('top.php');
?>
<h2>Find Players</h2>
<br />
<!-- Form: Search By Name -->
<form method="GET"> 
	Search by Name: <input type="text" name="byname" />
	<input type="submit" value="Submit" />
</form>
<a href="players.php?startplayers=0&finishplayers=100" >Show All</a>

<?
$sql="SELECT count(*) FROM $playerstable";
$result=mysql_query($sql,$db);
$yo = mysql_fetch_row($result);
$yo = $yo[0];
$number = 0;
$link = 1;
$finishnumber = $numplayerspage;
$startnext = $_GET[startplayers] + $numplayerspage;
$startprevious = $_GET[startplayers] - $numplayerspage;
echo "<p class='text'>Go to page:";
if ($startprevious >= 0) {
echo "&nbsp;|&nbsp;<a href='players.php?startplayers=$startprevious&finishplayers=$finishnumber'><font color='$color1'><</font></a>&nbsp;|";
}
while ($number < $yo) {
echo "&nbsp;<a href='players.php?startplayers=$number&finishplayers=$finishnumber'><font color='$color1'>$link</font></a>&nbsp;|&nbsp;";
$number = $number + $numgamespage;
$link = $link + 1;
}
if ($startplayers < $yo - $numplayerspage) {
echo "<a href='playersgames.php?startplayers=$startnext&finishplayers=$finishnumber'><font color='$color1'></font></a>&nbsp;|";
}
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#player").tablesorter({sortList: [[1,0]], widgets: ['zebra'] }); 
    } 
); 
</script>
<table id="player" class="tablesorter">
<thead>
<tr>
<th>&nbsp;</th>
<th>Player</th>
<th>Games</th>
<th>Wins</th>
<th>Losses</th>
<th>Rating</th>
<th>Streak</th>
</tr>
</thead>
<tbody>
<?php

$sql = "select * from (select a.name, g.reported_on, 
       CASE WHEN g.winner = a.name THEN g.winner_elo ELSE g.loser_elo END as rating,
       CASE WHEN g.winner = a.name THEN g.winner_wins ELSE g.loser_wins END as wins,
       CASE WHEN g.winner = a.name THEN g.winner_losses ELSE g.loser_losses END as losses,
       CASE WHEN g.winner = a.name THEN g.winner_games ELSE g.loser_games END as games,
       CASE WHEN g.winner = a.name THEN g.winner_streak ELSE g.loser_streak END as streak
       FROM (select name, max(reported_on) as latest_game FROM $playerstable JOIN $gamestable ON (name = winner OR name = loser) GROUP BY 1) a JOIN $gamestable g ON (g.reported_on = a.latest_game)) standings right join $playerstable USING (name)";

//if byname is set than, add the where clause
if ( isset($_GET['byname']) ) {
	$sql .= " WHERE name like '%".$_GET['byname']."%' ";
}
$sql .= "GROUP BY name ORDER BY name ASC";

//these two Variables had to be checked because if you search by players with the form, these two variables aren't set anymore
if ( isset($_GET[startplayers]) && isset($_GET[finishplayers]) ) { 
	$sql .= " LIMIT $_GET[startplayers], $_GET[finishplayers]";
}

$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
    if ($row["approved"] == "no") {
	$namepage = "<span style='color: #FF0000'>$row[name]</span>";
    } else {
	$namepage = $row[name];
    }

    $games = $row['games'] == "" ? 0 : $row['games'];
    $wins = $row['wins'] == "" ? 0 : $row['wins'];
    $losses = $row ['losses'] == "" ? 0 : $row['losses'];
    $rating = $row['rating'] == "" ? BASE_RATING : $row['rating'];
    $streak = $row['streak'] == "" ? 0 : $row['streak'];

?>
<tr>
<td align="right"><?echo "<img src='graphics/flags/$row[country].bmp' align='absmiddle' border='1'>"?></td>
<td><?php echo "<a href='profile.php?name=$row[name]'>$namepage</a>"?></td>
<td><?echo "$games" ?></td>
<td><?echo "$wins" ?></td>
<td><?echo "$losses" ?></td>
<td><?echo "$rating" ?></td>
<td><?echo "$streak" ?></td>
</tr>
<?
}
?>
</tbody>
</table>
<?
require('bottom.php');
?>

