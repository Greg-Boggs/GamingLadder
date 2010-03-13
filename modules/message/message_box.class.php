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
		
		public function run_controller($controller_name) {
		    $user = $this->get_user();
			if (!$user->get_id()) {
			    $this->error('You have not permission to access to the message service');
			}
			parent::run_controller($controller_name, $user);
		}
	}
?>