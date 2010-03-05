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
?>
    <a href = "message.php?action=create_message">Compose message</a>
    &nbsp;|&nbsp;
    <a href = "message.php?action=show_message_box">Message box</a>
    <hr />
<?php
	//It's for test now... 
	$config = new Config();//Create the config object...
	$actions = array(
	    'create_message' => 'message',
	    'show_message_box' => 'message_box'
	);
	$module_name = $actions[$_GET['action']];
	try {
	    if (isset($module_name)) {
	        require_once("modules/message/$module_name.class.php");
	        eval('$module = new '.first_letter($module_name).'($config);');
		    $module->run_controller($_GET['action']);
	    }
	    else {
	        //Smth for default...
		    echo "Action '<i>".$_GET['action']."</i>' is not specified in the section context...";
	    }
	}
	catch (Exception $e) {
	    echo $e->getMessage();
	}
?>
