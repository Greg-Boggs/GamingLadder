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
		    $items_per_page = 10;
		    $user = $this->acl->get_user();
			$show_topic_title = false;//if we view thred by one topic, then we don't need to view topic title in each message...
			if (!is_array($params)) {
		        $topic = $this->get_module('topic', array('id', $params));
			    if (!$topic->get_id()) {
			        $this->error("Topic doesn't exist!");
			    }
				$condition = new DB_Condition_List(array(
				    new DB_Condition('sender_id', $user->get_player_id()),
					'OR',
					new DB_Condition('reciever_id', $user->get_player_id())
				));
				$condition = new DB_Condition_List(array(
				    $condition,
					'AND',
					new DB_Condition('topic', $topic->get_topic()),
					'AND',
					new DB_Condition('id', $params, '!=')
				));
				$init = $this->get_request('p_c_p');
				$init = ($init)? $init : 0;
				$total = $this->get_modules_count('topic', $condition);
			    $topics = $this->get_modules('topic', $condition, array('sent_date', 'DESC'), array($init, $items_per_page));
				$url = $_SERVER['REQUEST_URI'];
			}
			else {
			    $topics = $params['results'];
				$total = $params['total_count'];
				$url = $params['url'];
				$is_js = $params['is_js'];
				$show_topic_title = true;
			}
			$this->html->assign('user', $user);
			$this->html->assign('topics', $topics);
			$this->html->assign('total', $total);
			$this->html->assign('url', $url);
			$this->html->assign('is_js', $is_js);
			$this->html->assign('items_per_page', $items_per_page);
			$this->html->assign('show_topic_title', $show_topic_title);
			$this->display();
		}
	}
?>