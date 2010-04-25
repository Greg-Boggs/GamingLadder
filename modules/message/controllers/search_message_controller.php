<?php
    /*
	*
    * search_message_controller: class search messages...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class search_message_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'search_message';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $items_per_page = 10;
		    $user = $this->acl->get_user();
		    $box = $this->get_request('box');
		    $goal = $this->get_request('goal');
			$fromwhere = $this->get_request('fromwhere');
			$status = $this->get_request('status');
			$signature = $this->get_request('signature');
			$users = $this->get_request('users');
			$users = (!empty($users))? explode(',', $users) : array();
			//***
			//If user is admin...
			$player = $this->get_request('player');
			$player = (!empty($player))? $player : NULL;
			$hide_deleted = $this->get_request('hide_deleted');
			//***
			$init_date = strtotime($this->get_request('init_date'));
			$last_date = strtotime($this->get_request('last_date'));
			$text = $this->get_request('text');
			$all_w = $this->get_request('all_w');
			$form = $this->get_request('form');
			if ($form) {
			    //Admin can search every message...
			    if ($user->get_is_admin() && $player) {
				    //Admin searches $player messages 
				    $player = $this->get_entity($this->get_config(), 'players', array('name', $player));
				    $user_id = $player->get_player_id();
				}
				else {
				    //Admin searches his messages
			        $user_id = $user->get_player_id();
				}
			    $condition_box = new DB_Condition((($box)? 'sender_id' : 'reciever_id'), $user_id);
				if ($box == 2) {
				    $condition_box = new DB_Condition_List(array($condition_box, 'OR', new DB_Condition('reciever_id', $user_id)));
				}
				if ($hide_deleted) {
				    $condition_box = new DB_Condition_List(array(
					    $condition_box,
						'AND',
						new DB_Condition('deleted_by_sender', 0),
						'AND',
						new DB_Condition('deleted_by_reciever', 0)
					));
				}
				//Look at the read or unread messages
				$condition_status = NULL;
				if ($status < 2) {
				    $condition_status = new DB_Condition('read_date', 0, new DB_Operator((($status)? '=' : '>')));
				}
				if ($condition_status) {
				    $condition_box = new DB_Condition_List(array($condition_box, 'AND', $condition_status));
				}
				//Loo at the signature
				$condition_signature = NULL;
				if ($signature) {
				    $condition_signature = new DB_Condition('signature', $signature);
				}
				if ($condition_signature) {
				    $condition_box = new DB_Condition_List(array($condition_box, 'AND', $condition_signature));
				}
				//Look, where fulltext search is required...
				if (!$goal || $goal == 2) {
					$matching = ($all_w)? new DB_Match(array('topic'), "$text") :
					                      new DB_Condition('topic', '%'.str_replace(' ', '%', $text).'%', new DB_Operator('LIKE'));
				}
				if ($goal) {
				    $matching2 = ($all_w)? new DB_Match(array('content'), "$text") : 
					                       new DB_Condition('content', '%'.str_replace(' ', '%', $text).'%', new DB_Operator('LIKE'));
					$query = new DB_Query_SELECT();
					$query->setup(array('topic_id'), $this->get_config()->db_prefix.'_module_message');
					$query->add_condition2($matching2);
					$matching2 = new DB_Condition('id', new DB_Condition_Value($query), new DB_Operator('IN'));
					$matching = ($goal == 2)? new DB_Condition_List(array($matching, 'OR', $matching2)) : $matching2;
				}
				//Date range...
				$condition_date = new DB_Condition_List(array(
				    new DB_Condition('sent_date', $init_date, new DB_Operator('>=')),
					'AND',
					new DB_Condition('sent_date', $last_date, new DB_Operator('<='))
				));
				//Users...
				$condition_users = NULL;
				$tmp = NULL;
				foreach ($users as $key => $user) {
				    $query = new DB_Query_SELECT();
				    $query->setup(array('player_id'), $this->get_config()->db_prefix.'_players', new DB_Condition('name', $user));
					if (!$fromwhere || $fromwhere == 2) {
					    $condition_users = new DB_Condition('sender_id', new DB_Condition_Value($query), new DB_Operator('IN'));
					}
					elseif ($fromwhere) {
					    $condition_users = new DB_Condition('reciever_id', new DB_Condition_Value($query), new DB_Operator('IN'));
					}
					if ($fromwhere == 2) {
					    $condition_users = new DB_Condition_List(array(
					        $condition_users,
						    'OR',
						    new DB_Condition('reciever_id', new DB_Condition_Value($query), new DB_Operator('IN'))
					    ));
					}
					if (count($users) > 1) {
					    if ($tmp) {
					        $condition_users = new DB_Condition_List(array($condition_users, 'OR', $tmp));
					    }
						$tmp = $condition_users;
					}
					unset($query);
				}
				$condition = new DB_Condition_List (array(
				    $condition_box,
					'AND',
					$matching,
					'AND',
					$condition_date
				));
				if ($condition_users) {
				    $condition = new DB_Condition_List(array($condition, 'AND', $condition_users));
				}
				$init = $this->get_request('p_c_p');
				$init = ($init)? $init : 0;
				$results = $this->get_modules('topic', $condition, array('sent_date', 'DESC'), array($init, $items_per_page));
				$this->html->assign('result', 1);
			    $this->html->assign('results', ((count($results))? array(
				    'results' => $results, 
					'total_count' => $this->get_modules_count('topic', $condition),
					'url' => 'javascript: getResults();',
					'is_js' => true
				) : NULL));
				$this->display(true);
			}
			else {
			    $this->html->assign('user', $user);
			    if (empty($init_date)) {
			        $db = new DB($this->get_config());
				    $condition = new DB_Condition('sender_id', $user->get_player_id(), '=', array('OR', false));
				    $condition->add_cond('reciever_id', $user->get_player_id());
				    $init_date = date('m/d/Y', $db->select_function($this->get_config()->get_db_prefix().'_module_topic', 'sent_date', 'min', $condition));
			    }
			    if (empty($last_date)) {
			        $last_date = date('m/d/Y');
			    }
		        $this->html->assign('init_date', $init_date);
			    $this->html->assign('last_date', $last_date);
			    $this->display();
			}
		}
	}
?>