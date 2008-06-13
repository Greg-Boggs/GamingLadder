<?
session_start();
echo "session: ". $_SESSION['username'];
$page = "ip";
require('./../../conf/variables.php');
require('./../../top.php');

//if ( isset($_SESSION['username']) ) {
?>
<p class="header">Ip-check.</p>
<?
$count = 0;
$sql="SELECT * FROM $playerstable ORDER BY ip ASC, name ASC";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$cur = 1;
while ($num >= $cur) {
$row = mysql_fetch_array($result);
$name = $row["name"];
$ip2 = $row["ip"];
if ($ip2 == $ip) {
$show = 'yes';
$count++;
}
else {
$show = 'no';
}
$ip = $ip2;

if ($ip != "") {
if ($show == 'yes') {
if ($ip != $ip3) {
echo "<p class='text'><b>Same ip</b>:<br>";
$sql2="SELECT * FROM $playerstable WHERE ip = '$ip'";
$result2=mysql_query($sql2,$db);
$num2 = mysql_num_rows($result2);
$cur2 = 1;
while ($num2 >= $cur2) {
$row2 = mysql_fetch_array($result2);
$name2 = $row2["name"];
echo "$name2<br>";
$cur2++;
}
$ip3 = $ip;
echo"<br>";
}
}
}
$cur++;
}
if ($count < 1) {
?>
<p class="text">There are no players with identical ip's.</p>
<?
}
/*}else{
echo "<p class='header'>You are not allowed to view this part of the site.<br><br>
<p class='text'><a href='../index.php'><font color='$color1'>Login.</font></a></p>";
}*/
require('./../../bottom.php');
?>
