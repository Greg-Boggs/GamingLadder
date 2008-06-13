<html>
<!-- v 1.01 -->

<head>
<title><?php echo $titlebar; ?></title>
<meta name="keywords" content="wesnoth, league, ladder, elo, open source">
<script type="text/javascript" src="lib/jquery.js"></script>
<script type='text/javascript' src='lib/jquery.autocomplete.js'></script>
<script type="text/javascript" src="lib/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="lib/jquery.tablesorter.pager.js"></script>
<link rel="stylesheet" type="text/css" href="sorter.css" />
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />


<style type=text/css>
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

<body bgcolor="<? echo"$color7" ?>" topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0">
<?php 
	$starttime = microtime();
	$startarray = explode(" ", $starttime);
	$starttime = $startarray[1] + $startarray[0];
?>
<div align="center">

  <table border="0" cellpadding="2" color="red" cellspacing="0" width="800px" height="100%">
  <div align="center"><a href="<?php echo "$directory"; ?>"><img border="0" align="center" src="graphics/logo.gif"></a></div>
  <td width="100%">
    <?
    require('menu.php');
    ?>
  </td>
  </tr>
  <tr>
    <td width="100%" height="100%" bgcolor="<? echo"$color7" ?>" valign="top">
  <div align="center">
  
  <table border="0" cellpadding="5" cellspacing="0" width="100%">
  <tr>
  <td width="100%" valign="top">
