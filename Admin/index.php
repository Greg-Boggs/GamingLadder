<?
session_start();
if (isset($_GET['logout'])) {
    $_SESSION = array();
}
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require('./../top.php');
?>
<p class="header">Admin section.</p>
<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    $sql = "SELECT name FROM $admintable WHERE name = '$_POST[username]' AND password = '$_POST[password]'";
    $result = mysql_query($sql,$db);
    $number = mysql_num_rows($result);
    if ($number == 1) {
        $_SESSION['username'] = $_POST['username'];
    }
}

if(isset($_SESSION['username']) ) {
?>
<p class='text'>You are logged in as <b><?php echo $_SESSION['username'] ?></b>.</p>
<?php
} else {
    if (isset($_POST['submit']) && $_POST['submit'] == "Log in.") {
?>
<p class='text'>Login failed.</p>
<?php
    }
?>
<form method="post" action="index.php">
<table border="0" cellpadding="0">
<tr>
<td><p class='text'>Name:</p></td>
<td><input type="text" name="username" size="20" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class='text'>Password:</p></td>
<td><input type="password" name="password" size="20" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><input type="submit" value="Log in." name="submit" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
</table>
</form>
<?php
} // Display login form if failed, or not logged in.
?>
<?php
require ('./maintenance.php');
require('./../bottom.php');
?>
