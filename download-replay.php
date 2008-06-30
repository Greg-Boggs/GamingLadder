<?php
require 'conf/variables.php';

$sql = "SELECT CONCAT(DATE_FORMAT(reported_on, '%Y%m%d%h%i%s'),'_',winner,'_vs_',loser,'_wesnoth-ladder.gz') as filename, replay from $gamestable WHERE reported_on = '".$_GET['reported_on']."'";

$result = mysql_query($sql, $db);

if (mysql_num_rows($result) == 1) {
    $row = mysql_fetch_array($result);
    header('Cache-control: public');
    header('Content-Type: application/x-gzip'); 
    header('Content-Length: '.strlen($row['replay']));
    header('Content-Disposition: inline; filename="'.$row['filename'].'"');
    echo $row['replay'];
    exit;
} else {
   require 'top.php';
?>
   <h2>Download Failed</h2>
   <p>
   We were unable to find the requested replay.
   </p>
<?php
   require 'bottom.php';
}
?>
