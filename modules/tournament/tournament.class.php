<?php
    /*
	*
    * Tournament module, represents tournament...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournamnet';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
	}
?>