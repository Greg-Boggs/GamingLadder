<?php
    /*
	*
         * report_game_controller: class reports played game as tournament game.
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class report_game_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'report_game';
		/*
		*@function run
		*/
		public function run() {
		    $user = $this->acl->get_user();
		    $tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    $this->error('Unknown tournament!');
			}
			$game = $this->get_entity($this->get_config(), 'games', array('reported_on', $this->get_request('game')));
			$existed_row = $this->get_entity($this->get_config(), 'module_tournament_table', array('game_dt', $game->get_reported_on()));
			if ($existed_row->get_id()) {
			    $this->error('Game is already used in another tournament game!');
			}
			if (!$game->get_winner()) {
			    $this->error('Unknown game!');
			}
			if ($game->get_winner() != $user->get_name()) {
			    $this->error('You are not able to report this game, because you are not the winner in the game!');
			}
			$query_name = new DB_Query_SELECT();
			$query_name->setup(
			    array('player_id'), 
				$this->get_config()->get_db_prefix().'_players', 
				new DB_Condition('name', $game->get_loser())
			);
			$arr = array(
			    new DB_Condition_List(array(
				    new DB_Condition_List(array(
					    new DB_Condition('first_participant', new DB_Condition_Value($query_name), new DB_Operator('IN')),
						'AND',
						new DB_Condition('second_participant', $user->get_player_id())
					)),
				    'OR',
					new DB_Condition_List(array(
					    new DB_Condition('first_participant', $user->get_player_id()),
						'AND',
						new DB_Condition('second_participant', new DB_Condition_Value($query_name), new DB_Operator('IN'))
					))
				)),
				'AND',
				new DB_Condition('game_dt', '0000-00-00 00:00:00'),
				'AND',
				new DB_Condition('tournament_id', $tournament->get_id())
			);
			if ($tournament->get_type()) {
			    $query = new DB_Query_SELECT();
				$query->setup(
				    array('max(stage)'), 
					$this->get_config()->get_db_prefix().'_module_tournament_table', 
					new DB_Condition('tournament_id', $tournament->get_id())
				);
			    $arr[] = 'AND';
				$arr[] = new DB_Condition('stage', new DB_Condition_Value($query));        
			}
			$condition = new DB_Condition_List($arr);
			$row = $this->get_entity($this->get_config(), 'module_tournament_table', $condition);
			$competitor = 0;
			if ($row->get_first_participant() == $user->get_player_id()) {
			    $row->set_first_result(1);
				$competitor = $row->get_second_participant();
			}
			else {
			    $row->set_second_result(1);
				$competitor = $row->get_first_participant();
			}
			$row->set_game_dt($game->get_reported_on());
			$row->save();
			if ($tournament->get_type()) {
			    $this->_set_next_stage($row, $competitor);
			}
			$this->html->assign('winner', $this->_check_end($tournament));
			$this->html->assign('tid', $tournament->get_id());
			$this->display();
		}
		
		private function _check_end($tournament) {
		    $row = $this->get_entity(
			    $this->get_config(), 
				'module_tournament_table', 
				array('tournament_id', $tournament->get_id(), 'game_dt', '0000-00-00 00:00:00')
			);
		    if (!$row->get_id()) {
			    $table = $this->get_module(array('tournament_table', 'tournament'), array('tournament_id', $tournament->get_id()));
			    return $table->set_winner($tournament);
			}
			return NULL;
		}
		
		private function _set_next_stage($row, $competitor) {
		    //delete pairs with looser...
		    $db = new DB($this->get_config());
			$db->delete(
			    $this->get_config()->get_db_prefix().'_module_tournament_table', 
				new DB_Condition_List(array(
				    new DB_Condition('first_participant', $competitor),
					'OR',
					new DB_Condition('second_participant', $competitor)
				))
			);
			//change current stage...
			$row->set_stage($row->get_stage() + 1);
			$row->save();
			//Get new current stage row...
			$query = new DB_Query_SELECT();
			$query->setup(
			    array('min(stage)'), 
				$this->get_config()->get_db_prefix().'_module_tournament_table', 
				new DB_Condition('tournament_id', $row->get_tournament_id())
			);
		    $new_row = $this->get_entity(
			    $this->get_config(),
				'module_tournament_table',
				new DB_Condition_List(array(
				    new DB_Condition('stage', new DB_Condition_Value($query)),
					'AND',
					new DB_Condition('id', $row->get_id(), new DB_Operator('!='))
				)),
				array('id', 'ASC')
			);
			if ($new_row->get_id()) {
			    $new_row->set_stage($new_row->get_stage() + 1);
			    $new_row->save();
			}
		}
	}
?>