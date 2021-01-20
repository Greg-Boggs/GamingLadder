<?php
if ($_POST['submit']) {
?>
Creating tables...<br><br>
<?php
require_once '../../conf/variables.php';

$db = mysqli_connect($databaseserver, $databaseuser, $databasepass);
mysqli_select_db($databasename,$db);

if ($db==false) die("Failed to connect to MySQL server<br>\n");

$sql = "CREATE TABLE $playerstable (player_id int(10) NOT NULL auto_increment, name varchar(255) NOT NULL, passworddb varchar(255), approved  varchar(10) DEFAULT 'no', mail varchar(50), icq varchar(15), aim varchar(40), msn varchar (100), country varchar(40), rating int(10) DEFAULT '1500', games int(10) DEFAULT '0', wins int(10) DEFAULT '0', losses int(10) DEFAULT '0', points int(10) DEFAULT '0', totalwins int(10) DEFAULT '0', totallosses int(10) DEFAULT '0', totalpoints int(10) DEFAULT '0', totalgames int(10) DEFAULT '0', rank int(10) DEFAULT '0', streakwins int(10) DEFAULT '0', streaklosses int(10) DEFAULT '0', ip varchar(100), PRIMARY KEY (player_id))";
mysqli_query($db, $sql);
$sql = "ALTER TABLE $playerstable ADD UNIQUE(name) ";
mysqli_query($db, $sql);
echo"Players table<br>";

$sql = "CREATE TABLE $gamestable (game_id int(10) NOT NULL auto_increment, winner varchar(40), loser varchar(40), date varchar(40), recorded varchar(10), PRIMARY KEY (game_id))";
mysqli_query($db, $sql);
echo"Games table<br>";

$sql = "CREATE TABLE $admintable (id int(10) NOT NULL auto_increment, name varchar(40), password varchar(40), PRIMARY KEY (id))";
mysqli_query($db, $sql);
echo"Admin table<br>";

$sql = "CREATE TABLE $newstable (news_id int(10) NOT NULL auto_increment, title varchar (100), date varchar (100), news text, PRIMARY KEY (news_id))";
mysqli_query($db, $sql);
echo"News table<br>";

$date = date("M d, Y.");
echo"Inserting default values<br>";
$sql = "INSERT INTO $newstable (news, title, date) VALUES ('Congratulations, you have successfully installed Competitive Gaming Ladder.<br><br>[Be happy here.]<br><br>Enjoy. :)', 'Glory!', '$date')";
mysqli_query($db, $sql);
echo"Inserting news<br>";

$sql = "INSERT INTO $admintable (name, password) VALUES ('".mysqli_escape_string($_POST['name'])."','".mysqli_escape_string($_POST['password'])."')";
$result = mysqli_query($db, $sql);
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
<?php
}
?>
