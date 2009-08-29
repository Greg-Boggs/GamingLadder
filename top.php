<?php	
    // If we didn't receive the variable $GLOBALS['prefix'], set it to ""
    if (!isset($GLOBALS['prefix'])) {
        $GLOBALS['prefix'] = "";
    }

    // We need to detect the filename as maintenance.php includes this file.
    if ($maintenanceMode == true && basename($_SERVER['SCRIPT_FILENAME']) != 'maintenance.php') {
        // If the site is down for maintenance, redirect to the maintenance page
        header ("Location: maintenance.php");
        exit;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<title><?php echo $titlebar; ?></title>
<meta name="keywords" content="wesnoth, league, ladder, elo, open source, gaming" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<script type="text/javascript" src="<?php echo $GLOBALS['prefix'] ?>jquery/jquery-1.2.6.pack.js"></script>
<script type='text/javascript' src='<?php echo $GLOBALS['prefix'] ?>jquery/jquery.autocomplete.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['prefix'] ?>jquery/jquery.cookie.js'></script>
<script type="text/javascript" src="<?php echo $GLOBALS['prefix'] ?>jquery/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['prefix'] ?>jquery/tablesorter/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['prefix'] ?>jquery/tooltip.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['prefix'] ?>css/wesnoth-main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['prefix'] ?>css/sorter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['prefix'] ?>css/jquery.autocomplete.css" />


<style type='text/css'>
<!--

.copyleft {
color: #000000;
font-family: "san serif", verdana, arial;
font-size: 10px;

}


.smallinfo {
color: #000000;
font-family: "san serif", verdana, arial;
font-size: 12 px;

}
-->
</style>
</head>

<body>
<div id="toolTip"> </div>

<?php 
    $starttime = microtime();
    $startarray = explode(" ", $starttime);
    $starttime = $startarray[1] + $startarray[0];
?>
<div id="header">
  <div id="logo">
    <a href="<?php echo $directory;?>"><img alt="Ladder logo" src="<?php echo $GLOBALS['prefix'] ?>graphics/wesnoth-logo.png" /></a>
  </div>
<?php
    require('menu.php');
	
?>
</div>

<div style="width: 900px; margin-left: auto; margin-right: auto">
<?php
// If it's an admin that's impersonating a player we need to always display the warning message
if (isset($_SESSION['real-username'])) {
    if ($_SESSION['real-username'] != $_SESSION['username']) {
        
		echo "<table width='100%' align='center'><tr bgcolor='#a1bc85'><td><p align='center'>Admin. <b>".$_SESSION['real-username']."</b> is impersonating <b>".$_SESSION['username']."</b></p></td></tr></table>";
    }
}

?>
