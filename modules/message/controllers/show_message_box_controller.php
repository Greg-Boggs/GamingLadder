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
		public function run($params = array()) {
		    $items_per_page = 30;
			//Define current user...
			$user = $this->acl->get_user();
			$box = $this->get_request('box');
			$box = ($box)? $box : 'inbox';
			$unread = $this->get_request('unread');
			$player_name = $this->get_request('player', array('POST', 'GET'));
			$player = ($user->get_is_admin() && isset($player_name))? 
			$this->get_entity($this->get_config(), 'players', array('name', $player_name)) : $user;
			$player = ($player->get_player_id())? $player : $user;
			//Get topics...
			$condition = new DB_Condition((($box == 'inbox')? 'reciever_id' : 'sender_id'), $player->get_player_id());
			//Admin can see every message: deleted by user also...
		    if(!$user->get_is_admin()) {
			    $condition = new DB_Condition_List(array($condition, 'AND', new DB_Condition((($box == 'inbox')? 'deleted_by_reciever' : 'deleted_by_sender'), 0)));
			}
			if (isset($unread)) {
			    $condition = new DB_Condition_List(array($condition, 'AND', new DB_Condition('read_date', 0)));
			}
			$init = $this->get_request('p_c_p');
			$init = ($init)? $init : 0;
			$total = $this->get_entities_count($this->get_config(), 'module_topic', $condition);
			$topics = $this->get_modules('topic', $condition, array('sent_date', 'DESC'), array($init, $items_per_page));
			$url = $_SERVER['REQUEST_URI'];
			$this->html->assign('user', $user);
			$this->html->assign('player', $player);
	        $this->html->assign('topics', $topics);
			$this->html->assign('box', $box);
			$this->html->assign('total', $total);
			$this->html->assign('url', $url);
			$this->html->assign('items_per_page', $items_per_page);
			$this->display();
		}
	}
?>