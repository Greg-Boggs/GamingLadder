<?php
session_start();
require('conf/variables.php');
require_once 'autologin.inc.php';
require('top.php');

$personalladder = isset($_GET['personalladder']) ? $_GET['personalladder'] : "";

?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#ladder").tablesorter({sortList: [[0,0]], widgets: ['zebra'] }); 
    } 
); 
</script>
<?php

$result=mysql_query($standingsSqlWithRestrictions." LIMIT ".$playersshown,$db) ;
//echo "<br />".$sql;

##die ("failed to select players");
$cur=1;

unset($myrank);
while ($row = mysql_fetch_array($result)) {
    if ($row['name'] == $personalladder) {
        $myrank = $cur;
        break;
    }
    $cur++;
}

// I'm not ranked, it's not really my section of the ladder at all.
if (!isset($myrank)) {
    $personalladder = "";
}
?>
<h2><?php echo $personalladder ?> Ladder Standings</h2>

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
@mysql_data_seek($result, 0);
$cur = 1;

// If I don't have a rank, and requesting a personal ladder, display a message to that effect.
if (!isset($myrank) && isset($_GET['personalladder'])) {
    echo "<p>You are not ranked, the default ladder will be shown instead of the personal.</p>";
}

while ($row = mysql_fetch_array($result)) {
	$namepage = "$row[name]";

    if (isset($myrank) && ($cur < ($myrank - 10) || $cur > ($myrank + 10))) {
        $cur++;
        continue;
    }


if ($row[streak] >= $hotcoldnum) {
    $picture = 'images/streakplusplus.gif';
} else if ($row[streak] <= -$hotcoldnum) {
    $picture = 'images/streakminusminus.gif';
} else if ($row[streak] > 0) {
    $picture = 'images/streakplus.gif';
} else if ($row[streak] < 0) {
    $picture = 'images/streakminus.gif';
} else {
    $picture = 'images/streaknull.gif';
}
if (($personalladder == $namepage) || ($_SESSION['username'] == $namepage)) {
echo '<tr class="myrow">';
} else {
?>
<tr>
<?php 
}
?>
<td><?php echo "$cur"?></td>	
<td><?php echo "<img border='0' height='20px' src='avatars/$row[Avatar].gif' alt='avatar' />"?><a name="<?php echo $namepage ?>"></a></td>
<td><a href='profile.php?name=<?php echo "$namepage '> $namepage"; ?></a> </td>
<td><?php echo "$row[rating]"; ?></td>
<td><?php printf("%.0f", $row['wins']/$row['games']*100); ?></td>
<td><?php echo "$row[wins]" ?> </td>
<td><?php echo "$row[losses]" ?></td>
<td><?php echo ($row[games]); ?></td>
<td><?php echo "$row[streak]"?></td>
</tr>
<?php 
	$cur++;
}
?>
</tbody>
</table>

<p class="copyleft">To <i>show up</i> in the ladder above you must have played >= <?php echo "$gamestorank"; ?> games, have a rating of >= <?php echo "$ladderminelo"; ?> & have played within <?php echo "$passivedays"; ?> days. Don't worry  if you haven't played for a while. All it takes is one game to become active again. Your rating doesn't decay while you are gone. 1500 is the <i>average</i> skilled player, new players will have less and vets more. Hence, don't quit playing if you rate below 1500.</p>

<?php 
require('bottom.php');
?>
