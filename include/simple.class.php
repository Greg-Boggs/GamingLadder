<?php
    /*
	*
    * Simple: simple class
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/db.class.php');
    class Simple {
	    /*
		* Database driver
		*@var object
		*/
	    var $db;
		/*
		* Configuration
		*@var object
		*/
		var $config;
		function __construct($config) {
		    $this->db = new DB($config);
		}
		
		function __destruct() {
		    unset($this->db);
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
	    *@function re_config
	    *@param object $new_config
	    */
		public function re_config($new_config) {
	        unset($this->db);
			$this->db = new DB($new_config);
		}
	}
?>