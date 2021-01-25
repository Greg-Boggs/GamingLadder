<?php
session_start();
$page = "Recover password";
require('conf/variables.php');
require('top.php');
include 'include/avatars.inc.php';
require 'vendor/autoload.php';
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
                $email = new \SendGrid\Mail\Mail();
                $email->setFrom($laddermailsender, $titlebar);
                $email->setSubject($subject);
                $email->addTo($to, $player_id = $row['name']);
                $email->addContent("text/plain", strip_tags($body));
                $email->addContent(
                    "text/html", $body
                );
                $sendgrid = new \SendGrid(SENDGRID_KEY);
                try {
                    $response = $sendgrid->send($email);
//                    print $response->statusCode() . "\n";
//                    print_r($response->headers());
//                    print $response->body() . "\n";
                    echo "A password reset link was sent to your email. Check your spam folder";

                } catch (Exception $e) {
                    echo 'Mail has not been sent, an error happened ' . $e->getMessage() . "\n";
                }
            }
            else {
                echo "No matching player for this email";
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

function send_mail($to, $body, $subject, $fromaddress, $fromname)
{
    $eol = "\r\n";
    $mime_boundary = md5(time());

    # Common Headers
    $headers="";
    $headers .= "From: " . $fromname . "<" . $fromaddress . ">" . $eol;
    $headers .= "Reply-To: " . $fromname . "<" . $fromaddress . ">" . $eol;
    $headers .= "Return-Path: " . $fromname . "<" . $fromaddress . ">" . $eol;    // these two to set reply address
    $headers .= "Message-ID: <" . time() . "-" . $fromaddress . ">" . $eol;
    $headers .= "X-Mailer: Thunderbird" . $eol;          // These two to help avoid spam-filters

    # Boundry for marking the split & Multitype Headers
    $headers .= 'MIME-Version: 1.0' . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $mime_boundary . "\"" . $eol . $eol;

    # Open the first part of the mail
    $msg = "--" . $mime_boundary . $eol;

    $htmlalt_mime_boundary = $mime_boundary . "_htmlalt"; //we must define a different MIME boundary for this section
    # Setup for text OR html -
    $msg .= "Content-Type: multipart/alternative; boundary=\"" . $htmlalt_mime_boundary . "\"" . $eol . $eol;

    # Text Version
    $msg .= "--" . $htmlalt_mime_boundary . $eol;
    $msg .= "Content-Type: text/plain; charset=iso-8859-1" . $eol;
    $msg .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    $msg .= strip_tags(str_replace("<br>", "\n", substr($body, (strpos($body, "<body>") + 6)))) . $eol . $eol;

    # HTML Version
    $msg .= "--" . $htmlalt_mime_boundary . $eol;
    $msg .= "Content-Type: text/html; charset=iso-8859-1" . $eol;
    $msg .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    $msg .= $body . $eol . $eol;

    //close the html/plain text alternate portion
    $msg .= "--" . $htmlalt_mime_boundary . "--" . $eol . $eol;


    # Finished
    $msg .= "--" . $mime_boundary . "--" . $eol . $eol;  // finish with two eol's for better security. see Injection.

    # SEND THE EMAIL
    ini_set('sendmail_from', $fromaddress);  // the INI lines are to force the From Address to be used !
    $mail_sent = mail($to, $subject, $msg, $headers);

    ini_restore('sendmail_from');

    return $mail_sent;
}

