<?php
$date = date("H:i d-m-y");
$sql = "UPDATE $playerstable SET losses = losses + 1, games = games + 1, streakwins = 0, " .
"streaklosses = streaklosses + 1, active = 1, LastGame = '$date - Loss vs $winner' WHERE name='$loser'";
//echo "loser: $sql <br/>";
$result = mysql_query($sql) or die("update loser failed");
 
$sql = "UPDATE $playerstable SET wins = wins + 1, games = games + 1, streakwins = streakwins + 1, ".
"streaklosses = 0, active = 1, LastGame = '$date - Win vs $loser' WHERE name = '$winner'";
//echo "winner: $sql <br/>";
$result = mysql_query($sql) or die("update winner failed");




?>