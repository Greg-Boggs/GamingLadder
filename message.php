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
	//*************************************************
	//It's for test now... 
	//*************************************************
	$result = '';
	$config = new Config();//Create the config object...
	$actions = array(
	    'create_message' => array('message', 'message'),
		'view' => array('message', 'message'),
		'view_content' => array('message', 'message'),
		'delete_message' => array('message', 'message'),
		'thread' => array('topic', 'topic'),
	    'show_message_box' => array('message_box', 'message'),
		'search_message' => array('message_box', 'message'),
		'get_players' => array('user', 'user')
	);
	$ac_box = $actions[$_GET['action']];
	try {
	    if (isset($ac_box)) {
	        require_once("modules/".$ac_box[1]."/".$ac_box[0].".class.php");
	        eval('$module = new '.first_letter($ac_box[0]).'($config);');
		    $result = $module->run_controller($_GET['action']);// What's the controller returns (HTML, text)...
	    }
	    else {
	        //Smth for default...
		    $result = "Action '<i>".$_GET['action']."</i>' is not specified in the section context...";
	    }
	}
	catch (Exception $e) {
	    $result = $e->getMessage();
	}
	require 'top.php';
?>
<a href = "message.php?action=create_message">Compose message</a>
&nbsp;|&nbsp;
<a href = "message.php?action=show_message_box">Message box</a>
&nbsp;|&nbsp;
<a href = "message.php?action=search_message">Search</a>
<hr />
<?
    echo $result;
    require_once('bottom.php');
?>