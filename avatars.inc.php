<?php

/*
 * Look through the avatars folder for *.gif files. Print out all the ones we find.
 */
$dh = opendir('avatars');

$avatars = array();
while (false !== ($file = readdir($dh))) {
    $filename = substr($file, 0, strrpos($file,'.'));
    $extension = substr($file, strrpos($file,'.'));
    if (strcasecmp($extension, '.gif') == 0) {
        array_push($avatars, $filename);
    }
}

echo "<option>No avatar</option>";
sort($avatars);
foreach ($avatars as $data) {
    if ($data !== "No avatar") {
        echo '<option value="'.htmlentities($data).'">'.htmlentities($data)."</option>\n";
    }
}
?>
