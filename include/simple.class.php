<?php
    /*
    *
    * Simple: simple class, provides database and html interface...
    * @author Khramkov Ivan.
    * 
    */
    require_once(dirname(__FILE__).'/entity.class.php');
    require_once(dirname(__FILE__).'/html.class.php');
	require_once(dirname(__FILE__).'/genericfunctions.inc.php');
    class Simple extends Entity {
		/*
		* Configuration
		*@var object
		*/
		private $config;
		/*
		* HTML object
		*@var object
		*/
		var $html;
		/*
		* Constructor
		*@param object $config
		*/
		function __construct($config, $table_name = NULL, $params = array()) {
		    parent::__construct($config, $table_name, $params);
		    $this->config = $config;
		    $this->html = new HTML($this->config);
		}
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->config);
		    unset($this->html);
			parent::__destruct();
		}
        /*
		*@function error
		*@param string $message
		*@param integer $error_code
		*/
		public function error($message, $error_code = 404) {
		    throw new Exception($message, $error_code);
		}
		/*
		*@function get_config
		*@return object
		*/
		public function get_config() {
		    return $this->config;
		}
		/*
		*@function set_config
		*@param object $config
		*/
		public function set_config($config) {
		    $this->config = $config;
		}
		/*
		*@function get_module
		*@param string $module_name
		*@param array|null $params
		*@param object|null $config
		*/
		public function get_module($module_name, $params = NULL, $config = NULL) {
		     $path = dirname(__FILE__).'/../modules/'.$module_name.'/'.$module_name.'.class.php';
			 if (file_exists($path)) {
			     require_once($path);
				 $config = ($config)? $config : $this->get_config();
				 eval(
				     '$module = new '.first_letter($module_name).'($config, $params);'
				 );
				 return $module;
			 }
			 else {
			     throw new Exception('No module!');
			 }
		}
		
		public function get_request($param_name, $request_method = NULL) {
		    $result = NULL;
		    if ($request_method) {
			    eval('$result = $_'.strtoupper($request_method).'[$param_name];');
			}
			else {
			    $result = (isset($_GET[$param_name]))? $_GET[$param_name] : $_POST[$param_name];
			}
			return $result;
		}
		
		public function get_session($param_name) {
		    return $_SESSION[$param_name];
		}
		
		public function get_user() {
		    return $this->get_module('user', array('name', $this->get_session('username')));
		}
	}
?>