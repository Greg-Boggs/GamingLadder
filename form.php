<?
$page = "faq";
require('conf/variables.php');
require('top.php');
?>

<form action="upload.php" method="post" ENCTYPE="multipart/form-data">
File: <input type="file" name="file" size="30"> <input type="submit" value="Upload!">
</form> 


<?php
require('bottom.php');
?>
