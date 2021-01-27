<?php
session_start();
$page = "Recover password";
require('conf/variables.php');
require('top.php');
include 'include/avatars.inc.php';
require 'vendor/autoload.php';
include 'include/genericfunctions.inc.php';

?>

<p class="header">Recover Password</p>
<p class="text"> Type in your email and give it a second... If you don't know your email, then we can't help you.</p>
<p>

    <?php
    if (isset($_POST['submit']) && isset($_POST['mail'])) {
        $mail = trim(strip_tags($_POST['mail']));

        if ($mail == "") {
            echo "<p>You must enter an email to recover your password</p>";
        } else {
            $confirm_code = md5(uniqid(rand()));

            $sql = "SELECT player_id, name FROM $playerstable WHERE mail = '$mail'";
            $result = mysqli_query($db, $sql);


            // if sucessfully found email in database, send confirmation link to email
            if ($result) {

                $row = mysqli_fetch_array($result);
                $player_id = $row['player_id'];

                // store the confirmation key to later be used for login.
                $sql = "INSERT INTO $resettable (passcode, player_id) VALUES ('$confirm_code', $player_id)";
                $result = mysqli_query($db, $sql);

                $to = $mail;

                $subject = "Ladder of Wesnoth password reset link";
                $body = "This is your Wesnoth Ladder password reset mail. \r\n";
                $body .= "Click the link below to activate your account: \r\n";
                $body .= '<a href="http://' . $_SERVER['HTTP_HOST'] . "/reset-pass.php?passkey=$confirm_code\">";
                $body .= 'http://' . $_SERVER['HTTP_HOST'] . "/reset-pass.php?passkey=$confirm_code</a> \r\n";
                $body .= "If it doesnt work you can try to copy & pass it into your browser instead.\r\n";
                $body .= "\r\n";

                $body .= "See you in Wesnoth...\n";

                // send email
                $sentmail = send_mail($to, $body, $subject, $laddermailsender, $titlebar);
            }
            require('bottom.php');

        }
    } else {
    ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <table border="0" cellpadding="0">
            <tr>
                <td>Email: <input type="Text" name="mail" value="" class="text"></td>
                <td><p class="text"><input type="Submit" name="submit" value="Recover" class="text"></p></td></tr>
    </form>
    </table></p>
<?php
require('bottom.php');
}