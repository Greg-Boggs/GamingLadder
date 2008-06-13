<?PHP
session_start();
$page = "resetseason";
echo "session: ". $_SESSION['username'];
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">Reset season.</p>
<?
if ($_POST[submit]) {
$sql = "UPDATE $playerstable SET wins = 0, losses = 0 , points = 0,  games = 0, streakwins = 0, streaklosses = 0, rank = 0, rating = 1500";
$result = mysql_query($sql);
$sql = "DELETE FROM $gamestable WHERE game_id > 0";
$result = mysql_query($sql);
echo "<p class='text'>Thank you! Information entered.</p>";
} else{
?>
<form method="post">
<p>  <input type="Submit" name="submit" value="Reset." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>