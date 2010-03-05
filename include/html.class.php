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
		private $smarty;
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
			$this->smarty->register_object('html_entity', $this, array(
			    'redirect'), 
			false);
		}
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->smarty);
		}
		/*
		*@function assign
		*@param string $param_name
		*@param variant|null $param_value
		*/
		public function assign($param_name, $param_value) {
		    $this->smarty->assign($param_name, $param_value);
		}
		/*
		*@function fetch
		*@param string $template_path
		*@return string
		*/
		public function fetch($template_path) {
		    return $this->smarty->fetch($template_path);
		}
		/*
		*@function get_template_dir
		*@return string
		*/
		public function get_template_dir() {
		    return $this->smarty->template_dir;
		}
		/*
		*@function set_template_dir
		*@param string $template_dir
		*/
		public function set_template_dir($template_dir) {
		    $this->smarty->template_dir = $template_dir;
		}
		
		public function restore_template($array) {
		    foreach($array as $param => $value) {
			    $this->smarty->assign($param, $value);
			}
		}
		
		public function redirect($url) {
		    return "<script type = 'text/javascript'>window.location.href = '$url';</script>";
		}
	}
	//TODO:
	//write function when it'll be required...
?>
