<?
// v 1.01
$page = "playedgames";
require('conf/variables.php');
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
// Display the results...
echo "Players in the list: $num_rows <br><br><br>";


// Now its time to get all the names of the players in the ladder...
$sortby = "name ASC";
$sql="SELECT * FROM $playerstable ORDER BY $sortby";
$result=mysql_query($sql,$db);

// counts the number of player names we've written to the variable
$counter= 0;



while ($row = mysql_fetch_array($result)) {
	// echo"$row[name]";
	
		
		if ($counter == 0) { 
			// in the following we take the first name and also skip out on the coma-delimiter...
			$names = "$row[name]"; 
		}
		
		
		
		if (($counter > 0) && ($counter <= $num_rows)) { 
			// in the rest we need all the previous names and also the coma-delimitter to separate them...
			$names = "$names,$row[name]"; 
		}
	
	
	$counter = $counter + 1;
	
	}
?>

<textarea rows="10" cols="80">

	<?php echo "friends=\"$names\""; ?>

</textarea>

<br>
<?
require('bottom.php');
?>
