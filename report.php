<?php
session_start();
require('conf/variables.php');
require('autologin.inc.php');
require('logincheck.inc.php');

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
require('top.php');

if (isset($_POST['report'])) {
?>
<h3>Report Game Results</h3>
<?php    
    $current_player = $_SESSION['username'];
    $sql = "SELECT name FROM $playerstable WHERE name = '".$_POST['losername']."' and Confirmation = 'Ok'";
    $result = mysql_query($sql, $db);

	// Make sure the user selected a loser, this should be done in javascript.
	if ($_POST['losername'] == "") {
		echo "<p><b>You must select the name of the loser!</b></p><p>Please return the the <a href='report.php'>report</a> page and select a name.</p>";
		require('bottom.php');
		exit;
	}
    // Make sure the selected user is actually a ladder member
    if (mysql_num_rows($result) == 0) {
        echo "<p><b>You must select a valid and confirmed ladder player!</b></p><p>Please return the the <a href='report.php'>report</a> page and select a valid opponent.</p>";
        require 'bottom.php';
        exit;
    }

	$winner = $current_player;
	$loser = $_POST['losername'];
	if ($winner == $loser) { 
		echo "No playing against yourself! ";
		echo '<a href="report.php">Go back.</a>';
		require('bottom.php');	
        exit;
	}

    $draw = false;
    $failure = false;
    $error = "";
    // Do all the replay stuff to create a replay in the database
    // We use the tmp_name to detect if somebody actually filled in a file for upload.
    if (isset($_FILES["uploadedfile"]["name"]) && $_FILES['uploadedfile']['name'] != "") {
        if (($_FILES["uploadedfile"]["size"] < 200000) && ($_FILES["uploadedfile"]["type"] == "application/x-gzip")){
	        $replay = file_get_contents($_FILES['uploadedfile']['tmp_name']);
        } else {
            $failure = true;
            $error = "You attempted to upload a replay, it wasn't small enough (<200Kb) or it wasn't the correct type (*.gz)";
        }
    } else {
        $replay = null;
    }

    if (!$failure) {
        require_once 'include/elo.class.php';
    
        $elo = new Elo($db);
        $result = $elo->ReportNewGame($winner, $loser, $draw, $replay);
        if ($result === false) {
            $failure = true;
            $error = "There was a failure in reporting your game, please try again.";
        }
    }
    if ($failure) {
        echo "<p>ERROR: ".$error."</p>";
    } else {

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
        <td><?php echo $result['winnerProvisional'] ? "Yes" : "No"; ?></td>
        <td><?php echo $result['loserProvisional'] ? "Yes" : "No"; ?></td>
    </tr>
    <tr>
        <td>Rating change</td>
        <td><?php echo $result['winnerChange']; ?></td>
        <td><?php echo $result['loserChange']; ?></td>
    </tr>
    <tr>
        <td>Old Ratings</td>
        <td><?php echo $result['winnerRating']; ?></td>
        <td><?php echo $result['loserRating']; ?></td>
    </tr>
    <tr>
        <td>New Ratings</td>
        <td><?php echo $result['winnerRating'] + $result['winnerChange']; ?></td>
        <td><?php echo $result['loserRating'] + $result['loserChange']; ?></td>
    </tr>
    </table>
<?php
	echo "<p>Thank you! Information entered. Check your <a href=\"ladder.php?personalladder=".urlencode($_SESSION['username'])."\">current position.</a></p>";
    }
} else {
?><table>
<form name="form1" enctype="multipart/form-data" method="post" onsubmit="return confirm('Report win against ' + this.losername.value +'?')" action="report.php">
<h3>Report Game</h3>


<p>

<table>

<tr>
<td><?php echo $_SESSION['username']; ?> won over</td>
    
	<td><input type="text" name="losername" id="CityAjax" value="" style="width: 200px;" /></td><br />
</tr>

    <input type="hidden" name="MAX_FILE_SIZE" value="200000" />
    
	<tr><td>.gz replay to upload</td><td><input name="uploadedfile" type="file" /></td></tr><br />
	
	<tr><td>sportsmanship</td><td><select size="1" name="sportsmanship">
		<option selected>3</option>
		<option>1</option>
		<option>2</option>
		<option>3</option>
		<option>4</option>
		<option>5</option>
	</select>
	</td>
		
	<br/>
	
	
</p>

<tr><td valign="top">
<p valign="top">game comment</p></td>
<td valign="top"><textarea rows="5" cols="60"> </textarea> </td>
</tr>
<tr><td>
	<input type="submit" name="report" value="Report Game" onclick="lookupAjax();" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>"/>
	</td></tr>
</table>

</form>

<br /><br /><b>Warning: If you cheat you will be banned.</b><br />If <i>accidentally</i> reported a false result 

<a href="undogame.php" onclick="return confirm('Delete your last win?');"> undo it!</a> 


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
echo "<br /><br />";
require('bottom.php');
?>
