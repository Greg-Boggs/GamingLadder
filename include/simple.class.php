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
			date_default_timezone_set($config->ladder_timezone);
		    $this->html = new HTML($this->config);
			$this->html->register_object('application', $this, array('load_module'));
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
		*@return object
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
		/*
		*@function get_modules
		*@param string $module_name
		*@param array|null $params
		*@param array|null $order
		*@param array|null $limit
		*@param object|null $config
		*@return object
		*/
		public function get_modules($module_name, $params = array(), $order = NULL, $limit = NULL, $config = NULL) {
	        $config = ($config)? $config : $this->get_config();
		    $entities = $this->get_entities($config, 'module_'.$module_name, $params, $order, $limit);
			$result = array();
		    $path = dirname(__FILE__).'/../modules/'.$module_name.'/'.$module_name.'.class.php';
			if (file_exists($path)) {
			    require_once($path);
				$class_name = first_letter($module_name);
				for ($i = 0; $i < count($entities); $i ++) {
				    eval('$result[$i] = new '.$class_name.'($config);');
				    $result[$i]->set_properties($entities[$i]->get_properties());
				}
				return $result;
			 }
			 else {
			     throw new Exception('No module!');
			 }
		}
		/*
		*@function load_module
		*@param string $module_name
		*@param string $module_action
		*@param variant|null $param
		*return string;
		*/
		public function load_module ($module_name, $module_action, $param = NULL) {
		    $module = $this->get_module($module_name);
			return $module->run_controller($module_action, $param);
		}
		/*
		*@function get_request
		*@param string $param_name
		*@param string|null $request_method
		*return string;
		*/
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
		/*
		*@function get_session
		*@param string $param_name
		*return variant;
		*/
		public function get_session($param_name) {
		    return $_SESSION[$param_name];
		}
		/*
		*@function get_user
		*return object;
		*/
		public function get_user() {
		    return $this->get_module('user', array('name', $this->get_session('username')));
		}
	}
?>