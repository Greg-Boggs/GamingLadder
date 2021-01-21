<?php
session_start();
// v 1.01
require('conf/variables.php');
require_once 'include/gametable.inc.php';

// Handle all of the cookie management before we attempt to do anything else
$searchArray = array_merge(
    [
        'reporteddirection' => '',
        'reportdate' => '',
        'winner' => '',
        'loser' => '',
        'loserratingdirection' => '',
        'winnerratingdirection' => '',
        'winnerrating' => '',
        'loserrating' => '',
        'replay' => '',
        'playerand' => '',
        'ratingand' => '',
    ],
    unserialize(base64_decode($_COOKIE['gamehistoryoptions']))
);
if (isset($_GET['reporteddirection'])) $searchArray['reporteddirection'] = $_GET['reporteddirection'];
if (isset($_GET['reportdate'])) $searchArray['reportdate'] = $_GET['reportdate'];
if (isset($_GET['reportdate'])) $searchArray['reportdate'] = $_GET['reportdate'];
if (isset($_GET['winner'])) $searchArray['winner'] = $_GET['winner'];
if (isset($_GET['loser'])) $searchArray['loser'] = $_GET['loser'];
if (isset($_GET['loserratingdirection'])) $searchArray['loserratingdirection'] = $_GET['loserratingdirection'];
if (isset($_GET['winnerratingdirection'])) $searchArray['winnerratingdirection'] = $_GET['winnerratingdirection'];
if (isset($_GET['winnerrating'])) $searchArray['winnerrating'] = $_GET['winnerrating'];
if (isset($_GET['loserrating'])) $searchArray['loserrating'] = $_GET['loserrating'];
if (isset($_GET['replay'])) $searchArray['replay'] = $_GET['replay'];
if (isset($_GET['playerand'])) $searchArray['playerand'] = $_GET['playerand'];
if (isset($_GET['ratingand'])) $searchArray['ratingand'] = $_GET['ratingand'];

if ($searchArray['reporteddirection'] <> "<=" && $searchArray['reporteddirection'] <> ">=") $searchArray['reporteddirection'] = "";
if ($searchArray['winnerratingdirection'] <> "<=" && $searchArray['winnerratingdirection'] <> ">=") $searchArray['winnerratingdirection'] = "";
if ($searchArray['loserratingdirection'] <> "<=" && $searchArray['loserratingdirection'] <> ">=") $searchArray['loserratingdirection'] = "";
if ($searchArray['playerand'] <> "AND" && $searchArray['playerand'] <> "OR") $searchArray['playerand'] = "AND";
if ($searchArray['ratingand'] <> "AND" && $searchArray['ratingand'] <> "OR") $searchArray['ratingand'] = "AND";

setcookie("gamehistoryoptions", base64_encode(serialize($searchArray)), time() + 7776000);

require_once 'autologin.inc.php';
require('top.php');

if (isset($_REQUEST['selectname'])) {
    $playerquerystring = "&selectname=" . $_REQUEST['selectname'];
} else {
    $playerquerystring = "";
}
?>
<p class="header">Played games.</p>
<p>A maximum of 250 games will be displayed at any one time. If games are missing, please refine your search further.
    They are selected
    in reported on order descending, so any missing games will be the oldest games from the selection criteria.</p>
