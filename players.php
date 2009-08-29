<?
session_start();
require('conf/variables.php');

// Handle cookies setting before any display is made
$searchArray = unserialize(base64_decode($_COOKIE['playeroptions']));
if (isset($_GET['player'])) $searchArray['player'] = $_GET['player'];
if (isset($_GET['gamesdirection'])) $searchArray['gamesdirection'] = $_GET['gamesdirection'];
if (isset($_GET['winsdirection'])) $searchArray['winsdirection'] = $_GET['winsdirection'];
if (isset($_GET['lossesdirection'])) $searchArray['lossesdirection'] = $_GET['lossesdirection'];
if (isset($_GET['ratingdirection'])) $searchArray['ratingdirection'] = $_GET['ratingdirection'];
if (isset($_GET['streakdirection'])) $searchArray['streakdirection'] = $_GET['streakdirection'];
if (isset($_GET['country'])) $searchArray['country'] = $_GET['country'];
if (isset($_GET['games'])) $searchArray['games'] = $_GET['games'];
if (isset($_GET['wins'])) $searchArray['wins'] = $_GET['wins'];
if (isset($_GET['losses'])) $searchArray['losses'] = $_GET['losses'];
if (isset($_GET['rating'])) $searchArray['rating'] = $_GET['rating'];
if (isset($_GET['streak'])) $searchArray['streak'] = $_GET['streak'];

if ($searchArray['gamesdirection'] <> "<=" && $searchArray['gamesdirection'] <> ">=" && $searchArray['gamesdirection'] <> "=") $searchArray['gamesdirection'] = "";
if ($searchArray['winsdirection'] <> "<=" && $searchArray['winsdirection'] <> ">=" && $searchArray['winsdirection'] <> "=") $searchArray['winsdirection'] = "";
if ($searchArray['lossesdirection'] <> "<=" && $searchArray['lossesdirection'] <> ">=" && $searchArray['lossesdirection'] <> "=") $searchArray['lossesdirection'] = "";
if ($searchArray['ratingdirection'] <> "<=" && $searchArray['ratingdirection'] <> ">=" && $searchArray['ratingdirection'] <> "=") $searchArray['ratingdirection'] = "";
if ($searchArray['streakdirection'] <> "<=" && $searchArray['streakdirection'] <> ">=" && $searchArray['streakdirection'] <> "=") $searchArray['streakdirection'] = "";

setcookie ("playeroptions", base64_encode(serialize($searchArray)), time()+7776000); 

