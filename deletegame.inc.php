<?php
include ("logincheck.inc.php");
//deletegame.inc.php
	echo "Unding last game: please do not abuse this feature. Admin can revert undo's if your reading this by mistake.";


$winner_name = $nameincookie;

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
$row = mysql_fetch_array($result);

$sql = "DELETE FROM `$gamestable` WHERE `$gamestable`.`game_id` = $last_game LIMIT 1;";
$result = mysql_query($sql) or die("failed to delete the last game");
$row = mysql_fetch_array($result);

?>