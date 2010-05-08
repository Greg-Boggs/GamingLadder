<?php
    /*
	*
    * Tournament module, represents tournament...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournament';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
		
		private function _get_formatted_date($date, $format = ".") {
		    if (!$date) {
			    return '';
			}
		    return ($format == "/")? date('m'.$format.'d'.$format.'Y', $date) : date('d'.$format.'m'.$format.'Y', $date);
		}
		
		public function get_date($period = 'signup_starts', $format = '.') {
		    eval('$date = $this->_get_formatted_date($this->get_'.$period.'(), $format);');
			return $date;
		}
	}
?>