<?php
    /*
	*
        * list_tournaments_controller: class displays list of tournaments of differents types and dates...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class list_tournaments_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'list_tournaments';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $items_per_page = 30;
			//Define current user...
			$user = $this->acl->get_user();
			//Get tournaments
			$condition = NULL;
			$init = $this->get_request('p_c_p');
			$init = ($init)? $init : 0;
			$total = $this->get_entities_count($this->get_config(), 'module_tournament', $condition);
			$tournaments = $this->get_modules('tournament', $condition, array('play_starts', 'DESC'), array($init, $items_per_page));
			$url = $_SERVER['REQUEST_URI'];
			$this->html->assign('user', $user);
			$this->html->assign('player', $player);
	        $this->html->assign('tournaments', $tournaments);
			$this->html->assign('total', $total);
			$this->html->assign('url', $url);
			$this->html->assign('items_per_page', $items_per_page);
			$this->display();
		}
	}
?>