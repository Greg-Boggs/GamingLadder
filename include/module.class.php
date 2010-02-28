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
	    function __construct($config, $id = NULL) {
		    $table_name = 'module_'.$this->name;
			$params = ($id)? array('id', $id) : array();
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
				eval('$controller = new '.$controller_name.'_controller($this->config);');
				$controller->html->smarty->template_dir = dirname(__FILE__).'/../modules/'.$this->name.'/templates/';
				$controller->run();
			}
			else {
			    throw new Exception('No controller!');
			}
		}
	}
?>