<?php
    /*
    *
    * ACL: access control level...
    * @author Khramkov Ivan.
    * 
    */
	require_once(dirname(__FILE__).'/entity.class.php');
    class ACL {
	    /*
		* Configuration
		*@var object
		*/
		private $config;
		/*
		* Current user
		*@var object
		*/
		private $user = NULL;
		/*
		* Constructor
		*@param object $config
		*/
		function __construct($config) {
		    $this->config = $config;
		    $this->user = $this->get_user();
		}
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->user);
			unset($this->config);
		}
        /*
		*@function get_user
		*@return object|null
		*/
		public function get_user() {
		    if (!$this->user && !$this->get_session('user')) {
			    $user = new Entity($this->config, 'players', array('name', $this->get_session('username')));
		 	    $this->user = ($user->get_player_id())? $user : NULL;
			    if ($this->user && !$this->get_session('user')) {
			        $this->set_session('user', serialize($this->user));
			    }
			}
			else {
			    return unserialize($this->get_session('user'));
			}
		}
		/*
		*@function check_access
		*@param array|null $pretend_ids
		*@return object|null
		*/
		public function check_access($pretend_ids = NULL) {
		    if (!$this->get_user()) {
			    return false;
			}
		    if ($this->get_user()->get_is_admin()) {
			    return true;
			}
			$result = true;
			if ($pretend_ids) {
			    foreach($pretend_ids as $key => $id) {
			        $result = ($result || ($this->user && $this->user->get_player_id == $id));
			    }
			}
			return $result;
		}
		/*
		*@function get_session
		*@param string $param_name
		*return variant;
		*/
		public function get_session($param_name) {
		    return $_SESSION[$param_name];
		}
		
		/*
		*@function set_session
		*@param string $param_name
		*@param string $param_value
		*/
		public function set_session($param_name, $param_value) {
		    $_SESSION[$param_name] = $param_value;
		}
	}
?>