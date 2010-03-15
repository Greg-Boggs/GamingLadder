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
	    * Returned content
	    *@var string
	    */
		var $content;
		/*
		* Constructor
		*@param object $config
		*/
	    function __construct($config) {
		    parent::__construct($config);
		}
		/*
		*@function display
		*@param boolean $only_current_content
		*/
		public function display($only_current_content = false) {
		    $this->content = $this->html->fetch($this->name.'.tpl');
			//if we don't want to include content to the main page (useful for AJAX requests)...
			if ($only_current_content) {
			    echo $this->content;
			    exit;
			}
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