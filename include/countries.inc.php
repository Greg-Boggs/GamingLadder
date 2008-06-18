<?php

/*
 * Look through the countries folder for *.bmp files. Print out all the ones we find.
 */
$dh = opendir('graphics/flags');

$countries = array();
while (false !== ($file = readdir($dh))) {
    $filename = substr($file, 0, strrpos($file,'.'));
    $extension = substr($file, strrpos($file,'.'));
    if (strcasecmp($extension, '.bmp') == 0) {
        array_push($countries, $filename);
    }
}

echo "<option>No Country</option>";
sort($countries);
foreach ($countries as $data) {
    if ($data !== "No Country") {
        echo '<option value="'.htmlentities($data).'">'.htmlentities($data)."</option>\n";
    }
}

?>
