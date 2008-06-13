<?
if ($_POST[submit]) {
?>
Creating tables...<br><br>
<?
include "variables.php";
$db = mysql_connect($databaseserver, $databaseuser, $databasepass);
mysql_select_db($databasename,$db);
if ($db==false) die("Failed to connect to MySQL server<br>\n");

$sql = "CREATE TABLE $playerstable (player_id int(10) NOT NULL auto_increment, name varchar(255) NOT NULL, passworddb varchar(255), approved  varchar(10) DEFAULT 'no', mail varchar(50), icq varchar(15), aim varchar(40), msn varchar (100), country varchar(40), rating int(10) DEFAULT '1500', games int(10) DEFAULT '0', wins int(10) DEFAULT '0', losses int(10) DEFAULT '0', points int(10) DEFAULT '0', totalwins int(10) DEFAULT '0', totallosses int(10) DEFAULT '0', totalpoints int(10) DEFAULT '0', totalgames int(10) DEFAULT '0', rank int(10) DEFAULT '0', streakwins int(10) DEFAULT '0', streaklosses int(10) DEFAULT '0', ip varchar(100), PRIMARY KEY (player_id))";
mysql_query($sql,$db);
$sql = "ALTER TABLE $playerstable ADD UNIQUE(name) ";
mysql_query($sql,$db);
echo"Players table<br>";

$sql = "CREATE TABLE $gamestable (game_id int(10) NOT NULL auto_increment, winner varchar(40), loser varchar(40), date varchar(40), recorded varchar(10), PRIMARY KEY (game_id))";
mysql_query($sql,$db);
echo"Games table<br>";

$sql = "CREATE TABLE $admintable (id int(10) NOT NULL auto_increment, name varchar(40), password varchar(40), PRIMARY KEY (id))";
mysql_query($sql,$db);
echo"Admin table<br>";

$sql = "CREATE TABLE $newstable (news_id int(10) NOT NULL auto_increment, title varchar (100), date varchar (100), news text, PRIMARY KEY (news_id))";
mysql_query($sql,$db);
echo"News table<br>";

$sql = "CREATE TABLE $pagestable (page_id int(10) NOT NULL auto_increment, title varchar (100), page text, PRIMARY KEY (page_id))";
mysql_query($sql,$db);
echo"Pages table<br>";

$sql = "CREATE TABLE $varstable (vars_id int(10) NOT NULL auto_increment, color1 varchar(20), color2 varchar (20), color3 varchar (20), color4 varchar (20), color5 varchar (20), color6 varchar(20), color7 varchar(20), font varchar(80), fontweight varchar(40), fontsize varchar(20), numgamespage int(10), numplayerspage int(10),  statsnum int(10), standingsnogames varchar(10), hotcoldnum varchar(10), gamesmaxdayplayer int(10), gamesmaxday int(10), approve varchar(10), approvegames varchar(10), system varchar (20), pointswin int(10), pointsloss int(10), report varchar (20), leaguename varchar (100), titlebar varchar (100), newsitems int(10), copyright varchar(200), PRIMARY KEY (vars_id))";
mysql_query($sql,$db);
echo"Vars table<br><br>";

$date = date("M d, Y.");
echo"Inserting default values<br>";
$sql = "INSERT INTO $newstable (news, title, date) VALUES ('Congratulations, you have successfully installed WebLeague.<br><br>[Be happy here.]<br><br>Enjoy. :)', 'Glory!', '$date')";
mysql_query($sql,$db);
echo"Inserting news<br>";

$sql = "INSERT INTO $varstable (color1, color2, color3, color4, color5, color6, color7, font, fontweight, fontsize,  numgamespage, numplayerspage, statsnum,  hotcoldnum, gamesmaxdayplayer, gamesmaxday, approve, approvegames, system, pointswin, pointsloss, report, leaguename, titlebar, newsitems, copyright) VALUES ('#000000', '#FFFFFF', '#66CC66', '#339933', '#EEEEEE', '#000000', '#FFFFFF', 'Tahoma', 'normal', '12', '20', '30', '10', '5', '2', '10', 'no', 'no', 'elorating', '2', '-1', 'winner', 'Web<i>League</i>', 'WebLeague', '3', 'powered by: <a href=\"http://www.worms-league.com/WebLeague\">WebLeague</a>')";
mysql_query($sql,$db);
echo"Inserting vars<br><br>";

$sql = "INSERT INTO $admintable (name, password) VALUES ('$_POST[name]','$_POST[password]')";
$result = mysql_query($sql);
echo"Creating admin account.<br><br>";
echo"Done.";
} else{
?>
<form method="post">
<p align="center"><b>WebLeague installation.</b><br><br>
Create an admin account:
<div align="center">
<center>
<table border="0" cellpadding="0">
<tr>
<td>Nickname:</td>
<td><input type="Text" name="name"></td>
</tr>
<tr>
<td>Password:</td>
<td><input type="password" name="password"></td>
</tr>
</table>
</center>
</div>
<p align="center">
<input type="Submit" name="submit" value="Submit.">
</form>
<?
}
?>