<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "edituser";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Edit user.</p>
<?
if (!$_POST[edituser]){
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="edituser" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
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
<p class="text"><input type="Submit" name="submit2" value="Edit." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br><br>
</form>
</p>
<?
}else{
if ($_POST[submit]) {
if ($system == "ladder") {
if ($_POST[rank] < $_POST[rankold]) {
$sql = "UPDATE $playerstable SET rank = rank + 1 WHERE rank > $_POST[rank] - 1 AND rank < $_POST[rankold]";
$result = mysql_query($sql);
}
if ($_POST[rank] > $_POST[rankold]) {
$sql = "UPDATE $playerstable SET rank = rank - 1 WHERE rank < $_POST[rank] + 1 AND rank > $_POST[rankold]";
$result = mysql_query($sql);
}
}
$sql = "UPDATE $playerstable SET approved = '$_POST[blocked]', wins = '$_POST[wins]', mail = '$_POST[mail]', icq = '$_POST[icq]', aim = '$_POST[aim]', msn = '$_POST[msn]', country = '$_POST[country]', losses = '$_POST[losses]', totalwins = '$_POST[totalwins]', totallosses = '$_POST[totallosses]', points = '$_POST[points]', totalpoints = '$_POST[totalpoints]', games = '$_POST[games]', totalgames = '$_POST[totalgames]', streakwins = '$_POST[streakwins]', streaklosses = '$_POST[streaklosses]', rating = '$_POST[rating]', rank = '$_POST[rank]'  WHERE name='$_POST[edituser]'";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.<br><br><a href='edituser.php'><font color='$color1'>Edit another user</font></a>.</p>";
} else{
$sql="SELECT * FROM $playerstable WHERE name = '$_POST[edituser]' ORDER BY name ASC";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text"><b>General</b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="edituser" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option><?echo "$edituser" ?></option>
</select>
</td>
</tr>
<tr>
<td><p class="text">Approved:</p></td>
<td><select size="1" name="blocked" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">>
<option selected><?echo "$row[approved]" ?></option>
<option>no</option>
<option>yes</option>
</select>
</td>
</tr>
<tr>
<td><p class="text">Mail:</p></td>
<td><input type="Text" name="mail" value="<?echo "$row[mail]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Icq:</p></td>
<td><input type="Text" name="icq" value="<?echo "$row[icq]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Aim:</p></td>
<td><input type="Text" name="aim" value="<?echo "$row[aim]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Msn:</p></td>
<td><input type="Text" name="msn" value="<?echo "$row[msn]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Country:</p></td>
<td><select size="1" name="country" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$row[country]" ?></option>
<option>No country</option>
<option>Argentina</option>
<option>Australia</option>
<option>Austria</option>
<option>Belgium</option>
<option>Bosnia</option>
<option>Brazil</option>
<option>Bulgaria</option>
<option>Canada</option>
<option>Chile</option>
<option>Croatia</option>
<option>Cyprus</option>
<option>Czechoslavakia</option>
<option>Denmark</option>
<option>England</option>
<option>Finland</option>
<option>France</option>
<option>Georgia</option>
<option>Germany</option>
<option>Greece</option>
<option>Holland</option>
<option>Hong Kong</option>
<option>Hungary</option>
<option>Iceland</option>
<option>India</option>
<option>Indonesia</option>
<option>Iran</option>
<option>Iraq</option>
<option>Ireland</option>
<option>Israel</option>
<option>Italy</option>
<option>Japan</option>
<option>Leichenstein</option>
<option>Luxembourg</option>
<option>Malaysia</option>
<option>Malta</option>
<option>Mexico</option>
<option>Morocco</option>
<option>New Zealand</option>
<option>North Vietnam</option>
<option>Norway</option>
<option>Poland</option>
<option>Portugal</option>
<option>Puerto Rico</option>
<option>Qatar</option>
<option>Rumania</option>
<option>Russia</option>
<option>Scotland</option>
<option>Singapore</option>
<option>South Africa</option>
<option>Spain</option>
<option>Sweden</option>
<option>Switzerland</option>
<option>Turkey</option>
<option>United Kingdom</option>
<option>United States</option>
</select></td>
</tr>
<tr>
<td><br><p class="text"><b>Season<b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">Games:</p></td>
<td><input type="Text" name="games" value="<?echo "$row[games]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Wins:</p></td>
<td><input type="Text" name="wins" value="<?echo "$row[wins]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Losses:</p></td>
<td><input type="Text" name="losses" value="<?echo "$row[losses]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?
if ($system == "points") {
?>
<tr>
<td><p class="text">Points (season):</p></td>
<td><input type="Text" name="points" value="<?echo "$row[points]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?
}
else {
?>
<input type="hidden" name="points" value="<?echo "$row[points]" ?>">
<?
}
?>
<tr>
<td><br><p class="text"><b>Total</b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">Games (total):</p></td>
<td><input type="Text" name="totalgames" value="<?echo "$row[totalgames]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Wins (total):</p></td>
<td><input type="Text" name="totalwins" value="<?echo "$row[totalwins]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Losses (total):</p></td>
<td><input type="Text" name="totallosses" value="<?echo "$row[totallosses]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?
if ($system == "points") {
?>
<tr>
<td><p class="text">Points (total):</p></td>
<td><input type="Text" name="totalpoints" value="<?echo "$row[totalpoints]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?
}else{
?>
<input type="hidden" name="totalpoints" value="<?echo "$row[totalpoints]" ?>">
<?
}
?>
<tr>
<td><p class="text">&nbsp;</p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<?
if ($system == "ladder") {
?>
<tr>
<td><p class="text">Rank:</p></td>
<td><input type="Text" name="rank" value="<?echo "$row[rank]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?
}else{
?>
<input type="hidden" name="rank" value="<?echo "$row[rank]" ?>">
<?
}
?>
<input type="hidden" name="rankold" value="<?echo "$row[rank]" ?>">
<tr>
<td><p class="text">Rating:</p></td>
<td><input type="Text" name="rating" value="<?echo "$row[rating]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Streak (wins):</p></td>
<td><input type="Text" name="streakwins" value="<?echo "$row[streakwins]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Streak (losses):</p></td>
<td><input type="Text" name="streaklosses" value="<?echo "$row[streaklosses]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Edit user." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>