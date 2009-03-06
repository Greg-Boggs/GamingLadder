<?php
require 'conf/variables.php';

$sql = "SELECT replay_filename, winner, loser from $gamestable WHERE reported_on = '".$_GET['reported_on']."'";

$result = mysql_query($sql, $db);

if (mysql_num_rows($result) == 1) {
    $row = mysql_fetch_array($result);
		$filename = $row['replay_filename'];
		$filenameuser = '';
		$filenamestrict = substr($filename,0,strrpos($filename,'.'));
		$fileextension = substr($filename,strrpos($filename,'.'));
		if ($row['winner'] > $row['loser'])
			$filenameuser = $filenamestrict."_".$row['winner']."_vs_".$row['loser']."_wesnoth-ladder".$fileextension;
		else
			$filenameuser = $filenamestrict."_".$row['loser']."_vs_".$row['winner']."_wesnoth-ladder".$fileextension;
		
    mysql_query("UPDATE $gamestable set replay_downloads = replay_downloads + 1 WHERE reported_on = '".$_GET['reported_on']."'", $db);
    header('Cache-control: public');
    header('Content-Type: application/x-gzip'); 
    header('Content-Length: '.filesize($path_file_replay.$filename));		
    //header('Content-Disposition: inline; filename="'.$filename.'"');
		if (preg_match('/MSIE 5.5/', $_ENV['HTTP_USER_AGENT']) || preg_match('/MSIE 6.0/', $_ENV['HTTP_USER_AGENT'])) {
        header('Content-Disposition: filename="'.$filenameuser.'"');
    }
    else {
        header('Content-Disposition: attachment; filename="'.$filenameuser.'"');
    }
    header('Content-Transfer-Encoding: binary');
     // Output file
    readfile($path_file_replay.$filename);		
    //exit;
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
