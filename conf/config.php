<?php
    /*
	*
         * Config: Object, contains modified properties. Now, we can create different configurations, like:
	* $config1 = new Config(); $config2 = new Config($config_source);
	* @author Khramkov Ivan.
	* 
	*/
	include_once(dirname(__FILE__).'/../include/genericfunctions.inc.php');
    class Config {
	    /*
		* Current config source
		*@var string
		*/
	    private $source;
		/*
		* Constructor
		*@param string $source
		*/
		function  __construct($source = 'default_conf.php') {
		    $this->source = $source;
			include_once($source);
		}
		/*
		*@function __call
		*@param string $method
		*@return array|null $params
		*/
		public function __call($method, $params = NULL) {
		    //get or set value from entity..
		    $method = get_method($method);
			switch ($method[0]) {
			    case 'get': return $GLOBALS[$method[1]]; break;
				case 'set': $GLOBALS[$method[1]] = $params[0]; break;
				default: return NULL;
			}
		}
	}
?>