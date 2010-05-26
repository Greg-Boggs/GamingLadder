<?php
    /*
	*
        * list_filters_controller: class displays list of filters...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class list_filters_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'list_filters';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
			//Get filters
			if (!$this->acl->get_user()->get_is_admin()) {
			    $this->error('Access denied!');
			}
			$filters = $this->get_modules(array('tournament_filter', 'tournament'), NULL, array('field', 'DESC'));
	        $this->html->assign('filters', $filters);
			$this->html->assign('tid', $this->get_request('tid'));
			$this->display(true);
		}
	}
?>