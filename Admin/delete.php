<?PHP
session_start();
$page = "view";
require('./../conf/variables.php');
require('./../top.php');
?>
<?php
$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {

if ($_POST[submit]) {
$sql = "DELETE FROM $pagestable WHERE page_id = '$_POST[edit]'";
$result = mysql_query($sql);
echo "<p class='header'>Page deleted.</p>";
}
else {
?>
<p class="header">Delete news.</p>
<form method="post">
<table border="0" cellpadding="0" width="100%">
<tr>
<td><p class="text">Delete page?</p></td>
</tr>
</table>
<p class="text">
<input type='hidden' name='edit' value="<?echo "$_GET[edit]" ?>">
<input type="Submit" name="submit" value="Delete." class="text"><br>
</form>
<?php
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='index.php'>Login.</a></p>";
}
require('./../bottom.php');
?>
