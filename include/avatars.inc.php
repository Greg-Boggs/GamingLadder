<?php

/*
 * Look through the avatars folder for *.gif files. Print out all the ones we find.
 */

/**
 *
 * Returns a list of named avatars in an array with title => filename pairs
 *
 * Currently avatars can only be gif files, a small alteration to the code should
 * allow other media types.
 *
 * @param path the path to collect the avatars from, default is avatars
 */
function avatarList($path = 'avatars')
{
	$dh = opendir($path);

	$avatars = array();
	while (false !== ($file = readdir($dh))) {
	    $filename = substr($file, 0, strrpos($file,'.'));
	    $extension = substr($file, strrpos($file,'.'));
	    if (strcasecmp($extension, '.gif') == 0) {
	        $avatars[$filename] = $file;
	    }
	}

	return $avatars;
}
?>
