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
		
		public function run_controller($controller_name, $topic_id) {
		    $user = $this->get_user();
			if (!$user->get_id()) {
			    $this->error('You have not permission to access to the message service');
			}
			$params = array();
			$params['id'] = $topic_id;
			$params['user'] = $user;
			return parent::run_controller($controller_name, $params);
		}
		
		public function get_sender() {
		    return $this->get_module('user', array('player_id', $this->get_sender_id()));
		}
		
		public function get_reciever() {
		    return $this->get_module('user', array('player_id', $this->get_reciever_id()));
		}
		
		public function get_sent_date() {
		    return date('d.m.Y H:i:s', parent::__call('get_sent_date'));
		}
		
		public function get_read_date() {
		    return date('d.m.Y H:i:s', parent::__call('get_read_date'));
		}
	}
?>