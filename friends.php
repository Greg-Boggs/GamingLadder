<?php
/**
 * This page generates a list of all ladder players in a format to add to the Wesnoth preferences
 * file.  It will mark them all as friends in your games of wesnoth
 */

require('conf/variables.php');

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

// If we are downloading the friends page, we only want the friends line, nothing else.
if (isset($_GET['download'])) {
    echo "friends=\"$names\"";
    exit;
}
require('top.php');
?>
<h1>Friends List</h1>
<p>In the world of Wesnoth you can cuddle as well as combat, with happy tree friends all is possible. If you cut out the info below and edit your corresponding line in your wesnoth <i>preference</i> file, all the ladder players will be marked as your friends with the !-mark in the official server lobby, making it much easier to know who to bug for a ladder game. </p>
<p>Tip of the Wose: Make sure you backup your preference file before editing it.</p>
<textarea rows='10' cols='80'><?php echo "friends=\"$names\""; ?></textarea>
<p>Players in list: <?php echo count($friends); ?></p>
<p>Tips from the Wose: Keep in mind that you need to update your preference file every now and then to include all the new players. It's easy to also add just the new ones: They're ordered in ascending joining order, so just use ctrl+f inside the textbox above to find the last name in your preference file, and copy &amp; paste all the ones after that, including the coma.</p>
<h2>Semi Automated Friends Update</h2>
<p>If you want to more easily and more automatically get your friends list, you can download it in text form by requesting <?php echo $directory ?>/friends.php?download. This will return a text string with the friends in it.</p>
<p>WARNING: The following comes without warranty and my delete your preferences file, but some have had success with the following shell script on linux. The script below will update your friends list with all of the ladder players.  It will remove any players on your friends list that are not ladder players!</p>
<pre>
#!/bin/bash
TMPFILE=`mktemp /tmp/wesnoth.prefs.XXXXXXXXXX`
wget --quiet -O${TMPFILE} "http://localhost/~mr-russ/gamingladder/trunk/friends.php?download"
grep -v '^friends=' ~/.wesnoth/preferences &gt; ~/.wesnoth/preferences2
cat $TMPFILE &gt;&gt; ~/.wesnoth/preferences2
mv ~/.wesnoth/preferences2 ~/.wesnoth/preferences
rm $TMPFILE
</pre>
<?php
require('bottom.php');
?>
