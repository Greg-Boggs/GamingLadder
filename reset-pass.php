<?php
session_start();
$page = "Reset password";
require('conf/variables.php');
require('top.php');
include 'include/avatars.inc.php';
include 'include/genericfunctions.inc.php';

echo '<p class="header">Reset Password</p>';
echo '<p class="text">';

if (!isset($_GET['passkey'])) {
    echo "<p>No confirmation key was supplied, please check the link is complete and includes the confirmation key.</p>";
    require('bottom.php');
    exit;
} else {
    $passkey = trim(strip_tags($_GET['passkey']));

    // Passkey that got from the link the user got in the verification mail
    // Retrieve data from table where row that match this passkey
    $sql = "SELECT player_id, passcode FROM $resettable WHERE passcode like '$passkey'";
    $result = mysqli_query($db, $sql);
    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 0) {
        echo "<p>Bad confirmation key was supplied, please check the link is complete and includes the confirmation key.</p>";
        require('bottom.php');
        exit;
    } else {

        // Change the users password
        $row = mysqli_fetch_assoc($result);
        $passworddb = uniqid(rand());
        echo "<p>Your new password is $passworddb</p>";
        echo '<p>Go <a href="/">home and use it</a>!</p>';

        // Lets generate the hashed pass...
        $passworddb = $salt . $passworddb;
        $passworddb = md5($passworddb);
        $passworddb = md5($passworddb);

        $sql = "UPDATE $playerstable SET passworddb='$passworddb' WHERE player_id = $row[player_id]";
        $result = mysqli_query($db, $sql);

        $sql = "DELETE FROM $resettable WHERE passcode like '$passkey'";
        $result = mysqli_query($db, $sql);
    }
}
echo '</p>';
require('bottom.php');

