<p align="center" class="copyleft">
<?php 
if ($cronbottommsg != "" && $cronbottommsg != " "){
echo "<b>$cronbottommsg</b><br>"; 
}
?>the ladder is not in any way associated with the official wesnoth forum, it's moderators and/or the developers of Wesnoth.
<br /><a href="<?php echo $GLOBALS['prefix'] ?>ip.php">dupe check</a> / <a href="<?php echo $GLOBALS['prefix'] ?>rss.php">rss</a> / powered by <a href="https://sourceforge.net/projects/gamingladder/?abmode=1">tribal bliss</a> <?php echo TRIBAL_VERSION ?> / <a href="https://sourceforge.net/pm/?group_id=224204">to-do</a>
<?php echo "<br />contact: ". FOOTER_MAIL ?>

<?php
$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime, 5);
echo "<p align=\"center\" class=\"copyleft\">". date('Y-m-d H:i') ." $cfg_ladder_timezone | ". $totaltime ."sec. </p>";
?>
</div>
<br /><br />
</body>
</html>
