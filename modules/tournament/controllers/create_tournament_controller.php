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
			if ($form) {
			    $t = $this->get_entity($this->get_config(), 'module_tournament', array('name', $this->get_request('name')));
			    $checker->add_checking('name', FormValidator::VAR_REQUIRED, 'Name is required!')->
				          add_checking(
						      'name', 
							  FormValidator::VAR_CALL_BACK, 
							  'Tournament with the same name exists!', 
							  array('result' => (!$t->get_id()))
						  )->
				          add_checking('information', FormValidator::VAR_REQUIRED, 'Information is required!')->
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
							  array('result' => (intval($_POST['min_participants']) && $_POST['min_participants'] > 1))
						  )->
						  add_checking(
						      'max_participants', 
							  FormValidator::VAR_CALL_BACK, 
							  'Max. number of participants must be an integer number greater than zero!', 
							  array('result' => (intval($_POST['max_participants']) && $_POST['max_participants'] > 0))
						  )->
						  add_checking(
						      'max_participants', 
							  FormValidator::VAR_CALL_BACK, 
							  'Max. number of participants must be greater than min. number of participants!', 
							  array('result' => (intval($_POST['max_participants']) > $_POST['min_participants']))
						  );
			    $tournament = $this->get_module('tournament');
				$tournament->set_type($this->get_request('type') - 1);
				$tournament->set_name($this->get_request('name'));
				$tournament->set_information($this->get_request('information'));
				$tournament->set_rules($this->get_request('rules'));
				$tournament->set_min_participants($this->get_request('min_participants'));
				$tournament->set_max_participants($this->get_request('max_participants'));
				$tournament->set_sign_up_starts(time());
				$this->html->restore_template($_POST);
			}
			if ($checker->check() && $form) {
			    $tournament->save();
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