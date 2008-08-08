<?php
session_start();
require('conf/variables.php');
require_once 'autologin.inc.php';
require('top.php'); ?>

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#ladder").tablesorter({sortList: [[0,0]], widgets: ['zebra'] }); 
    } 
); 
</script>

<?php


// Let's see what group we're in. We want the info in order to mark the row with a different colour if the user viewing the page is logged in and his name is in that row...

if ($_SESSION['username'] != ""){
	
$result=mysql_query($standingsSqlWithRestrictions." LIMIT ".$playersshown,$db) ;
$row = mysql_fetch_array($result);

// Init variables..

$classbaseelo = $row['rating'];
$rank = 0;


@mysql_data_seek($result, 0);

	while ($row = mysql_fetch_array($result)) {


	if  ($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE) || ($row['rating'] >= ($classbaseelo)) ) {
								
			$rank++;
		


/*		if  ($row['rating'] <= ($classbaseelo - GADDER_ELO_RANGE)) {
				$rank++;
				
	*/			
				
				$classbaseelo = $row['rating'];
				if ($row['name'] == $_SESSION['username']) {$mygroup = $rank;}
				
				
			} else {
			
			if ($row['name'] == $_SESSION['username']) {$mygroup = $rank;}
				
			}
			
	}

}
			
?>

<h2>Skill Classes</h2>
<table id="ladder" class="tablesortergadder">
<thead>
<tr>
<th align="left" width='7%'>Class</th>

<th align="left">Avatar</th>

<th align="left">Members&nbsp; &nbsp;</th>
</tr>
</thead>

<?php

// display the results...

// reset  vars

$result=mysql_query($standingsSqlWithRestrictions." LIMIT ".$playersshown,$db) ;
$row = mysql_fetch_array($result);

$rank = 0;
$classbaseelo = $row['rating'];

@mysql_data_seek($result, 0);


while ($row = mysql_fetch_array($result)) {

// Set row colouring...
	if  ($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE) || ($row['rating'] >= ($classbaseelo)) ) {
								
			$rank++;
		}

	if ($rank == $mygroup) { // Im to lazy to find out why the -1 is needed but I think it has to with the order of the code...
		$trclass = "<tr class='myrow'>";
		$myclassrank++;
		} else {
			$trclass = "<tr>";
	}


// Set the flag if it's enabled. Players which haven't specified country won't have a flag shown at all.

if (GADDER_FLAGS == 1){
	
		if ($row['country'] != "No Country"){	
		$flagprefix = "<img src='graphics/flags/".$row['country'].".bmp'> &nbsp;";
		} else { 
		$flagprefix ="";
		}
	
	}
	
	// Finally time to display it all..
	
	if  (($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE)) || ($row['rating'] >= $classbaseelo)) {

			
			echo "</td></tr>$trclass<td class=\"gadderrank\">$rank</td><td><img src='avatars/".$row['Avatar'].".gif'></td><td>$flagprefix ";?>
			
			<a title='<?php  echo $row['rating'] . " points"; ?>' href='profile.php?name=<?php echo $row['name']; ?>'><?php echo $row['name'] ."</a>";
			
			if (($myclassrank) && ($row['name'] == $_SESSION['username'] )) { 
			echo " (".$myclassrank.") ";
			}
			
			$classbaseelo = $row['rating'];

		} else {
	
			echo ", $flagprefix";?>
			<a title='<?php  echo $row['rating'] . " points"; ?>' href='profile.php?name=<?php echo $row['name']; ?>'><?php echo $row['name'] ."</a>";
			
			
			if (($myclassrank) && ($row['name'] == $_SESSION['username'] )) { 
			echo " (".$myclassrank.") ";
			}
			
		}
	
}
echo "</table>";

?>

<p class="copyleft">Players in the same skill class are estimated to be of about the same skill, hence they have the same rank in here since rank equals the skill class you are in. The elo range used to determine the class belonging is currently set to <?php echo " ". GADDER_ELO_RANGE ;?>. 
1:st class = all players that are <= <?php echo " ". GADDER_ELO_RANGE ;?> points from the number 1 players rating. The player that is first to be excluded creates 2:nd class, and so on. To <i>show up</i> in the skill class listing above you must have played >= <?php echo "$gamestorank"; ?> games, have a rating of >= <?php echo "$ladderminelo"; ?> & have played within <?php echo "$passivedays"; ?> days. </p><br />

<?php 
require('bottom.php');
?>
