<?php
    /*
	*
         * Config: Object, contains modified properties. Now, we can create different configurations, like:
	* $config1 = new Config(); $config2 = new Config($config_source);
	* @author Khramkov Ivan.
	* 
	*/
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
	
	//Function returns array(operation, value of param) in get_smth (set_smth) methods: exmp: get_method(get_one_param) returns array('get', 'one_param')...
    function get_method($method_str) {
        $result = explode('_', $method_str);
	    if (count($result) > 2) {
	        $tmp = $result;
		    array_shift($result);
		    $result = implode('_', $result);
		    $tmp[1] = $result;
		    $result = $tmp;
	    }
	    return $result;
	}
	
?>