<?
session_start();
require('./../conf/variables.php');
require('./../top.php');
include ('./../include/smileys.inc.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {
?>
<p class="header">Pages.</p>
<?
if ($read) {
$sql="SELECT * FROM $pagestable WHERE page_id = '$read'ORDER BY page_id DESC";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
$content = nl2br($row["page"]);
$content = addSmileys($content);
?>
<p class="text"><b><?echo"$row[title]" ?></b></p>
<p class="text"><?echo"$content" ?></p>
<hr size="1" color="<?echo"$color1" ?>"><br>
<?
}
?>
<table border="1" cellspacing="1" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../icons/view.gif' width='18' height='18' align='middle'></td>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../icons/edit.gif' width='18' height='18' align='middle'></td>
<td align='center' bordercolor='<?echo"$color7" ?>'><img border='1' src='../icons/delete.gif' width='18' height='18' align='middle'></td>
<td align='left' bordercolor='<?echo"$color7" ?>'><p class='text'><b>Page<b></p></td></tr>
<?
$sql="SELECT * FROM $pagestable ORDER BY page_id DESC";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$cur = 1;
while ($row = mysql_fetch_array($result)) {
echo"
<tr>
<td align='center' bordercolor='$color7'><a href='view.php?read=$row[page_id]'><font color='$color1'>View.</a></td>
<td align='center' bordercolor='$color7'><a href='edit.php?edit=$row[page_id]'><font color='$color1'>Edit.</a></td>
<td align='center' bordercolor='$color7'><a href='delete.php?edit=$row[page_id]'><font color='$color1'>Delete.</a></td>
<td align='left' bordercolor='$color7'><p class='text'>$row[title]</td></tr>
";
}
?>
</table>
<br>
<?
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../bottom.php');
?>
