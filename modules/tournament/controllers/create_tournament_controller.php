<?php
    /*
	*
        * create_tournament_controller: class creates a tournament and sends it from sender to reciever...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
	require_once(dirname(__FILE__).'/../../../include/form_validator.class.php');
    class create_tournament_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'create_tournament';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $user = $this->acl->get_user();
			if (!$user || !$user->get_is_admin()) {
			    $this->error('Access denied!');
			}
			$checker = new FormValidator();
			$form = $this->get_request('form');
			$tid = $this->get_request('tid');
			$now = strtotime(date('Y/d/m', time()));
			$tournament = ($tid)? $this->get_module('tournament', array('id', $tid)) : $this->get_module('tournament');
			if ($form) {
			    $this->html->assign('form', $form);
			    $t = $this->get_entity($this->get_config(), 'module_tournament', array('name', $this->get_request('name')));
			    if (!$tid) {
			        $checker->add_checking('name', FormValidator::VAR_REQUIRED, 'Name is required!')->
				              add_checking('date_signup_start', FormValidator::VAR_REQUIRED, 'Signup start date is required!')->
						      add_checking('date_signup_end', FormValidator::VAR_REQUIRED, 'Signup end date is required!')->
						      add_checking('date_play_start', FormValidator::VAR_REQUIRED, 'Play start date is required!')->
						      add_checking('date_play_end', FormValidator::VAR_REQUIRED, 'Play end date is required!')->
				              add_checking(
						          'name', 
							      FormValidator::VAR_CALL_BACK, 
							      'Tournament with the same name exists!', 
							      array('result' => (!$t->get_id()))
						      )->
						      add_checking(
						          'min_participants', 
							      FormValidator::VAR_REQUIRED, 
							      'Min. number of participants is required!'
						      )->
						      add_checking(
						          'max_participants', 
							      FormValidator::VAR_REQUIRED, 'Max. number of participants is required!'
						      )->
						      add_checking(
						          'min_participants', 
							      FormValidator::VAR_CALL_BACK, 
							      'Min. number of participants must be an integer number greater than 1!', 
							      array('result' => (intval($this->get_request('min_participants')) && $this->get_request('min_participants') > 1))
						      )->
						      add_checking(
						          'max_participants', 
							      FormValidator::VAR_CALL_BACK, 
							      'Max. number of participants must be an integer number greater than 2!', 
							      array('result' => (intval($this->get_request('max_participants')) && $this->get_request('max_participants') > 0))
						      )->
						      add_checking(
						          'max_participants', 
							      FormValidator::VAR_CALL_BACK, 
							      'Max. number of participants must be greater or equal min. number of participants!', 
							      array('result' => (intval($this->get_request('max_participants')) >= $this->get_request('min_participants')))
						      )->
						      add_checking(
						          'games_to_play', 
							      FormValidator::VAR_CALL_BACK, 
							      'Number of required games must be an integer number greater than zero!', 
							      array('result' => (intval($this->get_request('games_to_play')) && $this->get_request('games_to_play') > 0)))->
						      add_checking(
						          'date_signup_end', 
							      FormValidator::VAR_CALL_BACK, 
							      'Malformed signup dates: write its number representation look like: '.
							      'day/month/year. Signup start must be earler than signup end!', 
							      array(
							          'result' => (
								          strtotime($this->get_request('date_signup_start')) && 
									      strtotime($this->get_request('date_signup_end')) && 
									      strtotime($this->get_request('date_signup_start')) <= strtotime($this->get_request('date_signup_end'))
								      )
							      )
						      )->
						      add_checking(
						          'date_play_start', 
							      FormValidator::VAR_CALL_BACK, 
							      'Malformed play dates: write its number representation look like: '.
							      'day/month/year. Play start must be earler than play end!', 
							      array(
							          'result' => (
								          strtotime($this->get_request('date_play_start')) && 
									      strtotime($this->get_request('date_play_end')) && 
									      strtotime($this->get_request('date_play_start')) <= strtotime($this->get_request('date_play_end'))
								      )
							      )
						      )->
						      add_checking(
						          'date_signup_end', 
							      FormValidator::VAR_CALL_BACK, 
							      'Signup dates must be now or later!', 
							      array(
							          'result' => (
								          strtotime($this->get_request('date_signup_start')) >= $now && 
									      strtotime($this->get_request('date_signup_end')) >= $now
								      )
							      )
						      )->
						      add_checking(
						          'date_play_start', 
							      FormValidator::VAR_CALL_BACK, 
							      'Play dates must be now or later!', 
							      array(
							          'result' => (
								          strtotime($this->get_request('date_play_start')) >= $now && 
									      strtotime($this->get_request('date_play_end')) >= $now
								      )
							      )
						      )->
						      add_checking(
						          'date_play_start', 
							      FormValidator::VAR_CALL_BACK, 
							      'Play dates must be later than signup dates!', 
							      array('result' => (strtotime($this->get_request('date_signup_end')) <= strtotime($this->get_request('date_play_start'))))
						      );
					if ($this->get_request('type') - 1) {
					    $checker->add_checking(
						    'games_to_play', 
							FormValidator::VAR_CALL_BACK, 
							'Number of required games must be odd in knock out tournament!', 
							array('result' => (intval($this->get_request('games_to_play')) % 2 != 0))
						);
					}
				}
				$checker->add_checking('information', FormValidator::VAR_REQUIRED, 'Information is required!');
				if (!$tid) {
				    $tournament->set_type($this->get_request('type') - 1);
				    $tournament->set_name($this->get_request('name'));
					$tournament->set_sign_up_starts(strtotime($this->get_request('date_signup_start')));
					$tournament->set_sign_up_ends(strtotime($this->get_request('date_signup_end')));
					$tournament->set_play_starts(strtotime($this->get_request('date_play_start')));
					$tournament->set_play_ends(strtotime($this->get_request('date_play_end')));
					$tournament->set_min_participants($this->get_request('min_participants'));
				    $tournament->set_max_participants($this->get_request('max_participants'));
				    $tournament->set_sign_up_starts(time());
				}
				$tournament->set_games_to_play((($this->get_request('games_to_play'))? $this->get_request('games_to_play') : 1));
				$tournament->set_information($this->get_request('information'));
				$tournament->set_rules($this->get_request('rules'));
				$this->html->restore_template($_POST);
			}
			if ($checker->check() && $form) {
			    $tournament->save();
				$db = new DB($this->get_config());
			    $db->delete(
			        $this->get_config()->get_db_prefix().'_tournament_filter_xrel', 
				    new DB_Condition('tournament_id', $tournament->get_id())
			    );
			    $filters = (array)($this->get_request('filters'));
				foreach ($filters as $key => $fid) {
				    $tournament->apply_filter($fid);
				}
				$result = $this->get_entity($this->get_config(), 'tournament_result');
				$result->set_tournament_id($tournament->get_id());
				$result->set_title($this->get_request('winner_title'));
				$result->save();
			    $this->html->assign('created', 1);
			}
			else {
				$this->html->assign('user', $user);
				$this->html->assign('tournament', (($tournament)? $tournament : $this->get_module('tournament')));
				$this->html->assign('winner_title', $this->get_request('winner_title'));
				$this->html->assign('errors', $checker->results);
		    }
			$this->display();
		}
	}
?>