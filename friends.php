<?php
/**
 * This page generates a list of all ladder players in a format to add to the Wesnoth preferences
 * file.  It will mark them all as friends in your games of wesnoth
 */

require('conf/variables.php');

// Get a list of all the player names
$sql = "SELECT name FROM $playerstable WHERE confirmation <> 'Deleted' ORDER BY player_id ASC";
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
session_start();
require 'autologin.inc.php';
require('top.php');
?>
<h1>Friends List</h1>
<p>In the world of Wesnoth you can cuddle as well as combat, with happy tree friends all is possible. If you cut out the info below and edit your corresponding line in your wesnoth <i>preference</i> file, all the ladder players will be marked as your friends with the !-mark in the official server lobby, making it much easier to know who to bug for a ladder game. </p>
<p>Tip of the Wose: Make sure you backup your preference file before editing it.</p>
<textarea rows='10' cols='80'><?php echo "friends=\"$names\""; ?></textarea>
<p>Players in list: <?php echo count($friends); ?></p>
<p>Tips from the Wose: Keep in mind that you need to update your preference file every now and then to include all the new players. It's easy to also add just the new ones: They're ordered in ascending joining order, so just use ctrl+f inside the textbox above to find the last name in your preference file, and copy &amp; paste all the ones after that, including the coma.</p>
<h2>Linux Automated Friends Update</h2>
<i>by mr-russ</i>
<p><img src="graphics/penguin.png" class="alignright">If you want to get the latest friendslist easier and automagically  you can download it in text form by requesting <?php echo $directory ?>/friends.php?download. This will return a text string with the <i>ladder</i> friends in it.</p>
<p>WARNING: The following comes without warranty and my delete your preferences file, but some have had success with the following shell script on linux. The script below will update your friends list with all of the ladder players. It will also remove any players on your friends list that are not ladder players. By using cron or anacron you can make the script run once or twice a week, meaning you'd always have fresh friendslist without having to do anything.</p>
<pre>
#!/bin/bash
TMPFILE=`mktemp /tmp/wesnoth.prefs.XXXXXXXXXX`
wget --quiet -O${TMPFILE} " <?php echo $directory ?>/friends.php?download"
grep -v '^friends=' ~/.wesnoth/preferences &gt; ~/.wesnoth/preferences2
cat $TMPFILE &gt;&gt; ~/.wesnoth/preferences2
mv ~/.wesnoth/preferences2 ~/.wesnoth/preferences
rm $TMPFILE
</pre>
<br />
<h2>Microsoft Windows Automated Friends Update</h2>
<i>by eyerouge</i>
<p><img src="graphics/winsmall.png" class="alignright"> Because of ideological and ethical reasons won't support this since I'm actually against the usage of MS Windows and it's offspring as well as any other non-free OS. From my perspective most users can and would do them self and the world at large a favour if they migrated from Windows to Linux instead. However, I do recognize that a majority never will for various reasons, hence I bring to you a friends updater for Windows until you come and visit us in Sherwood. As with russ, I leave no guarantee whatsoever that this will not cause drama.</p>

<ul>
<li><a href="wesfriends.rar">Download</a> and un-rar it.</li>
<li>To use it simply put the file wherever your Wesnoth preferences file is located. /userdata in your Wesnoth dir would be a good guess, at least in 1.4.3 it was. If you can't find it then bug somone but not us.</li>
<li>Whenever you run the updater it it will update your preferences file by replacing whatever friends you have in it with the latest friends fetched form the ladder. </li>
<li>To always have a fresh friends list you just need to make your MS Windows run the udpater every now and then. You could e.g. use the schedueler to accomplish that, and scheduele it to run twice a week or so. Whatever you do, don't run it more often than that.</li>
<li>I wrote and tested it on two XP machines but it will probably work on most flavours.</li>
<li>If anything goes wrong take a look at the two debug files generated in the same dir: LatestDownload and LatestParsed. You can also resort to the latest backup - preferences.old - that the program creates before modifying anything. LatestDownload will show you what was fetched from our site, and LatestParsed how it was all put together in the new preference-file.</li>
<li>If a bug should apperar then just delete your Wesnoth preference file. Enter Wesnoth, a new will be created. Add a dummy friend. Exit Wesnoth. Make sure the ladder site is online and working. Run the friends updater. If you still get the same error then there's a problem.</li>
</ul>


<?php
require('bottom.php');
?>
