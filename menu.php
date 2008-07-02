<?php 
// All pages that include the menu should have started the session is they want to use logged in information

if ($maintenanceMode <> true) {
?>
<div id="nav">
  <ul>
	<?php if (!isset($_SESSION['username'])) { ?>
	<li><a href="join.php">Join</a></li>
	<?php } ?>
	<li><a href="report.php">Report</a></li>
	<li><a href="ladder.php">Ladder</a></li>
	<li><a href="players.php?startplayers=0&amp;finishplayers=<?php echo $numplayerspage; ?>">Players</a></li>

	<li><a href="gamehistory.php?startplayed=0&amp;finishplayed=<?php echo $numgamespage; ?>">Game History</a></li>
	<?php if (isset($_SESSION['username'])) { ?>
	<li><a href="ladder.php?personalladder=<?php echo urlencode($_SESSION['username']) ?>">My Ladder</a></li>
	<?php } ?>

	<?php if (isset($_SESSION['username'])) { 
          echo "<li><a href=\"profile.php?name=".$_SESSION['username']."\">".$_SESSION['username']."</a></li>";
          }
    ?>
	<li><a href="faq.php">FAQ</a></li>
	</ul>
</div>
<?php
 } else {
?>
<div id="nav"></div>
<?php
}
?>
