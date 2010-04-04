<?php
    /*
    *
    * FormValidator: class validates data from form. It submits, if everythin is right, or return back, if something is wrong...
    * @author Khramkov Ivan.
    * 
    */
    class FormValidator {
	    const VAR_NUMBER = 'number';
	    const VAR_INTEGER = 'integer';
		const VAR_NUMBER_CONDITION = 'number_condition';
		const VAR_STRING = 'string';
		const VAR_STRING_LATIN = 'string_latin';
		const VAR_STRING_LENGTH = 'string_length';
		const VAR_STRING_MAX_LENGTH = 'string_max_length';
		const VAR_STRING_MIN_LENGTH = 'string_min_length';
		const VAR_REQUIRED = 'required';
		const VAR_CALL_BACK = 'call_back';
		const METHOD_POST = 'POST';
		const METHOD_GET = 'GET';
		/*
		* Array of items. Every item is an array of 3 elements: 1st is array (1st item is the value of checked form element, 
		* 2nd element is the array of optional parameters), 2nd is the message, will be shown on error, 
		* 3rd is the name of checked form element. 
		*@var array
		*/
	    private $suspect;
		/*
		* Request method: GET or POST...
		*@var string
		*/
		private $method;
		/*
		* Results of checking... Array of items. Evevery items is an array of 3 elements: 1st is the name of checked form element,
		* 2nd is result of checking: 1 if no error, 0 if error, 3rd is an error message (if error is)
		*@var array
		*/
		public $results;
		/*
		* Constructor
		*@param string $method
		*/
	    function __construct($method = FormValidator::METHOD_POST) {
		    $this->suspect = array();
			$this->method = $method;
			$this->results = array();
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_number($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (intval($params['suspect']) || floatval($params['suspect'])), 
				'onerror' => $onerror
	        );
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_integer($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (intval($params['suspect'])), 
				'onerror' => $onerror
	        );
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_number_condition($params, $onerror, $suspect) {
		    $result = 0;
		    if (isset($params['suspect'])) {
                eval('$result = ('.$params['suspect'].$params['params']['operator'].$params['params']['value'].');');
			}
		    return array(
			    'suspect' => $suspect,
			    'result' => $result, 
				'onerror' => $onerror
			);
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_string($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (is_string($params['suspect'])), 
				'onerror' => $onerror
			);
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_string_latin($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (preg_match("/^[a-zA-Z0-9_ ]+$/", $params['suspect']) && is_string($params['suspect'])), 
				'onerror' => $onerror
			);
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_string_length($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (strlen($params['suspect']) == $params['params']['max_length']), 
				'onerror' => $onerror
			);
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_string_max_lengt($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (strlen($params['suspect']) <= $params['params']['max_length']),
				'onerror' => $onerror
		    );
		}
		/*
		*@function check_number
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_string_min_length($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (strlen($params['suspect']) >= $params['params']['max_length']),
				'onerror' => $onerror
		    );
		}
		/*
		*@function check_required
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_required($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => (isset($params['suspect']) && !empty($params['suspect'])), 
				'onerror' => $onerror
			);
		}
		/*
		*@function call_back
		*@param array $params
		*@param string $onerror
		*@param string $suspect
		*/
		private function check_call_back($params, $onerror, $suspect) {
		    return array(
			    'suspect' => $suspect,
			    'result' => ($params['params']['result']), 
				'onerror' => $onerror
			);
		}
		/*
		*@function add_checking
		*@param string $suspect
		*@param string $variant
		*@param string $onerror
		*@param array $params
		*@description Check form element, and insert result to $this->suspect.
		*/
		public function add_checking($suspect, $variant, $onerror, $params = array()) {
		    $function_name = "check_$variant";
			if (method_exists($this, $function_name)) {
			    eval('$this->suspect[] = $this->'.$function_name.
				     '(array("suspect" => $_'.$this->method.'["'.$suspect.'"], "params" => $params), $onerror, $suspect);');
			}
		    return $this;
		}
		/*
		*@function check
		*@return integer
		*/
	    public function check() { 
		    $result = 1;
		    foreach ($this->suspect as $suspect) {
			    $result *= $suspect['result'];
				$this->results[$suspect['suspect']] = ($result)? "" : $suspect['onerror'];
				if (!$result) {
				    break;
				}
			}
			return $result;
		}
	}
?>