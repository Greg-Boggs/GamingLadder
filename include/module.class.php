<?php
    /*
    *
    * Module: abstract class, represents abstract module.
    * @author Khramkov Ivan.
    * 
    */
    require_once(dirname(__FILE__).'/simple.class.php');
    class Module extends Simple {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name;
		/*
		* Constructor
		*@param object $config
		*@param integer|null $id
		*/
	    function __construct($config, $params = array()) {
		    $table_name = 'module_'.$this->name;
		    parent::__construct($config, $table_name, $params);
		}
		/*
		*@function run_controller
		*@param string $controller_name
		*/
		public function run_controller ($controller_name) {
		    $path = dirname(__FILE__).'/../modules/'.$this->name.'/controllers/'.$controller_name.'_controller.php';
		    if (file_exists($path)) {
			    require_once($path);
				eval('$controller = new '.$controller_name.'_controller($this->get_config());');
				$controller->html->set_template_dir(dirname(__FILE__).'/../modules/'.$this->name.'/templates/');
				$controller->run();
			}
			else {
			    throw new Exception('No controller!');
			}
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
				     '$module = new '.strtoupper(substr($module_name, 0, 1)).substr($module_name, 1).'($config, $params);'
				 );
				 return $module;
			 }
			 else {
			     throw new Exception('No module!');
			 }
		}
	}
?>