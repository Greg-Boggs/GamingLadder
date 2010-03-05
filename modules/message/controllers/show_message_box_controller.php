<?php
    /*
	*
    * show_message_box_controller: class displays list of message of inbox or outbox, related to user...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class show_message_box_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'show_message_box';
		/*
		*@function run
		*@param array $params
		*/
		const error_permission = 'You have not permission to access into the message box';
		public function run($params = array()) {
			//Define current user...
			$user = $this->get_user();
			//If user does not exists (maybe, user is guest)...
			if (!$user->get_id()) {
			    throw new Exception(self::error_permission);
			}
			$box = $this->get_request('box');
			$box = ($box)? $box : 'inbox';
			//Get topics...
			$condition = new DB_Condition((($box == 'inbox')? 'reciever_id' : 'sender_id'), $user->get_id());
			$topics = $this->get_entities($this->get_config(), 'module_topic', $condition, array('sent_date', 'DESC'), array(0, 30));
	        $this->html->assign('topics', $topics);
			$this->html->assign('box', $box);
			$this->display();
		}
	}
?>