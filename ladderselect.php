<?php 
//this file creates a additional menu only with the purpose of accessing different ladders
//reads the $G_CFG_enabled_ladder_list array and will create for each entry a link plus $_GET Parameter
//
?>
<div id="nav">
  <ul>
	
	<li><a>Ladderselection:</a></li>
	<?
	foreach($G_CFG_enabled_ladder_list as $key => $value) { 
	echo "<li><a href=\"index.php?ladder=".$value."\">".$value."</a></li>";
	}
	?>

  </ul>
</div>
<?php
?>