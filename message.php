<?php
    /*
	*
    * Message section
	*@author Khramkov Ivan.
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
	if (isset($_GET['action']) && !empty($_GET['action'])) {//if we get any action name in the GET request...
	    $message->run_controller($_GET['action']);// run "view" message controller (in my point of view, "view" will prints content of the message to the the page...)
	}
	else {//If nothing in the request
	    echo "TODO: default controller...";// Default action...
	}
	echo "<p>Now, tests...<ul>";
	echo "<li> Simple create message, save and delete...";
	unset($message);
	$message = new Message($config);
	$message->set_content('Test content');
	$time = time();
	$message->set_sent_date($time);
	$message->save();
	echo "...message created at ".date("G:i:s", $time)."...<br />";
	$mid = $message->get_id();
	unset($message);
	$message = new Message($config, array('id', $mid));
	echo "Content of message with id = $mid: '<i>".
	    $message->get_content()."</i>'. This message was sent at ".
		date('G:i:s', $message->get_sent_date());
	$message->delete();
	echo "...deleted.</li>";
	echo "<li> Send message from me to me...";
	$message = new Message($config);
	$message->send(1, 1, 'Topic', 'Content');
	echo "Message with id = ".$message->get_id()." was sent with content = '<i>".$message->get_content()."</i>'...";
	echo "</li></ul>";
	$message->delete();
	unset($message);
?>
