<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "blockuser";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Block player.</p>
<?
if ($_POST[submit]) {
$sql = "UPDATE $playerstable SET approved = 'no' WHERE name='$_POST[name]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='blockuser.php'><font color='$color1'>Block another user</font>.</a></p>";
}else{
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="name" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
$sql="SELECT * FROM $playerstable WHERE approved='yes' ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"<option>$row[name]</option>";
}
?>
</select></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Block." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?php
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>