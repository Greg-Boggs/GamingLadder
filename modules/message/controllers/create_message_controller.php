<?php
    /*
	*
    * create_message_controller: class creates a message and sends it from sender to reciever...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
	require_once(dirname(__FILE__).'/../../../include/form_validator.class.php');
    class create_message_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'create_message';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $user = $this->acl->get_user();
			$checker = new FormValidator();
			$form = $this->get_request('form');
			$reciever_name = $this->get_request('reciever');
			$reciever = $this->get_entity($this->get_config(), 'players', array('name', $reciever_name));
			if ($form) {
			    $checker->add_checking('topic', FormValidator::VAR_REQUIRED, 'Topic is required!')->
				          add_checking('reciever', FormValidator::VAR_REQUIRED, 'Choose a reciever!')->
						  add_checking('reciever', FormValidator::VAR_CALL_BACK, 'Player with name "'.$reciever_name.'" does not exists!', array('result' => $reciever->get_player_id()))->
            			  add_checking('content', FormValidator::VAR_REQUIRED, 'Content is required!')->
				          add_checking('topic', FormValidator::VAR_STRING_LATIN, 'Unsupported symbols in topic!')->
			              add_checking('topic', FormValidator::VAR_STRING_MAX_LENGTH, 'Max topic length is 64 symbols!', array('max_length' => 64));
		        $message = $this->get_module('message');
			    $message->set_content($this->get_request('content'));
				$topic = $this->get_request('topic');
				$this->html->restore_template($_POST);
			}
			if ($checker->check() && $form) {
			    $this->html->assign('sent', 1);
				$message->send($user->get_player_id(), $reciever->get_player_id(), $topic);
			}
			else {
			    $db = new DB($this->get_config());
			    $this->html->assign('users', $db->select_pairs($this->get_config()->db_prefix.'_players', 'player_id'));
		        $this->html->assign('message', (($message)? $message : $this->get_module('message')));
				$this->html->assign('topic', (($topic)? $topic : ''));
				$this->html->assign('reciever', $reciever);
				$this->html->assign('errors', $checker->results);
		    }
			$this->display();
		}
	}
?>