<?
// v 1.01

$page = "logout";
require('conf/variables.php');

// Set cookies so that they expire.
			
		 setcookie ("LadderofWesnoth1", $bajs[name], $time-8776000); 
		  setcookie ("LadderofWesnoth2", $bajs[passworddb], $time-8776000); 
		  
		//  old broke setcookie ("LadderofWesnoth1", $bajs[name], $time-8776000, "/wesnoth/", "chaosrealm.net"); 
		 //  oldbroke setcookie ("LadderofWesnoth2", $bajs[passworddb], $time-8776000, "/wesnoth/", "chaosrealm.net"); 
		require('top.php');  
?>

<h1>Farewell Hero....</h1><br>
<p>You're now logged out. Please <a href="index.php">come back</a> any time - your skills improve, and so does the ladder.</p>
<?php
require('bottom.php');
?>
