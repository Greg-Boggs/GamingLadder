<?php
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');

if (isset($_POST['submit']) && $_POST['submit']) {
    $sql = "DELETE FROM $newstable WHERE news_id = '" . intval($_POST['edit']) . "'";
    $result = mysqli_query($db, $sql);
    echo "<p class='header'>News deleted.</p>";
} else {
    ?>
    <p class="header">Delete news.</p>
    <form method="post">
        <table border="0" cellpadding="0" width="100%">
            <tr>
                <td><p class="text">Delete news?</p></td>
            </tr>
        </table>
        <p class="text">
            <input type='hidden' name='edit' value="<? echo $_GET['edit'] ?>">
            <input type="Submit" name="submit" value="Delete." class="text"><br>
    </form>
    <?php
}
require('./../bottom.php');
?>
