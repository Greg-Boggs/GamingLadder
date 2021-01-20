<?php
require('conf/variables.php');
require('top.php');

echo "<h2>Account Activation</h2>";
// Passkey that got from the link th euser clicked / got in his verification mail
$passkey = $_GET['passkey'];
if (!isset($_GET['passkey'])) {
    echo "<p>No confirmation key was supplied, please confirm the link is complete and includes the confirmation key.</p>";
    require('bottom.php');
    exit;
}

// Retrieve data from table where row that match this passkey
$sql1 = "SELECT * FROM $playerstable WHERE Confirmation ='$passkey'";
$result1 = mysqli_query($db, $sql1);

// If there is a confirmation key with this label.
if ($result1 && mysqli_num_rows($result1) === 1) {
    // Change the users passkey to a Ok, as a sign of the user passing the registration
    // Shows the numner of seconds since 70.
    $timesec = date("U");

    $sql = "UPDATE $playerstable SET Confirmation='Ok', Joined='$timesec' WHERE Confirmation ='$passkey'";
    $result2 = mysqli_query($db, $sql);

    if ($result2) {
        //deb  echo "Passkey was used successfully & cleared.";
        ?>
        <br/>
        <img align="center" src="graphics/activated.jpg"/>
        <?php
    } else {
        echo "<p>Database Error: Couldnt clear validation key. Please retry or contact admin if the problem remains.</p>";
    }
} else {
    ?>
    <ol>
        <li>Either you have already verfied once, in which case you can now login with your user &amp; password</li>
        <li>You supplied us with the wrong Confirmation code, in which case you should please check if you used the very
            exact verification link sent to you by mail and try again.
        </li>
        <li>There was an error retreiving the confirmation code, in which case, please try again later</li>
    </ol>
    <?php
}

require('bottom.php');
?>
