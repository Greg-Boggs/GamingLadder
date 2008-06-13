</td>
</tr>
</table>
</div>
</td>
</tr>
<tr>


<td height="1" align="center">
&nbsp;
&nbsp;
&nbsp;
<p align="center" class="copyleft">the ladder is not in any way associated with the official wesnoth forum, it's moderators and/or the developers of Wesnoth.<br><a href="rss.php">rss</a> / power <a href="https://sourceforge.net/projects/gamingladder">tribal bliss</a> / <a href="https://sourceforge.net/pm/?group_id=224204">to-do</a><br>contact: eyerouge(x)eyerouge(y)com</p>

</td>
</tr>
<!-- <tr>
<td width="100%" height="5" bgcolor="<?php echo"$color4" ?>"></td>
</tr>
-->
</table>
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
echo "<br>Loaded in $totaltime seconds.";
?>
</body>
</html>
</body>
</html>

</body>
</html>
/html>
