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
		
		public function run_controller($controller_name) {
		    $user = $this->get_user();
			if (!$user->get_id()) {
			    $this->error('You have not permission to access to the message service');
			}
			parent::run_controller($controller_name, $user);
		}
		
		public function get_sender() {
		    return $this->get_module('user', array('player_id', $this->get_sender_id()));
		}
		
		public function get_reciever() {
		    return $this->get_module('user', array('player_id', $this->get_reciever_id()));
		}
	}
?>