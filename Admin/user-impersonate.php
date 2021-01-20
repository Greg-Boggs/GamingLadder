<?php
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');

?>
<p class="header">Impersonate another Player</p>
<p>You use player impersonation to complete tasks on their behalf. Eg update their account, report a game, contest a
    game on a losers behalf or delete a game.</p>
<p>To stop impersonation, please impersonate yourself.</p>
<?php
if (isset($_POST['submit']) && $_POST['submit']) {
    $_SESSION['username'] = $_POST['name'];
}
?>
<form method="post">
    <table border="0" cellpadding="0">
        <tr>
            <td><p class="text">Name:</p></td>
            <td><select size="1" name="name" class="text">
                    <?php
                    $sql = "SELECT * FROM $playerstable WHERE approved='yes' ORDER BY name ASC";
                    $result = mysqli_query($db, $sql);
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value="' . $row['name'] . '">' . $row['name'] . "</option>";
                    }
                    ?>
                </select></td>
        </tr>
    </table>
    <p><input type="Submit" name="submit" value="Impersonate" class="text"><br>
</form>
<hr/>
<?php
require('./../bottom.php');
?>
