<?php
    /*
	*
    * view_content_controller: class displays only content of the message
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class view_content_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'view_content';
		/*
		*@function run
		*@param object $user
		*/
		public function run($user) {
		    $user_id = (integer)($this->get_request('user'));
			if ($user_id != $user->get_id()) {
			    $this->error('You have not permission to view this message');
			}
		    $topic_id = (integer)($this->get_request('topic'));
			$message = $this->get_module('message', array('topic_id', $topic_id));
			if (!$message->get_id()) {
			    $this->error("Message doesn't exist");
			}
			$this->html->assign('content', $message->get_content());
			$this->display(true);
		}
	}
?>