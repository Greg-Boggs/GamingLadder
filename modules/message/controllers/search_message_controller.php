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
		    $user = $this->get_user();
		    $box = $this->get_request('box');
		    $goal = $this->get_request('goal');
			$users = explode(',', $this->get_request('users'));
			$init_date = strtotime($this->get_request('init_date'));
			$last_date = strtotime($this->get_request('last_date'));
			$text = $this->get_request('text');
			$all_w = $this->get_request('all_w');
			$form = $this->get_request('form');
			if ($form) {
			    $condition_box = new DB_Condition((($box)? 'sender_id' : 'reciever_id'), $user->get_id(), '=', (($box == 2)? array('OR', false) : NULL));
				if ($box == 2) {
				    $condition_box->add_cond('reciever_id', $user->get_id());
				}
				//Look, where fulltext search is required...
				if (!$goal || $goal == 2) {
			        //$matching_topic = new DB_Match(array('topic'), "$text*", array('AND', true));
					$matching_topic = new DB_Condition('topic', "%$text%", new DB_Operator('LIKE'), array('AND', true));
				}
				if ($goal) {
				    //$matching_message = new DB_Match(array('content'), "$text*");
				}
				$condition = new DB_Condition('sent_date', $init_date, new DB_Operator('>='), array('AND', false));
				if ($matching_message) {
				    $query = new DB_Query_SELECT();
					$query->setup(array('topic_id'), $this->get_config()->db_prefix.'_module_message');
					$query->add_condition2($matching_message);
					$condition->add_cond('id', new DB_Condition_Value($query), new DB_Operator('IN'), array('AND', false));
				}
				if ($matching_topic) {
				    $condition->add_cond2($matching_topic);
				}
				$condition->add_cond('sent_date', $last_date, new DB_Operator('<='), array('AND', true));
				$condition->add_cond2($condition_box);
				$results = $this->get_entities($this->get_config(), 'module_topic', $condition);
				$this->html->assign('result', 1);
			    $this->html->assign('results', $results);
				$this->display(true);
			}
			else {
			    if (empty($init_date)) {
			        $db = new DB($this->get_config());
				    $condition = new DB_Condition('sender_id', $user->get_id(), '=', array('OR', false));
				    $condition->add_cond('reciever_id', $user->get_id());
				    $init_date = date('m/d/Y', $db->select_function($this->get_config()->db_prefix.'_module_topic', 'sent_date', 'min', $condition));
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