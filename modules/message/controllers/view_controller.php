<?php
    /*
	*
         * view_controller: class displays content of the message
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class view_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'view';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
	        $this->html->smarty->assign('content', 'TODO: a lot of...');
			$this->display();
		}
	}
?>