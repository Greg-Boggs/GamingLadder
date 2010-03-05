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
            $user = $this->get_user();
			$checker = new FormValidator();
			$form = $this->get_request('form');
			if ($form) {
			    $checker->add_checking('topic', FormValidator::VAR_REQUIRED, 'Topic is required!')->
				          add_checking('reciever', FormValidator::VAR_REQUIRED, 'Choose a reciever!')->
            			  add_checking('content', FormValidator::VAR_REQUIRED, 'Content is required!')->
				          add_checking('topic', FormValidator::VAR_STRING_LATIN, 'Unsupported symbols in topic!')->
			              add_checking('topic', FormValidator::VAR_STRING_MAX_LENGTH, array('max_length' => 64), 
					                            'Max topic length is 64 symbols!');
		        $message = $this->get_module('message');
			    $message->set_content($this->get_request('content'));
				$topic = $this->get_request('topic');
				$reciever = $this->get_request('reciever');
				$this->html->restore_template($_POST);
			}
			if ($checker->check() && $form) {
			    $this->html->assign('sent', 1);
				$message->send($user->get_id(), $reciever, $topic);
			}
			else {
			    $this->html->assign('users', $this->get_entities($this->get_config(), 'players'));
		        $this->html->assign('message', (($message)? $message : $this->get_module('message')));
				$this->html->assign('topic', (($topic)? $topic : ''));
				$this->html->assign('reciever', (($reciever)? $reciever : 0));
				$this->html->assign('errors', $checker->results);
		    }
			$this->display();
		}
	}
?>