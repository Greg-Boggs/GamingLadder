<?php
    /*
	*
        * join_controller: class joins player to the tournament
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class join_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'join';
		/*
		*@function run
		*/
		public function run() {
		    $user = $this->acl->get_user();
			if (!$user->get_player_id()) {
			    echo "{'error': 'You shold be registered, before join to the tournament!'}";
				exit;
			}
			$tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    echo "{'error': 'Unknown tournament!'}";
				exit;
			}
			$state = $tournament->get_state();
			if ($state['value']) {
			    echo "{'error': 'Sign up date was expire, so you are not be able to join!'}";
				exit;
			}
			if ($tournament->is_user_joined($user->get_player_id())) {
			    echo "{'error': 'You are already joined to this tournament'}";
				exit;
			}
			if ($tournament->get_joined_participants() == $tournament->get_max_participants()) {
			    echo "{'error': 'Sorry, but number of max. participants is reached, so you are not able to join!'}";
				exit;
			}
			if (!$tournament->run_filters($user->get_name())) {
			    echo "{'error': 'Sorry, but You do not satisfy the conditions of the tournament!'}";
				exit;
			}
			$tournament->join($user->get_player_id());
			if ($tournament->get_joined_participants() == $tournament->get_max_participants()) {
			    $tournament->set_sign_up_ends(time());
				$tournament->set_play_starts(time() + 33);
				$tournament->save();
				$tournament->build_table();
				$tournament->send_notification('Tournament is started!', 'tournament_notification_started');
			}
			echo "{'success': 1}";
			exit;
		}
	}
?>