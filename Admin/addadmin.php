<?php
session_start();
$GLOBALS['prefix'] = "../";
require('../conf/variables.php');
require_once 'security.inc.php';
require('../top.php');
?>
<p class="header">Add Admin User</p>
<?php
if (isset($_POST['submit']) && $_POST['submit'] == "Add Admin") {
    $sql = "UPDATE $playerstable SET is_admin = true WHERE name = '$_POST[name]'";
    $result = mysql_query($sql,$db);

    if (mysql_affected_rows() == 1) {
        echo "<p class='text'>Thank you! Information entered.</p><p><a href='addadmin.php'>Add another admin</a></p>";
    } else {
        echo "<p class='text'>The name you entered is already an admin.</p>";
    }
} else {
?>
<p>Administrators must already be members of the ladder.  It's not nessecary for them to participate in the ladder, but they must have an account.</p>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><input type="Text" name="name" class="text"></td>
</tr>
</table>
<p align="left">
<input type="Submit" name="submit" value="Add Admin" class="text"><br><br>
</form>
</p>
<p>This these are the current ladder administrators</p>
<ul>
<?php
// Display a list of all the current admin users
$sql = "SELECT name from $playerstable WHERE is_admin = true";
$result = mysql_query($sql, $db);

while ($row = mysql_fetch_array($result)) {
    echo "<li>".$row['name']."</li>";
}
echo "</ul>";

}
require('./../bottom.php');
?>
