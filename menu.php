<?php 

// Get to know if a player is logged in or not...

require('conf/variables.php');

if (isset($_COOKIE["LadderofWesnoth1"]) AND isset($_COOKIE["LadderofWesnoth2"])) {
		//DEB echo "2 Cookies are set..."; 
		
			
		$nameincookie = $_COOKIE["LadderofWesnoth1"];
		$passincookie =  $_COOKIE["LadderofWesnoth2"];
		
		// We hash it again to avoid getting the password broken by Rainbow tables...
		

	
		// Now lets compare the cookies with the database. If there is a playername and pass that corresponds, he's logged in...
		$sql = "SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb='$passincookie'";
		$result = mysql_query($sql,$db);
		$bajs = mysql_fetch_array($result); 
		
		//DEB echo "<div align='right'>Vnameincookie: $nameincookie</div>";
		//DEB echo "bajsplayerid: $bajs[name] $bajs[player_id]";
		
			if ($bajs[player_id] > 0) { 
				//echo "<div align='right'>//<a href=\"profile.php?name=$bajs[name]\">$nameincookie</a></div>";
				$loggedin = 1;
			} else { $loggedin = 0; }
		}
?>
<div id="nav">
  <ul>
	<?php if ( $loggedin == 0 ) { ?>
	<li><a href=join.php>Join</a></li>
	<?php } ?>
	<li><a href="report.php">Report</a></li>
	<li><a href="ladder.php">Ladder</a></li>
	<li><a href="players.php?startplayers=0&finishplayers=<?php echo $numplayerspage; ?>">Players</a>

	<li><a href="playedgames.php?startplayed=0&finishplayed=<?php echo $numgamespage; ?>">Game History</a></li>
	<?php if ( $loggedin == 1 ) { ?>
	<li><a href="playerdata.php">My Stats</a></li>
	<?php } ?>
	<li><a href="edit.php">My Profile</a></li>
	<li><a href="faq.php">FAQ</a></li>
	<li><a href="ip.php">Dupe Check</a></li>
  </ul>
</div>


<div class="menu">
<ul>
</ul>
</div>

<?php
/*

$sql="SELECT * FROM $pagestable ORDER BY page_id DESC";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$cur = 1;
while ($row = mysql_fetch_array($result)) {
$title = strtolower($row["title"]);

if ($_GET[number] =="$row[page_id]") {
echo"<a href=pages.php?number=$row[page_id]><img border=0 src=\"hfaq.gif\"></a>";
}else{
echo"<a href=pages.php?number=$row[page_id]><img border=0 src=\"faq.gif\"></a>";
}
}
*/

?>
