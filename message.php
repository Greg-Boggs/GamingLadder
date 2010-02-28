<?php
    /*
	*
         * Message section
	* @author Khramkov Ivan.
	* 
	*/
    session_start();
	require 'conf/variables.php';
	require 'conf/config.php';
	require_once 'autologin.inc.php';
	require 'top.php';
	require_once('modules/message/message.class.php');
	//It's for test now... 
	$config = new Config();//Create the config object...
	$message = new Message($config);// Create new message object... This object will represents "empty" message (ss. this message is not in database)
	if (isset($_GET['action'])) {//if we get any action name in the GET request...
	    $message->run_controller($_GET['action']);// run "view" message controller (in my point of view, "view" will prints content of the message to the the page...)
	}
	else {//If nothing in the request
	    echo "TODO: default controller...";// Default action...
	}
?>
