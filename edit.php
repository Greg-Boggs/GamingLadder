<?php
session_start();
// v. 1.07

require('conf/variables.php');
require_once 'autologin.inc.php';
require_once 'logincheck.inc.php';

require('top.php');
include 'include/genericfunctions.inc.php';
include 'include/avatars.inc.php';

// Lets check to see if there are Ladder cookies to see if the user is logged in. If so, we wont show the login box....
// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.
echo "<br /><h2>" . $_SESSION['username'] . "</h2>";

if (isset($_POST['submit']) && $_POST['submit']) {
    // Lets generate the encrypted pass, after all, its the one thats stored in the database... we do it by applying the salt and hashing it twice.
// We need to take the users real pass, "encrypt" it the same way we did when he registered, and then compare the results.
// $salt is read from config file

    $passworddb2 = $salt . $_POST['passworddb'];
    $passworddb2 = md5($passworddb2);
    $passworddb2 = md5($passworddb2);

    $sql = "SELECT * FROM $playerstable WHERE name='" . $_SESSION['username'] . "'";
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $msn = check_plain(trim(strip_tags($_POST['msn'])));
        $icq = check_plain(trim(strip_tags($_POST['icq'])));
        $aim = check_plain(trim(strip_tags($_POST['aim'])));
        $mail = check_plain(trim(strip_tags($_POST['mail'])));

        $CanPlay = "";
        foreach ([
                     'MonM', 'MonN', 'MonA', 'MonE', 'MonNi', 'TueM', 'TueN', 'TueA', 'TueE', 'TueNi', 'WedM', 'WedN', 'WedA', 'WedE', 'WedNi', 'ThuM', 'ThuN', 'ThuA', 'ThuE', 'ThuNi', 'FriM', 'FriN', 'FriA', 'FriE', 'FriNi', 'SatM', 'SatN', 'SatA', 'SatE', 'SatNi', 'SunM', 'SunN', 'SunA', 'SunE', 'SunNi',
                 ] as $period) {
            if (isset($_POST[$period]) && $_POST[$period] != "") {
                $CanPlay .= ' ' . check_plain($_POST[$period]);
            }
        }

// check if the user wants a new password than the old. 
        $newpassworddb = false;
        if ($_POST['newpassworddb'] == $_POST['newpassworddb2']) {
            // if the same new pass was entered twice he should get a new pass.... one will be generated now:
            if ($_POST['newpassworddb'] != "" && $_POST['newpassworddb2'] != "") {
                // Apparently he wants a new pass... let's compare the new pass verification:
                $newpassworddb = $salt . $_POST['newpassworddb'];
                $newpassworddb = md5($newpassworddb);
                $newpassworddb = md5($newpassworddb);
            }
        } else {
            echo "New password verification didn't match. Please re-type and verify the new password.";
            include 'bottom.php';
            exit;
        }


        // Depending on if he wants a new pass or wants to keep the old we need 2 different sql queries...
        // An administrator can impersonate a user and update information about them without entering the original password.
        if ($newpassworddb && $newpassworddb != "") {
            $sql = "UPDATE $playerstable SET mail = '$mail', icq = '$icq', aim = '$aim', msn = '$msn', country = '$_POST[country]', Avatar = '$_POST[avatar]', MsgMe = '$_POST[msgme]', HaveVersion = '$_POST[version]', CanPlay = '$CanPlay', Jabber = '$_POST[jabber]', passworddb = '$newpassworddb' WHERE name='" . $_SESSION['username'] . "' AND (passworddb = '$passworddb2' OR '" . $_SESSION['real-username'] . "' <> '" . $_SESSION['username'] . "')";
        } else {
            $sql = "UPDATE $playerstable SET mail = '$mail', icq = '$icq', aim = '$aim', msn = '$msn', country = '$_POST[country]', Avatar = '$_POST[avatar]', MsgMe = '$_POST[msgme]', HaveVersion = '$_POST[version]', CanPlay = '$CanPlay', Jabber = '$_POST[jabber]' WHERE name='" . $_SESSION['username'] . "' AND (passworddb = '$passworddb2' OR '" . $_SESSION['real-username'] . "' <> '" . $_SESSION['username'] . "')";
        }
        $result = mysqli_query($db, $sql);
        echo "<p class='text'>Profile is now updated.</p>";
        if ($newpassworddb && $newpassworddb != "") {
            echo "<p class='text'><br>Since you changed password you will need to login to the site again to use the automatic login feature.  Please login with your new password.</p>";
        }
    } else {
        echo "<p class='text'>The password you entered is incorrect.<br><br><a href='edit.php'>Please try again.</a></p>";
    }
} else {
    ?>
    <?php
    $sql = "SELECT * FROM $playerstable WHERE name = '" . $_SESSION['username'] . "' ORDER BY name ASC";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);
    ?>
    <form method="post">
        <table border="0" cellpadding="0">

            <tr>
                <td>
                    <input type=hidden name="editname" value="<?php echo $_SESSION['username'] ?>">
                </td>
            </tr>

            <tr>
                <td><p class="text"><b>Password:</b></p></td>
                <td><input type="password" size="15" name="passworddb" class="text"> You <i>must</i> enter this to save
                    settings.
                </td>
            </tr>

            <tr>
                <td><p class="text">New Password:</p></td>
                <td><input type="password" size="15" name="newpassworddb" class="text"> Leave empty unless changing
                    password.
                </td>
            </tr>


            <tr>
                <td><p class="text">Verify New Password:</p></td>
                <td><input type="password" size="15" name="newpassworddb2" class="text"></td>
            </tr>


            <tr>
                <td><p class="text"><b>Mail:</b></p></td>
                <td><input type="Text" name="mail" value="<?php echo "$row[mail]" ?>" class="text"></td>
            </tr>

            <tr>
                <td><p class="text">Jabber:</p></td>
                <td><input type="Text" name="jabber" value="<?php echo "$row[Jabber]" ?>" class="text"> Open source, Same
                    as g-mail chat.
                </td>
            </tr>

            <tr>
                <td><p class="text">Icq:</p></td>
                <td><input type="Text" name="icq" value="<?php echo "$row[icq]" ?>" class="text"></td>
            </tr>
            <tr>
                <td><p class="text">Aim:</p></td>
                <td><input type="Text" name="aim" value="<?php echo "$row[aim]" ?>" class="text"></td>
            </tr>
            <tr>
                <td><p class="text">Msn:</p></td>
                <td><input type="Text" name="msn" value="<?php echo "$row[msn]" ?>" class="text"></td>
            </tr>
            <tr>
                <td><p class="text">Country:</p></td>
                <td><select size="1" name="country" class="text">
                        <option selected><?php echo "$row[country]" ?></option>
                        <?php include("include/countries.inc.php"); ?>
                    </select></td>
            </tr>
            <tr>
                <td><p class="text">Avatar:</p></td>
                <td>&nbsp;<select size="1" name="avatar" class="text">
                        <?php
                        foreach (WlAvatar::all() as $avatarKey => $avatarImg) {
                            printf(
                                '<option value="%s" %s>%s</optionvalue>',
                                $avatarKey,
                                $row['Avatar'] === $avatarKey ? 'selected' : '',
                                $avatarKey
                            );
                        }
                        ?>
                    </select></td>
            </tr>


            <tr>
                <td><p class="text"><b>My version of Wesnoth:</b></p></td>

                <td>&nbsp;<select size="1" name="version" class="text">
                        <?php echo "<option>$row[HaveVersion]</option>"; ?>
                        <option>Stable</option>
                        <option>Development</option>
                        <option>Both</option>
                    </select></td>
            </tr>

            <tr>
                <td><p class="text">Msg me to play:</p></td>
                <td>&nbsp;<select size="1" name="msgme" class="text">
                        <?php echo "<option>$row[MsgMe]</option>"; ?>
                        <option>Yes</option>
                        <option>No</option>
                    </select></td>
            </tr>
        </table>


        <p class="text">I can usually play these times (GMT):</p>
        <table width="100%">


            <tr>

                <td></td>
                <td>Morning</td>
                <td>Noon</td>
                <td>Afternoon</td>
                <td>Evening</td>
                <td>Night</td>
            </tr>


            <tr>
                <td bgcolor="#E7D9C0">Monday</td>


                <td bgcolor="#E7D9C0"><input type="checkbox" name="MonM"
                                             value="MonM" <?php $pos1 = strpos("$row[CanPlay]", "MonM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>


                <td bgcolor="#E7D9C0"><input type="checkbox" name="MonN"
                                             value="MonN" <?php $pos1 = strpos("$row[CanPlay]", "MonN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="MonA"
                                             value="MonA" <?php $pos1 = strpos("$row[CanPlay]", "MonA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="MonE"
                                             value="MonE" <?php $pos1 = strpos("$row[CanPlay]", "MonE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="MonNi"
                                             value="MonG" <?php $pos1 = strpos("$row[CanPlay]", "MonG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>


            <tr>
                <td>Tuesday</td>
                <td><input type="checkbox" name="TueM" value="TueM" <?php $pos1 = strpos("$row[CanPlay]", "TueM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="TueN" value="TueN" <?php $pos1 = strpos("$row[CanPlay]", "TueN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="TueA" value="TueA" <?php $pos1 = strpos("$row[CanPlay]", "TueA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="TueE" value="TueE" <?php $pos1 = strpos("$row[CanPlay]", "TueE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="TueNi" value="TueG" <?php $pos1 = strpos("$row[CanPlay]", "TueG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>


            <tr>
                <td bgcolor="#E7D9C0">Wednesday</td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="WedM"
                                             value="WedM" <?php $pos1 = strpos("$row[CanPlay]", "WedM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="WedN"
                                             value="WedN" <?php $pos1 = strpos("$row[CanPlay]", "WedN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="WedA"
                                             value="WedA" <?php $pos1 = strpos("$row[CanPlay]", "WedA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="WedE"
                                             value="WedE" <?php $pos1 = strpos("$row[CanPlay]", "WedE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="WedNi"
                                             value="WedG" <?php $pos1 = strpos("$row[CanPlay]", "WedG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>

            <tr>
                <td>Thursday</td>
                <td><input type="checkbox" name="ThuM" value="ThuM" <?php $pos1 = strpos("$row[CanPlay]", "ThuM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="ThuN" value="ThuN" <?php $pos1 = strpos("$row[CanPlay]", "ThuN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="ThuA" value="ThuA" <?php $pos1 = strpos("$row[CanPlay]", "ThuA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="ThuE" value="ThuE" <?php $pos1 = strpos("$row[CanPlay]", "ThuE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="ThuNi" value="ThuG" <?php $pos1 = strpos("$row[CanPlay]", "ThuG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>

            <tr>
                <td bgcolor="#E7D9C0">Friday</td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="FriM"
                                             value="FriM" <?php $pos1 = strpos("$row[CanPlay]", "FriM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="FriN"
                                             value="FriN" <?php $pos1 = strpos("$row[CanPlay]", "FriN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="FriA"
                                             value="FriA" <?php $pos1 = strpos("$row[CanPlay]", "FriA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="FriE"
                                             value="FriE" <?php $pos1 = strpos("$row[CanPlay]", "FriE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="FriNi"
                                             value="FriG" <?php $pos1 = strpos("$row[CanPlay]", "FriG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>

            <tr>
                <td>Saturday</td>
                <td><input type="checkbox" name="SatM" value="SatM" <?php $pos1 = strpos("$row[CanPlay]", "SatM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="SatN" value="SatN" <?php $pos1 = strpos("$row[CanPlay]", "SatN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="SatA" value="SatA" <?php $pos1 = strpos("$row[CanPlay]", "SatA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="SatE" value="SatE" <?php $pos1 = strpos("$row[CanPlay]", "SatE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td><input type="checkbox" name="SatNi" value="SatG" <?php $pos1 = strpos("$row[CanPlay]", "SatG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
            </tr>

            <tr>
                <td bgcolor="#E7D9C0">Sunday</td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="SunM"
                                             value="SunM" <?php $pos1 = strpos("$row[CanPlay]", "SunM");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="SunN"
                                             value="SunN" <?php $pos1 = strpos("$row[CanPlay]", "SunN");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="SunA"
                                             value="SunA" <?php $pos1 = strpos("$row[CanPlay]", "SunA");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="SunE"
                                             value="SunE" <?php $pos1 = strpos("$row[CanPlay]", "SunE");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>
                <td bgcolor="#E7D9C0"><input type="checkbox" name="SunNi"
                                             value="SunG" <?php $pos1 = strpos("$row[CanPlay]", "SunG");
                    if ($pos1 != FALSE) {
                        echo "checked";
                    } ?>/></td>

        </table>

        <p><input type="Submit" name="submit" value="Submit." class="text"><br>

    </form>
    <?php
}


require('bottom.php');
?>
