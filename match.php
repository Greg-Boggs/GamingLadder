<?php

		// Let's check if it's a valid Wesnoth multiplayer name...

echo "test<br>";


$name = "ABsdd sdafKakmndsaik";

// check for spaces...

/*
if (strstr($name, ' ') != FALSE) {
	
	echo "The name <b>$name</b> isn't a valid Wesnoth multiplayer name since it has wicked characters.<br>Please choose another name without the space in it<br>";
	exit;
	}

*/


//if (!preg_match("/[^a-zA-Z0-9\-\_\]+$/s",$name)) {
	
	 // preg_match('/^[a-zA-Z0-9\+\-]$/i', $str) 
	 
	if (!preg_match("/^[a-zA-Z0-9\-\_]+$/i", $name)) {

	echo "The name <b>$name</b> isn't a valid Wesnoth multiplayer name since it has wicked characters.<br>Please choose another name without the bad characters.<br>The ones allowed are: Standard alfa and numerical, and also the "-" and "_" signs.";
	}
	
	else {
				echo "$name is okey"; 
	}
  ?>