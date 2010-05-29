<?php 
// All pages that include the menu should have started the session is they want to use logged in information
if ($maintenanceMode <> true) {

/*
Menu for teamladder
*/
 if ($CFG_teamladder == true){?>
  <div id="nav">
    <ul>
	  <?php if (isset($_SESSION['username'])) { ?>
	  <li><a href="team_create.php">CreateTeam</a></li>
	  <?php } ?>
	  <li><a href="ladder.php">Ladder</a></li>
	  <li><a href="team_report.php">Report</a></li>
	  <li><a href="team_profile.php">Profile</a></li>
    </ul>

  <?php 
  //next lines create an additional menu with the only purpose of accessing different ladders
  //checks if multiladder is enabled and then reads the $G_CFG_enabled_ladder_list array and will create for each entry a link plus $_GET Parameter
  //
  if  ($G_CFG_multiladder == true){?>
      <br></br>
      <ul style="font-size: x-small;">
	  <li><b>Ladderselection:</b></li>
	  <?
	  foreach($G_CFG_enabled_ladder_list as $key => $value) { 
	  echo "<li><a href=\"index.php?ladder=".$value."\">".$value."</a></li>";
	  }
	  ?>
      </ul>
  <?php } ?>

  </div> <?
  }

else{

/*
Menu for normal ladder
*/

?>
<div id="nav">
  <ul>
	<?php if (!isset($_SESSION['username'])) { ?>
	<li><a href="join.php">Join</a></li>
	<?php } ?>
	<li><a href="report.php">Report</a></li>
	<li><a href="ladder.php">Ladder</a></li>
	<li><a href="gadder.php">Classes</a></li>
	<li><a href="players.php">Players</a></li>

	<li><a href="gamehistory.php">Game History</a></li>
	<?php if (isset($_SESSION['username'])) { ?>
	<li><a href="ladder.php?personalladder=<?php echo urlencode($_SESSION['username']) ?>">My Ladder</a></li>
	<?php } ?>

	<?php if (isset($_SESSION['username'])) { 
          echo "<li><a href=\"profile.php?name=".$_SESSION['username']."\">".$_SESSION['username']."</a></li>";
          }
    ?>
	<li><a href="faq.php">FAQ</a></li>
	</ul>

<?php 
//next lines create an additional menu with the only purpose of accessing different ladders
//checks if multiladder is enabled and then reads the $G_CFG_enabled_ladder_list array and will create for each entry a link plus $_GET Parameter
//
if  ($G_CFG_multiladder == true){?>
    <br></br>
    <ul style="font-size: x-small;">
	<li><b>Ladderselection:</b></li>
	<?
	foreach($G_CFG_enabled_ladder_list as $key => $value) { 
	echo "<li><a href=\"index.php?ladder=".$value."\">".$value."</a></li>";
	}
	?>
    </ul>
<?php
}
?>

</div>
<?php
} } else {
?>
<div id="nav"></div>

<?php
}
?>


