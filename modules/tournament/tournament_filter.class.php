<?php
    /*
	*
         * Tournament_filter module, represents tournament's filters...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament_filter extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournament_filter';
		/*
		* Section of the module
		*@var string
		*/
	    protected $section = 'tournament';
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