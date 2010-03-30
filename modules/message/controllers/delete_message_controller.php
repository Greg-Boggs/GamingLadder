<?php
    /*
	*
    * delete_message_controller: class deletes message...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class delete_message_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'delete_message';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
			//Define current user...
			$user = $this->acl->get_user();
			if ($user->get_is_admin() && $this->get_request('totally')) {
			    //TODO: delete message from db...
			}
			else {
			    $topics = (array)$this->get_request('messages');
				$box = $this->get_request('box');
				foreach ($topics as $key => $topic_id) {
				    $topic = $this->get_module('topic', array('id', $topic_id));
				    if ($this->acl->check_access(array($topic->get_reciever_id(), $topic->get_sender_id()))) {
					    $topic->delete($box);
			        }
				    else {
				        $this->error('You have not permission to delete this message!');
				    }
				}
			}
			$this->display();
		}
	}
?>