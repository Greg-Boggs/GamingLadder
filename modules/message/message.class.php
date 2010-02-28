<?php
    /*
	*
         * Message module, represents private message...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Message extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'message';
		/*
		* Constructor
		*@param object $config
		*@param integer|null $message_id
		*/
	    function __construct($config, $message_id = NULL) {
		    parent::__construct($config, $message_id);
		}
	}
?>