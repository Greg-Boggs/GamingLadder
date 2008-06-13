<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "deleteuser";
require('./../../conf/variables.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Delete user.</p>
<?
if ($_POST[submit]) {
$sql="SELECT * FROM $playerstable WHERE name = '$_POST[deletename]' ORDER BY name ASC";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
if ($row["rank"] > 0) {
$sql = "UPDATE $playerstable SET rank = rank - 1 WHERE rank > $row[rank]";
$result = mysql_query($sql);
}
$sql = "DELETE FROM $playerstable WHERE name='$_POST[deletename]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='deleteuser.php'><font color='$color1'>Delete another user</font></a>.</p>";
} else{
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="deletename" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
$sql="SELECT * FROM $playerstable ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
echo"<option>$row[name]</option>";
}
?>
</select></td>
</tr>
</table>
</center>
<p><input type="Submit" name="submit" value="Delete." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>
