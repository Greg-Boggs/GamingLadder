<?
session_start();
$page = "login";
require('./../conf/variables.php');
require('./../top.php');
?>
<p class="header">Admin section.</p>
<?
$sql="SELECT * FROM $admintable WHERE name = '$_POST[username]' AND password = '$_POST[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
echo $number;
if ($number == "1") {
	$_SESSION['username'] = 1;
}
if(isset($_SESSION['username']) ) {
?>
<p class='text'>You are logged in as <b><?echo "$_SESSION[username]" ?></b>.</p>
<?
}
else {
if($_POST[submit]) {
?>
<p class='text'>Login failed.</p>
<?
}
?>
<form method="post">
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
<?
}
?>
<?
require ('./maintenance.php');
require('./../bottom.php');
?>
