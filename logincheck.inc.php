<?
// v 1.01 Ladder Login Check 
require('conf/variables.php');
?>
<p class="text">
<?php 

// Lets check to see if there are Ladder cookies to see if the user is logged in.


// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.
//$loggedin = 1;
	if (isset($_COOKIE["LadderofWesnoth1"]) AND isset($_COOKIE["LadderofWesnoth2"])) {
		//DEB echo "2 Cookies are set..."; 
		
			
		$nameincookie = $_COOKIE["LadderofWesnoth1"];
		$passincookie =  $_COOKIE["LadderofWesnoth2"];
		
	
		// Now lets compare the cookies with the database. If there is a playername and pass that corresponds, he's logged in...
		$sql = "SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb='$passincookie'";
		$result = mysql_query($sql,$db);
		$bajs = mysql_fetch_array($result); 
		
		//DEB echo "<div align='right'>Vnameincookie: $nameincookie</div>";
		//DEB echo "bajsplayerid: $bajs[name] $bajs[player_id]";
		
			if ($bajs[player_id] > 0) { 
				$loggedin = 1;
			} else { $loggedin = 0; }
		}

require('top.php');
?>
