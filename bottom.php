<p align="center" class="copyleft">the ladder is not in any way associated with the official wesnoth forum, it's moderators and/or the developers of Wesnoth.
<br /><a href="<?php echo $GLOBALS['prefix'] ?>ip.php">dupe check</a> / <a href="<?php echo $GLOBALS['prefix'] ?>rss.php">rss</a> / power <a href="https://sourceforge.net/projects/gamingladder">tribal bliss</a> / <a href="https://sourceforge.net/pm/?group_id=224204">to-do</a><br />contact: spam (x) eyerouge (y) com</p>

<?php
// Admins can impersonate other users on the ladder, it's important to display the fact they are behaving like another user at
// the time.
if (isset($_SESSION['real-username'])) {
    if ($_SESSION['real-username'] != $_SESSION['username']) {
        echo "<p align='center'>Administrator <b>".$_SESSION['real-username']."</b> is impersonating <b>".$_SESSION['username']."</b></p>";
    }
}

$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime, 5);
echo "<p align=\"center\" class=\"copyleft\">$totaltime sec.</p>";
?>
</div>
</body>
</html>
