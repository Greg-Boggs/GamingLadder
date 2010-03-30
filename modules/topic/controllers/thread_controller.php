<?php
    /*
	*
    * thread_controller: class displays dialog by specified topic
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class thread_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'thread';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = 0) {
		    $user = $this->acl->get_user();
			$how_topic_title = false;//if we view thred by one topic, then we don't need to view topic title in each message...
			if (!is_array($params)) {
		        $topic = $this->get_module('topic', array('id', $params));
			    if (!$topic->get_id()) {
			        $this->error("Topic doesn't exist!");
			    }
			    $condition = new DB_Condition('id', $params, '!=', array('AND', false));
			    $condition->add_cond('topic', $topic->get_topic(), '=', array('AND', true))->
			                add_cond('sender_id', $user->get_player_id(), '=', array('OR', false))->
			                add_cond('reciever_id', $user->get_player_id());
			    $topics = $this->get_modules('topic', $condition, array('sent_date', 'DESC'));
			}
			else {
			    $topics = $params;
				$show_topic_title = true;
			}
			$this->html->assign('user', $user);
			$this->html->assign('topics', $topics);
			$this->html->assign('show_topic_title', $show_topic_title);
			$this->display();
		}
	}
?>