require_once 'autologin.inc.php';
require('top.php');
?>
<h2>Player Search</h2>
<p>You can search for players below.  You may use the options in the header to search for specific criteria.  A maximum of 250 players will be displayed for any search.</p>
<form method="get" action="players.php"> 
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#player").tablesorter({sortList: [[1,0]], widgets: ['zebra'] }); 
    } 
); 
</script>
<table id="player" class="tablesorter">
<thead>
<tr>
<th>&nbsp;</th>
<th>Player</th>
<th>Games</th>
<th>Wins</th>
<th>Losses</th>
<th>Rating</th>
<th>Streak</th>
<th>Country</th>
</tr>
<tr>
<td><input type="submit" value="Search" /></td>
<td><input name="player" type="text" value="<? echo $searchArray['player'] ?>" size="10" /></td>
<td><select name="gamesdirection">
    <option <?php if ($searchArray['gamesdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['gamesdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['gamesdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    <option <?php if ($searchArray['gamesdirection'] == "=") echo "selected='selected'"; ?> value="=">=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['games']; ?>" name="games" size="2" />
</td>
<td><select name="winsdirection">
    <option <?php if ($searchArray['winsdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['winsdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['winsdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    <option <?php if ($searchArray['winsdirection'] == "=") echo "selected='selected'"; ?> value="=">=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['wins']; ?>" name="wins" size="2" />
</td>
<td><select name="lossesdirection">
    <option <?php if ($searchArray['lossesdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['lossesdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['lossesdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    <option <?php if ($searchArray['lossesdirection'] == "=") echo "selected='selected'"; ?> value="=">=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['losses']; ?>" name="losses" size="2" />
</td>
<td><select name="ratingdirection">
    <option <?php if ($searchArray['ratingdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['ratingdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['ratingdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    <option <?php if ($searchArray['ratingdirection'] == "=") echo "selected='selected'"; ?> value="=">=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['rating']; ?>" name="rating" size="2" />
</td>
<td><select name="streakdirection">
    <option <?php if ($searchArray['streakdirection'] == "") echo "selected='selected'"; ?> value="">--</option>
    <option <?php if ($searchArray['streakdirection'] == "<=") echo "selected='selected'"; ?> value="&lt;=">&lt;=</option>
    <option <?php if ($searchArray['streakdirection'] == ">=") echo "selected='selected'"; ?> value="&gt;=">&gt;=</option>
    <option <?php if ($searchArray['streakdirection'] == "=") echo "selected='selected'"; ?> value="=">=</option>
    </select>
    <input type="text" value="<?php echo $searchArray['streak']; ?>" name="streak" size="2" />
</td>
<td><select size="1" name="country" class="text">
    <option selected><?=$searchArray['country']?>
    <option value=""></option>
    <?php echo "$row[country]" ?></option>
    <?php include ("include/countries.inc.php"); ?>
    </select>
</td>
</tr>
</thead>
<tbody>
<?php

// Construct the where clause
$where = "name like '%".$searchArray['player']."%' ";

// Setup ratings in query
if ($searchArray['gamesdirection'] != "" && $searchArray['games'] != "") {
    $where .= " AND games ".$searchArray['gamesdirection']." '".$searchArray['games']."' ";
}
if ($searchArray['winsdirection'] != "" && $searchArray['wins'] != "") {
    $where .= " AND wins ".$searchArray['winsdirection']." '".$searchArray['wins']."' ";
}
if ($searchArray['lossesdirection'] != "" && $searchArray['losses'] != "") {
    $where .= " AND losses ".$searchArray['lossesdirection']." '".$searchArray['losses']."' ";
}
if ($searchArray['ratingdirection'] != "" && $searchArray['rating'] != "") {
    $where .= " AND rating ".$searchArray['ratingdirection']." '".$searchArray['rating']."' ";
}
if ($searchArray['streakdirection'] != "" && $searchArray['streak'] != "") {
    $where .= " AND streak ".$searchArray['streakdirection']." '".$searchArray['streak']."' ";
}
if ($searchArray['country'] != '') {
    $where .= " AND country = '".$searchArray['country']."' ";
}

$sql = "SELECT * FROM $standingscachetable RIGHT JOIN $playerstable USING (name) WHERE confirmation <> 'Deleted' AND ".$where;
$sql .= " ORDER BY name ASC LIMIT 250";

$result=mysql_query($sql,$db);
while ($row = mysql_fetch_array($result)) {
    if ($row["approved"] == "no") {
	$namepage = "<span style='color: #FF0000'>$row[name]</span>";
    } else {
	$namepage = $row[name];
    }

    $games = $row['games'] == "" ? 0 : $row['games'];
    $wins = $row['wins'] == "" ? 0 : $row['wins'];
    $losses = $row ['losses'] == "" ? 0 : $row['losses'];
    $rating = $row['rating'] == "" ? BASE_RATING : $row['rating'];
    $streak = $row['streak'] == "" ? 0 : $row['streak'];

?>
<tr>
<td align="right"><?php echo "<img border='0' height='20px' src='avatars/$row[Avatar].gif' alt='avatar' />"?></td>
<td><?php echo "<a href='profile.php?name=$row[name]'>$namepage</a>"?></td>
<td><?echo $games ?></td>
<td><?echo $wins ?></td>
<td><?echo $losses ?></td>
<td><?echo $rating ?></td>
<td><?echo $streak ?></td>
<td><?echo "<img src='graphics/flags/$row[country].bmp' align='absmiddle' border='1'>"?></td>
</tr>
<?
}
?>
</tbody>
</table>
</form>
<?
require('bottom.php');
?>

