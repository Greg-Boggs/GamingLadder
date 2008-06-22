<?php
require('conf/variables.php');

// We have ajax lower down on the page, we handle it here and then exit.
// This keeps the ajax code with the page that is calling it.
// Unfortunately we are stuck with 'q' as the query variable as it's hardcoded into the jquery autocomplete module.
if (isset($_GET['q'])) {
    $q = strtolower($_GET["q"]);
    $query = "SELECT player_id, name from $playerstable WHERE name like '$q%' ORDER BY name";
    $result = mysql_query($query) or die("fail");
    while ($row = mysql_fetch_array($result)) {
	    echo $row['name'] . "|" . $row ['player_id'] . "\n";
    }
    exit;
}


// Lets check to see if there are Ladder cookies to see if the user is logged in. If so, we wont show the login box....
// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.
require('ladder_cookie.inc.php');
require('top.php');

if ($loggedin == 0) {
    echo "<h1>Access denied.</h1><br><p>Please <a href=\"index.php\">log in</a> to report a game. " .
    "Only members of the ladder can make reports. <a href=\"join.php\">Become one</a> and compete today!</p>";
    require('bottom.php');
    exit;
}

if (isset($_POST['report'])) {
?>
<h3>Report Game Results</h3>
<?php    
    $current_player = $nameincookie;
    $sql = "SELECT * FROM $playerstable WHERE name = '$current_player'";
    $result = mysql_query($sql, $db);
    $row = mysql_fetch_array($result);

	// Make sure the user selected a loser, this should be done in javascript.
	if ($_POST[losername] == "") {
		echo "<p><b>You must select the name of the loser!</b></p><p>Please return the the <a href='report.php'>report</a> page and select a name.</p>";
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
<p>Congratulations <?php echo $current_player; ?> you have defeated <?php echo $loser; ?>!</p>

<table border="1" cellpadding="5" cellspacing="0">
	<tr>
        <th></th>
        <th><?php echo $current_player; ?></th>
        <th><?php echo $loser; ?></th>
	</tr>
	<tr>
        <td>Provisional Player</td>
        <td><?php echo $winnerStats[1] ? "Yes" : "No"; ?></td>
        <td><?php echo $loserStats[1] ? "Yes" : "No"; ?></td>
    </tr>
    <tr>
        <td>Rating change</td>
        <td><?php echo $winnerChange; ?></td>
        <td><?php echo $loserChange; ?></td>
    </tr>
    <tr>
        <td>Old Ratings</td>
        <td><?php echo $winnerStats[0]; ?></td>
        <td><?php echo $loserStats[0]; ?></td>
    </tr>
    <tr>
        <td>New Ratings</td>
        <td><?php echo $winnerRating; ?></td>
        <td><?php echo $loserRating; ?></td>
    </tr>
    </table>

<?php
	require('update_player.inc.php');

	$sql = "INSERT INTO $gamestable (winner, loser, date, elo_change) VALUES ('$winner', '$loser', '$date', '$winnerChange')";
	//echo "game: $sql <br/>";
	$result = mysql_query($sql) or die ("failed to insert game");

	echo "<p>Thank you! Information entered. Check your <a href=\"playerdata.php\">current position.</a></p>";
} else {
?>
<form name="form1" method="post" action="report.php">
<h3>Report Game</h3>
<p>
    <?php echo $nameincookie; ?> won over 
    <input type="text" name="losername" id="CityAjax" value="" style="width: 200px;" />
    <input type="submit" name="report" value="Report Game" onclick="lookupAjax();" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>"/>
</p>
<b>Warning: If you cheat you will be banned.</b><br />If <i>accidentally</i> reported a false result 

<a href="undogame.php" onclick="return confirm('Delete your last win?');"> undo it!</a> 
</form>
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
		"report.php",
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
