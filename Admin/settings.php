<?PHP
session_start();
$page = "settings";
require('./../conf/variables.php');
require('./../top.php');

$sql="SELECT * FROM $admintable WHERE name = '$_SESSION[username]' AND password = '$_SESSION[password]'";
echo $sql;
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
if ($number == "1") {
?>
<p class="header">Settings.</p>
<?php
if ($submit) {
$sql = "UPDATE $varstable SET
color1 = '$_POST[color1]',
color2 = '$_POST[color2]',
color3 = '$_POST[color3]',
color4 = '$_POST[color4]',
color5 = '$_POST[color5]',
color6 = '$_POST[color6]',
color7 = '$_POST[color7]',
fontweight = '$_POST[fontweight]',
font = '$_POST[font]',
fontsize = '$_POST[fontsize]',
numgamespage = '$_POST[numgamespage]',
numplayerspage = '$_POST[numplayerspage]',
statsnum = '$_POST[statsnum]',
hotcoldnum = '$_POST[hotcoldnum]',
gamesmaxdayplayer = '$_POST[gamesmaxdayplayer]',
gamesmaxday = '$_POST[gamesmaxday]',
approve = '$_POST[approve]',
approvegames = '$_POST[approvegames]',
system = '$_POST[system]',
pointswin = '$_POST[pointswin]',
pointsloss = '$_POST[pointsloss]',
report = '$_POST[report]',
leaguename = '$_POST[leaguename]',
titlebar = '$_POST[titlebar]',
newsitems = '$_POST[newsitems]'
";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.</p>";
}else{
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text"><b>General league info</b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">League name:</p></td>
<td><input type="Text" name="leaguename" value="<?echo"$leaguename" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Title of pages:</p></td>
<td><input type="Text" name="titlebar" value="<?echo"$titlebar" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text"><br><b>Cosmetics</b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">Color 1 (text):</p></td>
<td><input type="Text" name="color1" value="<?echo"$color1" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 2 (header text, menu links):</p></td>
<td><input type="Text" name="color2" value="<?echo"$color2" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 3 (light bar):</p></td>
<td><input type="Text" name="color3" value="<?echo"$color3" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 4 (dark bar):</p></td>
<td><input type="Text" name="color4" value="<?echo"$color4" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 5 (table background, form fields):</p></td>
<td><input type="Text" name="color5" value="<?echo"$color5" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 6: (header background, menu background)</p></td>
<td><input type="Text" name="color6" value="<?echo"$color6" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Color 7: (page background)</p></td>
<td><input type="Text" name="color7" value="<?echo"$color7" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Font:</p></td>
<td><input type="Text" name="font" value="<?echo"$font" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<tr>
<td><p class="text">Font size (in points):</p></td>
<td><input type="Text" name="fontsize" value="<?echo"$fontsize" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Font weight:</p></td>
<td><select size="1" name="fontweight" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$fontweight" ?></option>
<option>normal</option>
<option>bold</option>
</select></td>
</tr>
<tr>
<td><p class="text"><br></p><p class="text"><b>League settings</b></p></td>
<td><p class="text">&nbsp;</p></td>
</tr>
<tr>
<td><p class="text">Scoring system to use:</p></td>
<td><select size="1" name="system" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$system" ?></option>
<option>points</option>
<option>elorating</option>
<option>ladder</option>
</select></td>
</tr>
<?php
if ($system == "points") {
?>
<tr>
<td><p class="text">Points for a win:</p></td>
<td><input type="Text" name="pointswin" value="<?echo"$pointswin" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Points for a loss:</p></td>
<td><input type="Text" name="pointsloss" value="<?echo"$pointsloss" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<?php
}
else {
?>
<input type="hidden" name="pointsloss" value="<?echo"$pointsloss" ?>">
<input type="hidden" name="pointswin" value="<?echo"$pointswin" ?>">
<?php
}
?>
<tr>
<td><p class="text">Who reports the game?</p></td>
<td><select size="1" name="report" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$report" ?></option>
<option>winner</option>
<option>loser</option>
</select></td>
</tr>
<tr>
<td><p class="text">Number of news items on news page:</p></td>
<td><input type="Text" name="newsitems" value="<?echo"$newsitems" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Numbers of players to display per page:</p></td>
<td><input type="Text" name="numplayerspage" value="<?echo"$numplayerspage" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Numbers of games to display per page:</p></td>
<td><input type="Text" name="numgamespage" value="<?echo"$numgamespage" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Number of players to display in stats:</p></td>
<td><input type="Text" name="statsnum" value="<?echo"$statsnum" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Approve players first when they signed up:</p></td>
<td><select size="1" name="approve" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$approve" ?></option>
<option>yes</option>
<option>no</option>
</select></td>
</tr>
<tr>
<td><p class="text">Approve games before they are recorded:</p></td>
<td><select size="1" name="approvegames" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?echo "$approvegames" ?></option>
<option>yes</option>
<option>no</option>
</select></td>
</tr>
<tr>
<td><p class="text">Games to win/lose in a row to get hot/cold:</p></td>
<td><input type="Text" name="hotcoldnum" value="<?echo"$hotcoldnum" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Max. number of games a day:</p></td>
<td><input type="Text" name="gamesmaxday" value="<?echo"$gamesmaxday" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Max. number of games a day against same player :</p></td>
<td><input type="Text" name="gamesmaxdayplayer" value="<?echo"$gamesmaxdayplayer" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Enter information" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}else {
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../bottom.php');
?>
