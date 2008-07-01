<?
session_start();
if (isset($_GET['logout'])) {
    unset($_SESSION['admin-login']);
}
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require('./../top.php');
?>
<p class="header">Admin section.</p>
<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    $passworddb = $salt.$_POST['password'];
    $passworddb = md5($passworddb);
    $passworddb = md5($passworddb);

	$sql = "SELECT name FROM $playerstable WHERE name='$_POST[username]' AND passworddb='$passworddb' AND is_admin = 1";
    $result = mysql_query($sql,$db);
    $number = mysql_num_rows($result);
    if ($number == 1) {
        $_SESSION['real-username'] = $_POST['username'];
		$_SESSION['admin-login'] = true;
    }
}

if(isset($_SESSION['username']) && isset($_SESSION['admin-login'])) {
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
require('./../bottom.php');
?>
