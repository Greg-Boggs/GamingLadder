<?php
    /*
	*
         * get_valid_games_controller: class returns games, valid for the current tournament.
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class get_valid_games_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'get_valid_games';
		/*
		* Tournament
		*@var object
		*/
	    private $tournament;
		/*
		*@function run
		*/
		public function run() {
		    $this->tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$this->tournament->get_id()) {
			    echo "Unknown tournament!";
				exit;
			}
			$this->html->assign('tid', $this->tournament->get_id());
			$this->html->assign(
			    'games', 
				(($this->tournament->get_type())? $this->_get_games_for_knock_out() : $this->_get_games_for_circular())
			);
			$this->display(true);
		}
		
		private function _get_games_for_circular() {
		    $user = $this->acl->get_user();
			$condition = new DB_Condition_List(array(
			    new DB_Condition_List(array(
				    new DB_Condition('first_participant', $user->get_player_id()),
					'OR',
					new DB_Condition('second_participant', $user->get_player_id())
				)),
				'AND',
				new DB_Condition('game_dt', '0000-00-00 00:00:00'),
				'AND',
				new DB_Condition('tournament_id', $this->tournament->get_id())
			));
			$free_rows = $this->get_entities($this->get_config(), 'module_tournament_table', $condition);		
		    $arr = array();
			$query_name = new DB_Query_SELECT();
			$c = count($free_rows);
			for ($i = 0; $i < $c; $i ++) {
			    $query_name = new DB_Query_SELECT();
			    $query_name->setup(
				    array('name'), 
					$this->get_config()->get_db_prefix().'_players', 
					new DB_Condition('player_id', $free_rows[$i]->get_first_participant())
				);
			    $arr[] = new DB_Condition('loser', new DB_Condition_Value($query_name), new DB_Operator('IN'));
				$arr[] = 'OR';
				$query_name = new DB_Query_SELECT();
				$query_name->setup(
				    array('name'), 
					$this->get_config()->get_db_prefix().'_players', 
					new DB_Condition('player_id', $free_rows[$i]->get_second_participant())
				);
			    $arr[] = new DB_Condition('loser', new DB_Condition_Value($query_name), new DB_Operator('IN'));
				if ($i != $c - 1) {
				    $arr[] = 'OR';
				}	
			}
			if (count($arr)) {
			    $condition = new DB_Condition_List(array(
			        new DB_Condition_List($arr),
				    'AND',
				    new DB_Condition('winner', $user->get_name()),
				    'AND',
				    new DB_Condition('reported_on', date('Y-m-d h:i:s', $this->tournament->get_play_starts() - 100), new DB_Operator('>=')),
					'AND',
					$this->_get_unused_games_condition()
			    ));
			    return $this->get_entities($this->get_config(), 'games', $condition);
			}
			else {
			    return NULL;
			}
		}
		
		private function _get_games_for_knock_out() {
		    $user = $this->acl->get_user();
			$condition = new DB_Condition_List(array(
			    new DB_Condition('tournament_id', $this->tournament->get_id()),
				'AND',
				new DB_Condition('game_dt', '0000-00-00 00:00:00'),
				'AND',
				new DB_Condition('current', 1),
				'AND',
				new DB_Condition_List(array(
				    new DB_Condition('first_participant', $user->get_player_id()),
					'OR',
					new DB_Condition('second_participant', $user->get_player_id())
				))
			));
			$row = $this->get_entity(
			    $this->get_config(), 
				'module_tournament_table',
				$condition
			);
			if ($row->get_id()) {
			    $query_name = new DB_Query_SELECT();
				$uid = ($row->get_first_participant() != $user->get_player_id())? 
				    $row->get_first_participant() :
				    $row->get_second_participant();
			    $query_name->setup(
				    array('name'), 
					$this->get_config()->get_db_prefix().'_players', 
					new DB_Condition('player_id', $uid)
				);
				$condition = new DB_Condition_List(array(
				    new DB_Condition('winner', $user->get_name()),
					'AND',
					new DB_Condition('loser', new DB_Condition_Value($query_name), new DB_Operator('IN')),
				    'AND',
				    new DB_Condition(
					    'reported_on', 
						date('Y-m-d h:i:s', $this->tournament->get_play_starts() - 100), 
						new DB_Operator('>=')
					),
					'AND',
					$this->_get_unused_games_condition()
			    ));
			    return $this->get_entities($this->get_config(), 'games', $condition);
			}
			else {
			    return NULL;
			}
		}
		
		private function _get_unused_games_condition() {
		    $query = new DB_Query_SELECT();
		    $query->setup(
			    array('game_dt'), 
				$this->get_config()->get_db_prefix().'_module_tournament_table'
			);
		    return new DB_Condition('reported_on', new DB_Condition_Value($query), new DB_Operator('IN', 1));
		}
	}
?>