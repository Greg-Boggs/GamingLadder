<?php
    /*
	*
    * User module, represents user...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class User extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'user';
		/*
		* Constructor
		*@param object $config
		*@param integer|null $user_id
		*/
	    function __construct($config, $params = NULL) {
		    Simple::__construct($config, 'players', $params);
		}
	}
?>