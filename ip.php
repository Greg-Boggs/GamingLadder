<?php
require('conf/variables.php');
require('top.php');
?>
<p class="header">Duplicate IP check.</p>
<p>Identify players that are using the same IP address at the moment.</p>
<?php
$sql = "SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR ', ') as namelist FROM $playerstable GROUP BY ip HAVING count(name) > 1 ORDER BY namelist ASC";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) == 0) {
    ?>
    <p class="text">There are no players with identical ip's.</p>
    <?php
} else {
    echo '<table width="80%" border="1" bgcolor="#E7D9C0" cellspacing="0" cellpadding="2">';
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr><td class='text'>" . $row['namelist'] . "</td></tr>";
    }
    echo "</table>";
}
require('bottom.php');
?>
