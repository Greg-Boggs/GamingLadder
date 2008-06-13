<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "approve";
require('./../../conf/variables.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Approve.</p>
<?
$date = date("M d, Y");
if ($_POST[submit]) {
$sql = "UPDATE $playerstable SET approved = 'yes' WHERE name='$_POST[name]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='adduser.php'><font color='$color1'>Add another player</font>.</a></p>";
}else{
$sql="SELECT * FROM $playerstable WHERE approved='no' ORDER BY name ASC";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
if ($num > 0) {
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="name" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<?
$sql="SELECT * FROM $playerstable WHERE approved='no' ORDER BY name ASC";
$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
$name = $row["name"];
?>
<option><?echo "$row[name]" ?></option>
<?
}
?>
</select></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Approve." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}else{
echo"<p class='text'>There are no blocked or unapproved players.</p>";
}
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>

