<?php
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');

?>
<p class="header">Manage Blockng Players</p>
<?php
if ($_POST[submit]) {
    $sql = "UPDATE $playerstable SET approved = 'no' WHERE name='$_POST[name]'";
    $result = mysql_query($sql);
    echo "<p class='text'>Thank you! Information entered.<br><br><a href='user-block.php'>Complete more blocking operations</a>.</p>";
} else {
    if (isset($_GET['unblock'])) {
        $sql = "UPDATE $playerstable SET approved = 'yes' WHERE name='$_GET[unblock]'";
        $result = mysql_query($sql);
        if ($result) {
            echo "<p>".$_GET['unblock']." has been unblocked.</p>";
        }
    }
?>
<form method="post">
<table border="0" cellpadding="0">
<tr>
<td><p class="text">Name:</p></td>
<td><select size="1" name="name" class="text">
<?php
$sql = "SELECT * FROM $playerstable WHERE approved='yes' ORDER BY name ASC";
$result = mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
    echo"<option>$row[name]</option>";
}
?>
</select></td>
</tr>
</table>
<p><input type="Submit" name="submit" value="Block." class="text"><br>
</form>
<hr />
<?php

$sql = "SELECT name from $playerstable WHERE approved='no' ORDER BY name ASC";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysql_fetch_array($result)) {
        echo"<li><a href='user-block.php?unblock=".urlencode($row['name'])."'>Unblock</a> $row[name]</li>";
    }
    echo "</ul>";
}
}
require('./../bottom.php');
?>
