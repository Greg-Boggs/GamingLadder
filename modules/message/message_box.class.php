<?php
    /*
	*
    * Message box module, represents private message box...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Message_box extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'message';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
		/*
		*@function run_controller
		*@param string $controller_name
		*@return string
		*/
		public function run_controller($controller_name) {
			if (!$this->acl->check_access()) {
			    $this->error('You have not permission to access to the message service');
			}
			return parent::run_controller($controller_name);
		}
	}
?>