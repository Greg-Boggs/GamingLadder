<?
// v 1.01

// This page generates a list of all ladder players and the line thats needed in the Wesnoth preferences
// file to mark them all as friends...

$page = "playedgames";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>
<h1>Friends List</h1>
<table width=800px>
	<tr>
	<td>
<p>In the world of Wesnoth you can cuddle as well as combat, with happy tree friends all is possible. If you cut out the info below and edit your corresponding line in your wesnoth <i>preference</i> file, all the ladder players will be marked as your friends with the !-mark in the official server lobby, making it much easier to know who to bug for a ladder game. <br><br>Tip of the Wose: Make sure you backup your preference file before editing it.</p><br><br>
	</td>
	</tr>
</table>
<?

// Get the number of rows in the table...


$result = mysql_query("SELECT * FROM $playerstable");
$num_rows = mysql_num_rows($result);
// Display the results...this would the number of players in the table, not the number ofplayers in the table with valid names
// echo "Players in the list: $num_rows <br><br><br>";


// Now its time to get all the names of the players in the ladder...
$sortby = "player_id ASC";
$sql="SELECT * FROM $playerstable ORDER BY $sortby";
$result=mysql_query($sql,$db);

// counts the number of player names we've written to the variable
$counter= 0;
$dontinclude = 0;


while ($row = mysql_fetch_array($result)) {
	// echo"$row[name]";
	
		// sort out all wesnoth multiplayer unvalid player names...
		
		if (!preg_match("/^[a-zA-Z0-9\-\_]+$/i", $row[name])) {
			
			$dontinclude = 1;	}
			
			else {
				$dontinclude = 0;
				}
		
		
		if (($counter == 0) && ($dontinclude == 0) ) { 
			// in the following we take the first name and also skip out on the coma-delimiter...
			$names = "$row[name]"; 
		}
		
		
		
		if (($counter > 0) && ($counter <= $num_rows) && ($dontinclude == 0)) { 
			// in the rest we need all the previous names and also the coma-delimitter to separate them...
			$names = "$names,$row[name]"; 
		}
	
	if ($dontinclude == 0) {$validnames++;}
	
	$counter = $counter + 1;
	
	}
	
	?>

<textarea rows="5" cols="80">

	<?php echo "friends=\"$names\""; ?>

</textarea>

<br><br>
<table width=800px>
	<tr>
	<td>
<p>Players in list: <?php echo "$validnames"; ?><br><br>Tips from the Wose: Keep in mind that you need to update your preference file every now and then to include all the new players. It's easy to also add just the new ones: They're ordered in ascending joining order, so just use ctrl+f inside the textbox above to find the last name in your preference file, and copy & paste all the ones after that, including the coma.</p>
	</td>
	</tr>
</table>
<?
require('bottom.php');
?>