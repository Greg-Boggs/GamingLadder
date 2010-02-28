<?php
    /*
	*
         * Simple: simple class, provides database and html interface...
	* @author Khramkov Ivan.
	* 
	*/
	require_once(dirname(__FILE__).'/entity.class.php');
	require_once(dirname(__FILE__).'/html.class.php');
    class Simple extends Entity {
		/*
		* Configuration
		*@var object
		*/
		var $config;
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
	}
?>