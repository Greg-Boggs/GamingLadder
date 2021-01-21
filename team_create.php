<?php
session_start();
require('conf/variables.php');
require('logincheck.inc.php');//includes variables.php
require('top.php');
include 'include/avatars.inc.php';


if (isset ($_POST['submit'])) {

//reading the form input
    $teamname = trim(strip_tags($_POST['teamname']));
    $length = strlen($teamname);

    //create the array that temporarily holds the membernames 
    $names = array();
    //add the first member (user himself) to the array
    $names[0] = $_SESSION['username'];
    //add all selected names to the array
    $i = 1;
    while ($i < NUMBER_MEMBERS) {
        $names[$i] = trim(strip_tags($_POST['username' . $i]));
        $i++;
    }

    $avatar = $_POST['avatar'];


    //here comes the part of errorchecking etc.

    //teamname checking
    if ($teamname == "") {
        echo "Please enter a teamname.";
    } elseif (($length > REG_MAX_NICKLENGTH) || ($length < REG_MIN_NICKLENGTH)) {
        echo "The teamname you entered is invalid. It must be " . REG_MIN_NICKLENGTH . " to " . REG_MAX_NICKLENGTH . " characters long.<br />Please go back to correct the error by selecting a different username.";
    } elseif (!preg_match("/^[a-zA-Z0-9\-\_[:space:]]+$/i", $teamname)) {
        echo "You're only allowed to use standard aA-zZ 0-9 alphanumerical characters and the - and _ signs. <br>Please enter a valid teamname.";
    } else {

        if ($approve == 'yes') {
            $approved = 'no';
        } else {
            $approved = 'yes';
        }

        //check for an existing teamname
        $sql = "SELECT * FROM $teamstable WHERE name = '$teamname'";
        $result = mysqli_query($db, $sql);
        $samenick = mysqli_num_rows($result);

        // if already the same teamname exists
        if ($samenick == 1) {
            echo "Sorry but it seems someone was faster in picking this name. Try another one.";
        } //teampartner checking
        else {
            //check if all positions are filled
            $i = 0;
            while ($i < NUMBER_MEMBERS) {
                if ($names[$i] == "") {
                    echo "Sry, but you have to chose for every spot a teammates.";
                    echo "<br />";
                } // just counts the elements of $names vs the required number NUMBER_MEMBERS
                $i++;
            }

            //check duplicates
            if (array_unique($names) != $names) {
                echo "Sry, but a team should consist of (at least) " . NUMBER_MEMBERS . " <b>different</b> people";
                echo "<br />";
            } else {
                //check if all of them are registered
                $sql = "SELECT name FROM $playerstable";
                $result = mysqli_query($db, $sql);

                $samenick = array();
                while ($row = mysqli_fetch_array($result)) {
                    $samenick[] = $row['name'];
                }

                //write all the names that match with the mysql databes into a new array
                $namecheck = array();
                $namecheck = array_intersect($samenick, $names);

                //write the difference into a new array (be cautious with the order of the argument!!!!)
                $notregistered = array();
                $notregistered = array_diff($names, $namecheck);

                //count the elements of the array and display message if neccessary
                if (count($notregistered) > 0) {
                    echo "Sry but the player <br />";
                    foreach ($notregistered as $value) {
                        echo "$value <br />";
                    }
                    echo "couldnt be found in the database, be sure only to chose registered players.";
                    echo "<br />";
                } // if all checkings are passed write the team to the database
                else {
                    if (REG_MAILVERIFICATION == 1) {
                        // Random confirmation code
                        $confirm_code = md5(uniqid(rand()));
                    } else {
                        $confirm_code = "Ok";
                        // if we dont have mail confirmation enabled in the config we will "autoverify" the user by setting him to "Ok" in the Confirmation rown in the players table.
                    }

                    //create the query
                    $query = "INSERT INTO $teamstable (name, approved, avatar, Confirmation, ";

                    //fill the coloumn identifiers
                    $i = 1;
                    while ($i < NUMBER_MEMBERS + 1) {
                        $query .= "player" . $i . " ";
                        //add comma separation
                        if ($i < NUMBER_MEMBERS) {
                            $query .= ", ";
                        }
                        $i++;

                    }
                    $query .= " ) VALUES (";
                    //fill in the values
                    $i = 0;
                    $query .= "'" . $teamname . "', '" . $approved . "', '" . $avatar . "', '" . $confirm_code . "', ";

                    while ($i < NUMBER_MEMBERS) {
                        $query .= "'" . $names[$i] . "' ";
                        //add comma separation
                        if ($i < NUMBER_MEMBERS - 1) {
                            $query .= ", ";
                        }
                        $i++;

                    }
                    //complete the query
                    $query .= ")";

                    // finally execute the query
                    $sql = $query;
                    $result = mysqli_query($db, $sql);
                    if ($result) {
                        echo "Success! Your team is now registered in the database.";
                    } else {
                        echo "something was wrong with the sql query, couldnt write team into database";
                    }


                }
            }
        }
    }
}



//message that all is fine should appear here....
//
//
//

else {
    ?>

    <p class="header">Create a Team.</p>
    <p class="text">

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <table border="0" cellpadding="0">
                <!-- Teamname Selection -->
                <tr>
                    <td><p class="text"><b>Teamname</b></p></td>
    <td>&nbsp;<input type="Text" name="teamname" class="text">
        (<?php echo REG_MIN_NICKLENGTH . " - " . REG_MAX_NICKLENGTH . " "; ?> char)
    </td>
    </tr>
    <!-- Team-Avatar Selection -->
    <tr>
        <td><p class="text"><b>Team-Avatar</b></p></td>
        <td>&nbsp;<select size="1" name="avatar" class="text">
                <?php
                foreach (WlAvatar::all() as $avatarKey => $avatarImg) {
                    printf(
                        '<option value="%s">%s</optionvalue>',
                        $avatarKey,
                        $avatarKey
                    );
                }
                ?>
            </select></td>
        <!-- Selection of the different team members -->
    <tr>
        <td><p class="text"><b>Now select the members of your team.</b></p></td>
    </tr>

    <tr>
        <td><p class="text"><b>Your Nickname:</b></p></td>
        <td><p class="text"><?php echo $_SESSION['username']; ?></p></td>
    </tr>
    <?php

//create the entrylist for teammates the player select, the count is stored in the ladder constant NUMBER_MEMBERS
    //players without the creator of the team
    $i = 1;
    while ($i < NUMBER_MEMBERS) { ?>
        <tr>
            <td><p class="text"><b><?php echo $i; ?>. partner </b></p></td>
            <td>&nbsp;<input type="Text" name="username<?php echo $i; ?>" class="text"></td>
        </tr>
        <?php $i++;
    } ?>

    </table>

    <p class="text"><input type="submit" name="submit" value="Create."><br><br></p>
    </form>


    <?php
}
require('bottom.php');
?>






