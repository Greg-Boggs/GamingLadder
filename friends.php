<?php
/**
 * This page generates a list of all ladder players in a format to add to the Wesnoth preferences
 * file.  It will mark them all as friends in your games of wesnoth
 */

require('conf/variables.php');
require('top.php');
?>
<h1>Friends List</h1>
<p>In the world of Wesnoth you can cuddle as well as combat, with happy tree friends all is possible. If you cut out the info below and edit your corresponding line in your wesnoth <i>preference</i> file, all the ladder players will be marked as your friends with the !-mark in the official server lobby, making it much easier to know who to bug for a ladder game. </p>
<p>Tip of the Wose: Make sure you backup your preference file before editing it.</p>
<?php

// Get a list of all the player names
$sql = "SELECT name FROM $playerstable ORDER BY player_id ASC";
$result = mysql_query($sql, $db);

$friends = array();
while ($row = mysql_fetch_array($result)) {
    // Onlly display names that are valid wesnoth multiplayer names.
    if (preg_match("/^[a-zA-Z0-9\-\_]+$/i", $row['name'])) {
		array_push($friends, htmlentities($row['name']));
    }
}
$names = implode(',', $friends);
?>
<textarea rows='10' cols='80'><?php echo "friends=\"$names\""; ?></textarea>
<p>Players in list: <?php echo count($friends); ?></p>
<p>Tips from the Wose: Keep in mind that you need to update your preference file every now and then to include all the new players. It's easy to also add just the new ones: They're ordered in ascending joining order, so just use ctrl+f inside the textbox above to find the last name in your preference file, and copy &amp; paste all the ones after that, including the coma.</p>
<?php
require('bottom.php');
?>
