<?php 
require_once('conf/variables.php');

// Lets check to see if there are Ladder cookies to see if the user is logged in.


// The real-username will be set if we are logged in for this session
if (!isset($_SESSION['real-username'])) {
    // Attempt to login using the cookies if they are present
	if (isset($_COOKIE["LadderofWesnoth1"]) AND isset($_COOKIE["LadderofWesnoth2"])) {
		$nameincookie = $_COOKIE["LadderofWesnoth1"];
		$passincookie =  $_COOKIE["LadderofWesnoth2"];
		
		// Now lets compare the cookies with the database. If there is a playername and pass that corresponds, he's logged in...
		$sql = "SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb='$passincookie'";
		$result = mysql_query($sql,$db);
		$bajs = mysql_fetch_array($result); 
		
		if ($bajs[player_id] > 0) { 
            $_SESSION['username'] = $bajs['name'];
            $_SESSION['real-username'] = $bajs['name'];
		}
    }
}
?>
