<?
// v 1.02
$page = "report";
require('conf/variables.php');
?>
<p class="text">
<?php 

// Lets check to see if there are Ladder cookies to see if the user is logged in. If so, we wont show the login box....


// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.

require('ladder_cookie.inc.php');

require('top.php');

if  ($loggedin == 0) {
	
	echo "<h1>Access denied.</h1><br><p>Please <a href=\"index.php\">log in</a> to report a game. " .
	"Only members of the ladder can make reports. <a href=\"join.php\">Become one</a> and compete today!</p>";
	require('bottom.php');
	exit;
}

?>

<?php
if ( isset ($_POST['undo']) ) {
	require('deletegame.inc.php');
}

$date = date("H:i d-m-y");
if ( isset ($_POST['report']) ) {
	$current_player = $nameincookie;
	$sql="SELECT * FROM $playerstable WHERE name = '$current_player'";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);

	//Make sure the user selected a loser, this should be done in javascript.
	if ($_POST[losername] == "") {
		echo "<b>You must select the name of the loser in the game you played.</b>";
		require('bottom.php');
		exit;
	}	
	require('calc_elo.inc.php');

	$sql = "UPDATE $playerstable SET losses = losses + 1, games = games + 1, streakwins = 0, " .
	"streaklosses = streaklosses + 1, LastGame = '$date - Loss vs $winner' WHERE name='$loser'";
	//echo "Winner: $sql <br/>";
	$result = mysql_query($sql) or die("update loser failed");
	 
	$sql = "UPDATE $playerstable SET wins = wins + 1, games = games + 1, streakwins = streakwins + 1, ".
	"streaklosses = 0, LastGame = '$date - Win vs $loser' WHERE name = '$winner'";
	//echo "Loser: $sql <br/>";
	$result = mysql_query($sql) or die ("update winner failed");
	$sql = "INSERT INTO $gamestable (winner, loser, date) VALUES ('$winner', '$loser', '$date')";
	//echo "game: $sql <br/>";
	$result = mysql_query($sql) or die ("failed to insert game");
;
	include 'closedb.php';
	
	echo "Thank you! Information entered.";
} else {
?>
<form name="form1" method="post">
<h3>Report Game</h3>
<table border="0" cellpadding="3" spacing="3">

<tr><td>Loser's Name</td><td><input type="text" name="byname" size="20"></td>><td><input type="submit" value="Search" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td></tr>

<tr>
<td><?php echo "<b>$nameincookie</b>";?> won over </td><td><select size="1" name="losername" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color3" ?>" class="text">
<option></option>

<?php
if ( isset($_POST['byname']) ) {
	$sql="SELECT name FROM $playerstable " .
	"WHERE name like '".$_POST['byname']."%' " .
		"ORDER BY name ASC";
	$result=mysql_query($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		echo "<option selected value=\"" .$row['name']. "\"> " .$row['name']. "</option>";
	}
}

$sql="SELECT * FROM $playerstable WHERE approved = 'yes' ORDER BY name ASC";
$result=mysql_query($sql,$db);
echo "";
while ($row = mysql_fetch_array($result)) {
?>

<option><?echo "$row[name]" ?></option>
<?php
}
?>
</select></td>
</tr>
<tr>
<td align="center" colspan="3">
<p class="text"><br><input type="Submit" name="report" value="Report Game" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></p>
</td>
</table>

<b>Warning: If you cheat you will be banned.</b><br>If <i>accidentally</i> reported a false result <input type="submit" name="undo" value="undo" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text" onclick="return confirm('Delete last game?');"> it!
</form>
</p>
<?
}
?>
<?
require('bottom.php');
?>
