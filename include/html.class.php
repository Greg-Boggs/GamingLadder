<?php
    /*
	*
        * HTML: Manage templates and HTML representation of data
	* @author Khramkov Ivan.
	* 
	*/
    class HTML {
	    /*
		* Smarty engine
		*@var object
		*/
		var $smarty;
		/*
		* Constructor
		*@param object $config
		*/
		function __construct($config) {
		    require_once(dirname(__FILE__).'/SMARTY/libs/Smarty.class.php');
		    $this->smarty = new Smarty();
		    $this->smarty->template_dir = $config->smarty_templates_path;
		    $this->smarty->compile_dir = $config->smarty_templates_c_path;
            $this->smarty->cache_dir = $config->smarty_cache_path;
		}
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->smarty);
		}
		
	}
	//TODO:
	//write function when it'll be required...
?>
