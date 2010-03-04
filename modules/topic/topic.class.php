<?php
    /*
	*
    * User module, represents message topic...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Topic extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'topic';
		/*
		* Constructor
		*@param object $config
		*@param integer|null $user_id
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
	}
?>