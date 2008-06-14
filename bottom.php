<p align="center" class="copyleft">the ladder is not in any way associated with the official wesnoth forum, it's moderators and/or the developers of Wesnoth.<br><a href="rss.php">rss</a> / power <a href="https://sourceforge.net/projects/gamingladder">tribal bliss</a> / <a href="https://sourceforge.net/pm/?group_id=224204">to-do</a><br>contact: spam (x) eyerouge (y) com</p>

</div>
<?php 
if (($db) ) {
	mysql_close($db);
}
$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime, 5);
echo "<p align=\"center\" class=\"copyleft\">$totaltime sec.</div>";
?>
</body>
</html>

