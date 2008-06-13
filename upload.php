<?php
$page = "upload";
require('conf/variables.php');
require('top.php');
?>

<?php

// ==============

// Configuration

// ==============

$uploaddir = "uploads"; 

// Where you want the files to upload to 

//Important: Make sure this folders permissions is 0777!

$allowed_ext = "jpg, gif, png, pdf, bmp"; 

// These are the allowed extensions of the files that are uploaded

$max_size = "5000000"; 

// 50000 is the same as 50kb

$max_height = "100"; 

// This is in pixels - Leave this field empty if you don't want to upload images

$max_width = "100"; 

// This is in pixels - Leave this field empty if you don't want to upload images



// Check Entension

$extension = pathinfo($_FILES['file']['name']);

$extension = $extension[extension];

$allowed_paths = explode(", ", $allowed_ext);

for($i = 0; $i < count($allowed_paths); $i++) {

if ($allowed_paths[$i] == "$extension") {

$ok = "1";

}

}



// Check File Size

if ($ok == "1") {

if($_FILES['file']['size'] > $max_size)

{

print "File size is too big!";

exit;

}



/* Check Height & Width

if ($max_width && $max_height) {

list($width, $height, $type, $w) = getimagesize($_FILES['file']['tmp_name']);

if($width > $max_width || $height > $max_height)

{

print "File height and/or width are too big!";

exit;

}

}

*/

// The Upload Part

if(is_uploaded_file($_FILES['file']['tmp_name']))

{

move_uploaded_file($_FILES['file']['tmp_name'],$uploaddir.'/'.$_FILES['file']['name']);

}

print "Your file has been uploaded successfully! Yay!";

} else {

print "Incorrect file extension!";

}

?>


<?php
require('bottom.php');
?>
