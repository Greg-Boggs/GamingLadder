<?php
session_start();
require('conf/variables.php');
require_once 'autologin.inc.php';
include 'include/avatars.inc.php';
require('top.php'); ?>

<script type="text/javascript">
    $(document).ready(function () {
            $("#ladder").tablesorter({sortList: [[0, 0]], widgets: ['zebra']});
        }
    );
</script>

<?php


// Let's see what group we're in. We want the info in order to mark the row with a different colour if the user viewing the page is logged in and his name is in that row...

if (isset($_SESSION['username']) && $_SESSION['username'] != "") {

    $result = mysqli_query($db, $standingsSqlWithRestrictions . " LIMIT " . $playersshown);
    $row = mysqli_fetch_array($result);

// Init variables..

    $classbaseelo = $row['rating'];
    $rank = 0;


    @mysqli_data_seek($result, 0);

    while ($row = mysqli_fetch_array($result)) {


        if ($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE) || ($row['rating'] >= ($classbaseelo))) {

            $rank++;


            /*		if  ($row['rating'] <= ($classbaseelo - GADDER_ELO_RANGE)) {
                            $rank++;

                */

            $classbaseelo = $row['rating'];
            if ($row['name'] == $_SESSION['username']) {
                $mygroup = $rank;
            }


        } else {

            if ($row['name'] == $_SESSION['username']) {
                $mygroup = $rank;
            }

        }

    }

}

?>

<h2>Skill Classes</h2>
<table id="ladder" class="tablesortergadder">
    <thead>
    <tr>
        <th align="left" width='7%'>Class</th>

        <th align="left">Avatar</th>

        <th align="left">Members&nbsp; &nbsp;</th>
    </tr>
    </thead>

    <?php

    // display the results...

    // reset  vars

    $result = mysqli_query($db, $standingsSqlWithRestrictions . " LIMIT " . $playersshown);
    $row = mysqli_fetch_array($result);

    $previousrating = 999999;
    $mygroup = -1;
    $rank = 0;
    $myclassrank = 0;
    $classbaseelo = $row['rating'];

    @mysqli_data_seek($result, 0);


    while ($row = mysqli_fetch_array($result)) {


    if (($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE)) || (($row['rating'] >= $classbaseelo) && ($previousrating != $classbaseelo))) {

        $rank++;
    }

    if ($rank == $mygroup) { // Set row colouring
        $trclass = "<tr class='myrow'>";
        $myclassrank++;
    } else {
        $trclass = "<tr>";
    }


    // Set the flag if it's enabled. Players which haven't specified country won't have a flag shown at all.

    $flagprefix = "";
    if (GADDER_FLAGS == 1) {

        if ($row['country'] != "No Country") {
            $flagprefix = "<img src='graphics/flags/" . $row['country'] . ".bmp'> &nbsp;";
        } else {
            $flagprefix = "";
        }

    }

    // Finally time to display it all..

    if (($row['rating'] < ($classbaseelo - GADDER_ELO_RANGE)) || (($row['rating'] >= $classbaseelo) && ($previousrating != $classbaseelo))) {


    echo "</td></tr>$trclass<td class=\"gadderrank\">$rank</td><td>" . WlAvatar::image($row['Avatar']) . "</td><td>$flagprefix "; ?>

    <a title='<?php echo $row['rating'] . " points"; ?>'
       href='profile.php?name=<?php echo $row['name']; ?>'><?php echo $row['name'] . "</a>";

        if (($myclassrank) && ($row['name'] == $_SESSION['username'])) {
            echo " (" . $myclassrank . ") ";
        }

        $classbaseelo = $row['rating'];

        } else {

        echo ", $flagprefix"; ?>
        <a title='<?php echo $row['rating'] . " points"; ?>'
           href='profile.php?name=<?php echo $row['name']; ?>'><?php echo $row['name'] . "</a>";


            if (($myclassrank) && ($row['name'] == $_SESSION['username'])) {
                echo " (" . $myclassrank . ") ";
            }

            }


            $previousrating = $row['rating'];
            }
            echo "</table>";

            ?>

            <p class="copyleft">Players in the same skill class are estimated to be of about the same skill, hence they
                have the same rank in here since rank equals the skill class you are in. The elo range used to determine
                the class belonging is currently set to <?php echo " " . GADDER_ELO_RANGE; ?>.
                1:st class = all players that are <= <?php echo " " . GADDER_ELO_RANGE; ?> points from the number 1
                players rating. The player that is first to be excluded creates 2:nd class, and so on. To <i>show up</i>
                in the skill class listing above you must have played >= <?php echo "$gamestorank"; ?> games, have a
                rating of >= <?php echo "$ladderminelo"; ?> & have played within <?php echo "$passivedays"; ?> days.
            </p><br/>

            <?php
            require('bottom.php');
            ?>
