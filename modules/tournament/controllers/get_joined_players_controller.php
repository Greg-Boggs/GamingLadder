<?php
    /*
	*
        * get_joined_players_controller: class returns list of joined players for the current tournament
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class get_joined_players_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'get_joined_players';
		/*
		*@function run
		*/
		public function run() {
			$tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    echo "{'error': 'Unknown tournament!'}";
				exit;
			}
            $this->html->assign('players', $tournament->get_players());			
			$this->display(true);
		}
	}
?>