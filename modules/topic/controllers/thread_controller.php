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
		public function run($params) {
		    $topic = $this->get_module('topic', array('id', $params['id']));
			if (!$topic->get_id()) {
			    $this->error("Topic doesn't exist!");
			}
			$condition = new DB_Condition('id', $params['id'], '!=', array('AND', false));
			$condition->add_cond('topic', $topic->get_topic(), '=', array('AND', true))->
			            add_cond('sender_id', $params['user']->get_id(), '=', array('OR', false))->
			            add_cond('reciever_id', $params['user']->get_id());
			$topics = $this->get_modules('topic', $condition, array('sent_date', 'DESC'));
			$this->html->assign('user', $params['user']);
			$this->html->assign('topics', $topics);
			$this->display();
		}
	}
?>