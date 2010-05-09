<?php
    /*
	*
         * view_controller: class displays information about tournament
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class view_tournament_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'view_tournament';
		/*
		*@function run
		*@param object $user
		*/
		public function run() {
		    $user = $this->acl->get_user();
			$tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    $this->error('Unknown tournament!');
			}
			$this->html->assign('user', $user);
	        $this->html->assign('tournament', $tournament);
			$state = $tournament->get_state();
			$this->html->assign('state', $state['title']);
			$this->display();
		}
	}
?>