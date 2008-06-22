<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<title><?php echo $titlebar; ?></title>
<meta name="keywords" content="wesnoth, league, ladder, elo, open source" />
<script type="text/javascript" src="jquery/jquery-1.2.6.pack.js"></script>
<script type='text/javascript' src='jquery/jquery.autocomplete.js'></script>
<script type="text/javascript" src="jquery/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="jquery/tablesorter/jquery.tablesorter.pager.js"></script>
<link rel="stylesheet" type="text/css" href="css/wesnoth-main.css" />
<link rel="stylesheet" type="text/css" href="css/sorter.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />


<style type='text/css'>
<!--
a 	     { color: #7B0045; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: <?php echo"$fontweight" ?>; text-decoration: none}
a:link       { color: #7B0045; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: <?php echo"$fontweight" ?>; text-decoration: none}
a:visited    { color: #7B0045; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: <?php echo"$fontweight" ?>; text-decoration: none}
a:hover      { color: <?php echo"$color3" ?>; font-family: <?php echo"$font" ?>; font-size: 12 px; text-decoration: none; font-weight: <?php echo"$fontweight" ?>}
.text        { color: <?php echo"$color1" ?>; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: <?php echo"$fontweight" ?>; text-decoration: none}
.textalt     { color: <?php echo"$color2" ?>; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: <?php echo"$fontweight" ?>; text-decoration: none}
.header     { color: <?php echo"$color1" ?>; font-family: <?php echo"$font" ?>; font-size: 12 px; font-weight: bold; text-decoration: none}

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
<?php 
	$starttime = microtime();
	$startarray = explode(" ", $starttime);
	$starttime = $startarray[1] + $startarray[0];
?>
<div id="header">
  <div id="logo">
    <a href="<?php echo $directory;?>"<img alt="Ladder logo" src="graphics/wesnoth-logo.png" /></a>
  </div>
<?php
    require('menu.php');
?>
</div>

<div style="background-color: <?php echo"$color7" ?>; width: 800px; margin-left: auto; margin-right: auto">
