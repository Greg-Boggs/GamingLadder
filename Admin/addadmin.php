<?php
session_start();
$GLOBALS['prefix'] = "../";
require('../conf/variables.php');
require_once 'security.inc.php';
require('../top.php');
?>
<p class="header">Add Admin User</p>
<?php
if ($_POST['submit']) {
    $sql = "SELECT * FROM $admintable WHERE name = '$_POST[name]'";
    $result = mysql_query($sql,$db);
    $samenick = mysql_num_rows($result);

    if ($samenick < 1) {
        $sql = "INSERT INTO $admintable (name, password) VALUES ('$_POST[name]','$_POST[password]')";
        $result = mysql_query($sql);
        echo "<p class='text'>Thank you! Information entered.</p><p><a href='addadmin.php'>Add another admin</a></p>";
    } else {
        echo "<p class='text'>The name you entered already exist.</p>";
    }
} else {
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><input type="Text" name="name" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Password:</p></td>
<td><input type="password" name="password" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
</table>
<p align="left">
<input type="Submit" name="submit" value="Submit." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br><br>
</form>
</p>
<?php
}
require('./../bottom.php');
?>

