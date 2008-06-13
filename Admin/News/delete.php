<?
session_start();
$page = "view";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {

if ($_POST[submit]) {
$sql = "DELETE FROM $newstable WHERE news_id = '$_POST[edit]'";
$result = mysql_query($sql);
echo "<p class='header'>News deleted.</p>";
}else{
?>
<p class="header">Delete news.</p>
<form method="post">
<table border="0" cellpadding="0" width="100%">
<tr>
<td><p class="text">Delete news?</p></td>
</tr>
</table>
<p class="text">
<input type='hidden' name='edit' value="<?echo "$_GET[edit]" ?>">
<input type="Submit" name="submit" value="Delete." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>