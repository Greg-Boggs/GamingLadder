<?php
session_start();
$page = "Reset password";
require('conf/variables.php');
require('top.php');
include 'include/avatars.inc.php';

echo '<p class="header">Reset Password</p>';
echo '<p class="text">';

if (!isset($_POST['passkey']) {
    echo "<p>No confirmation key was supplied, please check the link is complete and includes the confirmation key.</p>";
	require('bottom.php');
    exit;
} else {
	$passkey = trim(strip_tags($_POST['passkey']));
	
	// Passkey that got from the link the user got in the verification mail
	// Retrieve data from table where row that match this passkey
	$sql = "SELECT player_id, passcode FROM $resettable WHERE passcode ='$passkey'";
	$result = mysql_query($sql, $db);

	if($result) {
	    // Change the users password
   		$passworddb = uniqid(rand());
		echo "<p>Your new password is $pasworddb</p>";

		// Lets generate the hashed pass...
		$passworddb = $salt.$passworddb;
		$passworddb = md5($passworddb); 
		$passworddb = md5($passworddb);
		
		$sql = "UPDATE $playerstable SET passworddb=$passworddb WHERE player_id = $row['player_id']";
    	$result = mysql_query($sql, $db);
	}
}
echo '</p>'
require('bottom.php');

