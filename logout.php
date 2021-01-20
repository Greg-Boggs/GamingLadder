<?php
session_start();
require('conf/variables.php');

$sql = "SELECT p.name, p.passworddb FROM $playerstable p WHERE p.name = '%s'";
$result = mysqli_query($db, sprintf($sql, $_SESSION['username']));
$bajs = mysqli_fetch_array($result);
$time = time();

session_destroy();
// v 1.01
// Set cookies so that they expire.
setcookie("LadderofWesnoth1", $bajs['name'], $time - 8776000);
setcookie("LadderofWesnoth2", $bajs['passworddb'], $time - 8776000);
require('top.php');
?>
    <h1>Farewell Hero....</h1><br>
    <p>You're now logged out. Please <a href="index.php">come back</a> any time - your skills improve, and so does the
        ladder.</p>
<?php
require('bottom.php');

