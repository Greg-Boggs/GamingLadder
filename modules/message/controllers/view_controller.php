<?php
    /*
	*
    * view_controller: class displays content of the message
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class view_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'view';
		/*
		*@function run
		*@param object $user
		*/
		public function run() {
		    $user = $this->acl->get_user();
		    $topic_id = (integer)($this->get_request('topic'));
		    $topic = $this->get_module('topic', array('id', $topic_id));
			if (!$topic->get_id()) {
			    $this->error("Topic doesn't exist!");
			}
			if (!$this->acl->check_access(array($topic->get_sender_id(), $topic->get_reciever_id()))) {
			    $this->error('You have not permission to view this message');
			}
			$message = $this->get_module('message', array('topic_id', $topic->get_id()));
			if (!$message->get_id()) {
			    $this->error("Message doesn't exist");
			}
			$this->html->assign('user', $user);
	        $this->html->assign('topic', $topic);
			$this->html->assign('message', $message);
			$this->display();
		}
	}
?>