<form method="get" action="gamehistory.php">
    <script type="text/javascript">
        $(document).ready(function () {
                $("#games").tablesorter({
                    sortList: [[0, 1]], widgets: ['zebra'],
                    headers: {
                        5: {sorter: false},
                        7: {sorter: false}
                    }
                });
            }
        );
    </script>

    <table id="games" class="tablesorter">
        <thead>
        <tr>
            <th>Reported</th>
            <th>Winner</th>
            <th>Loser</th>
            <th title="W. Elo rating after game (points earned due to the game)">W Rating</th>
            <th title="L. Elo rating after game (points lost due to the game)">L Rating</th>
            <th title="W. Rank when & before game was played">W Rank</th>
            <th title="L. Rank when & before game was played">L Rank</th>
            <th title="New W. Rank, rank of winner after game">NW Rank</th>
            <th title="New L. Rank, rank of loser after game">NL Rank</th>
            <th title="Sportm. Rating given to winner / Loser, & if W or L commented">Feedback</th>
            <th>Replay</th>
            <th>Details</th>
        </tr>
        <tr>
            <td><select name="reporteddirection">
                    <option <?php if ($searchArray['reporteddirection'] == "") echo "selected='selected'"; ?> value="">
                        --
                    </option>
                    <option <?php if ($searchArray['reporteddirection'] == "<=") echo "selected='selected'"; ?>
                            value="&lt;=">&lt;=
                    </option>
                    <option <?php if ($searchArray['reporteddirection'] == ">=") echo "selected='selected'"; ?>
                            value="&gt;=">&gt;=
                    </option>
                </select>
                <input type="text" value="<?php echo $searchArray['reportdate']; ?>" name="reportdate" size="9"/></td>
            <td><input type="text" value="<?php echo $searchArray['winner']; ?>" name="winner" size="10"/></td>
            <td><input type="text" value="<?php echo $searchArray['loser']; ?>" name="loser" size="10"/></td>
            <td><select name="winnerratingdirection">
                    <option <?php if ($searchArray['winnerratingdirection'] == "") echo "selected='selected'"; ?>
                            value="">--
                    </option>
                    <option <?php if ($searchArray['winnerratingdirection'] == "<=") echo "selected='selected'"; ?>
                            value="&lt;=">&lt;=
                    </option>
                    <option <?php if ($searchArray['winnerratingdirection'] == ">=") echo "selected='selected'"; ?>
                            value="&gt;=">&gt;=
                    </option>
                </select>
                <input type="text" value="<?php echo $searchArray['winnerrating']; ?>" name="winnerrating" size="3"/>
            </td>
            <td><select name="loserratingdirection">
                    <option <?php if ($searchArray['loserratingdirection'] == "") echo "selected='selected'"; ?>
                            value="">--
                    </option>
                    <option <?php if ($searchArray['loserratingdirection'] == "<=") echo "selected='selected'"; ?>
                            value="&lt;=">&lt;=
                    </option>
                    <option <?php if ($searchArray['loserratingdirection'] == ">=") echo "selected='selected'"; ?>
                            value="&gt;=">&gt;=
                    </option>
                </select>
                <input type="text" value="<?php echo $searchArray['loserrating']; ?>" name="loserrating" size="3"/>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><select name="replay">
                    <option <?php if ($searchArray['replay'] == "") echo "selected='selected'"; ?> value="">All</option>
                    <option <?php if ($searchArray['replay'] == "yes") echo "selected='selected'"; ?> value="yes">Yes
                    </option>
                    <option <?php if ($searchArray['replay'] == "no") echo "selected='selected'"; ?> value="no">No
                    </option>
                </select>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="vertical-align: middle; text-align: center"><input type="submit" name="submit"
                                                                          value="Update Search"/></td>
            <td style="vertical-align: middle; text-align: center" colspan="2"><select name="playerand">
                    <option <?php if ($searchArray['playerand'] == "AND") echo "selected='selected'"; ?> value="AND">
                        and
                    </option>
                    <option <?php if ($searchArray['playerand'] == "OR") echo "selected='selected'"; ?> value="OR">or
                    </option>
                </select> above options together.
            </td>
            <td style="vertical-align: middle; text-align: center" colspan="2"><select name="ratingand">
                    <option <?php if ($searchArray['ratingand'] == "AND") echo "selected='selected'"; ?> value="AND">
                        and
                    </option>
                    <option <?php if ($searchArray['ratingand'] == "OR") echo "selected='selected'"; ?> value="OR">or
                    </option>
                </select> above options together.
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        </thead>
        <?php
        // Select the games we are interested in

        // Build where clause
        // Store all this in a cookie
        $where = "(winner like '%" . $searchArray['winner'] . "%' " . $searchArray['playerand'] . " loser like '%" . $searchArray['loser'] . "%') ";

        // Setup ratings in query
        if ($searchArray['winnerratingdirection'] != "" && $searchArray['winnerrating'] != "") {
            $where .= " AND (winner_elo " . $searchArray['winnerratingdirection'] . " '" . $searchArray['winnerrating'] . "' " . $searchArray['ratingand'] . " ";
            if ($searchArray['loserratingdirection'] != "" && $searchArray['loserrating'] != "") {
                $where .= " loser_elo " . $searchArray['loserratingdirection'] . " '" . $searchArray['loserrating'] . "') ";
            } else {
                $where .= " 1=1)";
            }
        } else {
            // Still do the loser rating stuff
            if ($searchArray['loserratingdirection'] != "" && $searchArray['loserrating'] != "") {
                $where .= " AND loser_elo " . $searchArray['loserratingdirection'] . " '" . $searchArray['loserrating'] . "' ";
            }
        }

        // Add replay restrictions
        if ($searchArray['replay'] == "no") {
            $where .= " AND replay_filename IS NULL ";
        } else if ($searchArray['replay'] == "yes") {
            $where .= " AND lreplay_filename IS NOT NULL ";
        }

        // Add reported_on restrictions
        if ($searchArray['reporteddirection'] != "" && $searchArray['reportdate'] != "") {
            $where .= " AND reported_on " . $searchArray['reporteddirection'] . " '" . $searchArray['reportdate'] . "' ";
        }

        // Build the select
        $sql = "SELECT withdrawn, contested_by_loser, DATE_FORMAT(reported_on, '" . $GLOBALS['displayDateFormat'] . "') as report_time, reported_on, winner, loser, winner_points, loser_points, winner_elo, loser_elo, w_rank, l_rank, w_new_rank, l_new_rank, replay_filename as is_replay, replay_downloads, winner_stars, loser_stars FROM $gamestable WHERE $where ORDER BY reported_on DESC LIMIT 250";

        $result = mysqli_query($db, $sql);
        echo gameTableTBody($result);
        ?>
    </table>
</form>
<br/>
<?php
require('bottom.php');
?>
