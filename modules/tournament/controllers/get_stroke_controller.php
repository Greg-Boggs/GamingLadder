<?php
    /*
	*
         * get_stroke_controller: class shows table of tournament
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class get_stroke_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'get_stroke';
		/*
		*@function run
		*/
		public function run() {
			$tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    $this->error("Unknown tournament!");
			}
			$state = $tournament->get_state();
			if (!$state['value']) {
			    $this->html->assign('table', NULL);
			}
			else {
			    $this->html->assign('tournament', $tournament);
			    $this->html->assign('table', $this->get_module(
				    array('tournament_table', 'tournament'), 
					array('tournament_id', $tournament->get_id()))
				);
			}
			$this->display(true);
		}
	}
?>