<?php
    /*
	*
         * Controler: abstract class, represents module controller.
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/simple.class.php');
    class Controller extends Simple {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name;
		/*
		* Constructor
		*@param object $config
		*/
	    function __construct($config) {
		    parent::__construct($config);
		}
		/*
		*@function display
		*/
		public function display() {
		    echo $this->html->smarty->fetch($this->name.'.tpl');
		}
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    //Nothing to do yet... It's abstract...
		}
	}
?>