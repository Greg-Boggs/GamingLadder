<?php
    /*
	*
         * Tournament section
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
	    'create_tournament' => array('tournament', 'tournament'),
		'list_tournaments' => array('tournament', 'tournament'),
		'join' => array('tournament', 'tournament'),
		'view_tournament' => array('tournament', 'tournament'),
		'get_joined_players' => array('tournament', 'tournament'),
		'get_stroke' => array('tournament_table', 'tournament')
	);
	$_GET['action'] = (isset($_GET['action']))? $_GET['action'] : 'list_tournaments';
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
    echo $result;
    require_once('bottom.php');
?>