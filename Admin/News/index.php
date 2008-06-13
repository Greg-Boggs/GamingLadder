<?PHP
session_start();
$page = "news";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {
?>
<p class="header">News management.</p>
<p class="text">This is where you can post, view, edit and delete news.</p>
<?
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>

