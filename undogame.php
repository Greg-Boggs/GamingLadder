<?php
include ("logincheck.inc.php");
$winner_name = $nameincookie;

if ( isset($_POST['submit']) ) {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE `winner` LIKE '$winner_name' " .
		"ORDER BY `game_id` DESC " .
		"LIMIT 0 , 1" ;

	$result = mysql_query($sql) or die("failed to select last game");
	$row = mysql_fetch_array($result);
	$last_game = $row[0];

	$sql = "INSERT INTO `$deletedgames` (`game_id`, `winner`, `loser`, `date`, `elo_change`) VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3] ', '$row[4]')";

	$result = mysql_query($sql) or die("failed to save the last game");

	$sql = "DELETE FROM `$gamestable` WHERE `$gamestable`.`game_id` = $last_game LIMIT 1;";
	$result = mysql_query($sql) or die("failed to delete the last game");
	echo "Game:$row[1] vs $row[2] on $row[3] removed";
	include ("bottom.php");

} else {
	$sql= "SELECT * ".
		"FROM `$gamestable` " .
		"WHERE `winner` LIKE '$winner_name' " .
		"ORDER BY `game_id` DESC " .
		"LIMIT 0 , 1" ;

	$result = mysql_query($sql) or die("failed to select last game");
	$row = mysql_fetch_array($result);

	echo "Undo last game:$row[1] vs $row[2] on $row[3]? ";
?>
<p>
<form method="post">
<input type="submit" value="Undo Game" name="submit">
</form>

<b>Please Notice:</b> Your game history is updated instantly. However, <i>the Elos won't change</i> unless an Admin authorizes it. Please contact <?php echo $adminemail ?> and tell us to update it. Meanwhile you can report games as usual - all will be totally correct once we do our magic.<br>
<?php
	include ("bottom.php");
}
?>