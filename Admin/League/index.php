<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "league";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../top.php');

if ( isset($_SESSION['username']) ) {
?>
<p class="header">League management.</p>
<p class="text">This is where you can add and delete players, change their info, report games and reset the season.
</p>
<?
}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}
require('./../../bottom.php');
?>

