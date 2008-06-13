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

if ( isset ($_POST['report']) ) {

	$current_player = $nameincookie;
	$sql="SELECT * FROM $playerstable WHERE name = '$current_player'";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);

	//Make sure the user selected a loser, this should be done in javascript.
	if ($_POST[losername] == "") {
		echo "<b>You must select the name of the loser! </b>";
		echo '<a href="report.php">Go back.</a>';
		require('bottom.php');
		exit;
	}	
	$winner = $current_player;
	$loser = $_POST['losername'];
	if ($winner == $loser) { 
		echo "No playing against yourself! ";
		echo '<a href="report.php">Go back.</a>';
		require('bottom.php');
		
	}
	require('calc_elo.inc.php');

?>
Congradulations <?php echo $current_player; ?> you have defeated <?php echo $loser; ?>!<p>

<table border="1" cellpadding="5" cellspacing="0">
    <tr><td>Your rating change was: <?php echo $winnerChange; ?> points.</td><td>Your opponent's rating change was: <?php echo $loserChange; ?> points.</td></tr>
    <tr><td>Your old rating was: <?php echo $winnerStats[0]; ?>.</td> <td>Your opponent was: <?php echo $loserStats[0]; ?>. </td></tr>

    <tr><td>Your new rating is: <?php echo $winnerRating; ?>.</td><td>Your opponent's new rating is: <?php echo $loserRating; ?>. </td></tr>
    </table>
<p>

<?php
	require('update_player.inc.php');

	$sql = "INSERT INTO $gamestable (winner, loser, date, elo_change) VALUES ('$winner', '$loser', '$date', '$winnerChange')";
	//echo "game: $sql <br/>";
	$result = mysql_query($sql) or die ("failed to insert game");

	echo "Thank you! Information entered. Check your <a href=\"playerdata.php\">current position.</a>";
} else {
?>
<form name="form1" method="post" >
<h3>Report Game</h3>
<table border="0" cellpadding="3" spacing="3">

<tr><td>
		<?php echo $nameincookie; ?> won over 
		<input type="text" name="losername" id="CityAjax" value="" style="width: 200px;" /></td><td>
		<input type="submit" name="report" value="Report Game" onclick="lookupAjax();" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>"/>
</td></tr>
<tr>
</table>

<b>Warning: If you cheat you will be banned.</b><br>If <i>accidentally</i> reported a false result 

<a href="undogame.php" onclick="return confirm('Delete your last win?');"> undo it!</a> 
</form>
</p>
<?
}
?>
<script type="text/javascript">
var found = true;


function findValue(li) {

	if( li == null ) { return alert("No match!"); }


	// if coming from an AJAX call, let's use the CityId as the value
	if( !!li.extra ) var sValue = li.extra[0];

	// otherwise, let's just display the value in the text box
	else var sValue = li.selectValue;

	//alert("The value you selected was: " + sValue);
}

function selectItem(li) {
	findValue(li);
}

function formatItem(row) {
	return row[0];
}

function lookupAjax(){
	var oSuggest = $("#CityAjax")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

function lookupLocal(){
	var oSuggest = $("#CityLocal")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

$(document).ready(function() {
	$("#CityAjax").autocomplete(
		"autocomplete_ajax.php",
		{
			delay:10,
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:selectItem,
			onFindValue:findValue,
			formatItem:formatItem,
			autoFill:true
		}
	);

});
</script>
<?
require('bottom.php');
?>
