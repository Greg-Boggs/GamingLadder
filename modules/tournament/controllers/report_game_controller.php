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
			if (!$user->get_player_id()) {
			    echo "Access denied!";
				exit;
			}
		    $tournament = $this->get_module('tournament', array('id', $this->get_request('tid')));
			if (!$tournament->get_id()) {
			    echo 'Unknown tournament!';
				exit;
			}
			$game = $this->get_entity($this->get_config(), 'games', array('reported_on', $this->get_request('game')));
			$existed_game = $this->get_entity($this->get_config(), 'tournament_game', array('game_dt', $game->get_reported_on()));
			if ($existed_game->get_id()) {
			    echo 'Game is already used in another tournament game!';
				exit;
			}
			if (!$game->get_winner()) {
			    echo 'Unknown game!';
				exit;
			}
			if ($game->get_winner() != $user->get_name()) {
			    echo 'You are not able to report this game, because you are not the winner in the game!';
				exit;
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
				new DB_Condition('played_games', $tournament->get_games_to_play(), new DB_Operator('<')),
				'AND',
				new DB_Condition('tournament_id', $tournament->get_id())
			);
			if ($tournament->get_type()) {
			    $arr[] = 'AND';
				$arr[] = new DB_Condition('current', 1);        
			}
			$condition = new DB_Condition_List($arr);
			$row = $this->get_entity($this->get_config(), 'module_tournament_table', $condition);
			if (!$row->get_id()) {
			    echo 'Wrong game!';
				exit;
			}
			$row->set_played_games($row->get_played_games() + 1);
			$competitor = 0;
			if ($row->get_first_participant() == $user->get_player_id()) {
			    $row->set_first_result($row->get_first_result() + 1);
				$competitor = $row->get_second_participant();
			}
			else {
			    $row->set_second_result($row->get_second_result() + 1);
				$competitor = $row->get_first_participant();
			}
			if ($row->get_played_games() == $tournament->get_games_to_play() && $tournament->get_type()) {
			    $this->_set_next_stage($row, $user, $competitor);
			}
			$existed_game->set_tournament_id($tournament->get_id());
		    $existed_game->set_game_dt($game->get_reported_on());
			$existed_game->save();
			$row->save();
			$this->html->assign('winner', $this->_check_end($tournament));
			$this->html->assign('tid', $tournament->get_id());
			$this->display(true);
		}
		
		private function _check_end($tournament) {
		    $db = new DB($this->get_config());
		    if (!$db->select_function(
			    $this->get_config()->get_db_prefix().'_module_tournament_table', 
				'id',
				'count',
				new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $tournament->get_id()),
					'AND',
					new DB_Condition('played_games', $tournament->get_games_to_play(), new DB_Operator('<'))
			    ))
			)) {
			    $table = $tournament->get_table();
			    return $table->set_winner($tournament);
			}
			return NULL;
		}
		
		private function _set_next_stage($row, $user, $competitor) {
		    //delete pairs with looser...
		    $db = new DB($this->get_config());
			$db->delete(
			    $this->get_config()->get_db_prefix().'_module_tournament_table', 
				new DB_Condition_List(array(
				    new DB_Condition_List(array(
				        new DB_Condition('first_participant', $competitor),
					    'OR',
					    new DB_Condition('second_participant', $competitor)
					)),
					'AND',
					new DB_Condition('tournament_id', $row->get_tournament_id()),
					'AND',
					new DB_Condition('played_games', 0)
				))
			);
			//Set new stage for rows, where winner is (if participant already is in row with new stage - don't modify this)...
			$new_stage = $row->get_stage() + 1;
			$db = new DB($this->get_config());
			$condition = new DB_Condition_List(array(
			    new DB_Condition('tournament_id', $row->get_tournament_id()),
				'AND',
				new DB_Condition('played_games', 0),
				'AND',
				new DB_Condition_List(array(
				    new DB_Condition('first_participant', $user->get_player_id()),
					'OR',
					new DB_Condition('second_participant', $user->get_player_id())
				))
			));
			$db->update(
			    array('stage' => $new_stage), 
				$this->get_config()->get_db_prefix().'_module_tournament_table', 
				$condition
			);
			//if no unplayed rows with current stage - change current stage..
			$condition = new DB_Condition_List(array(
			    new DB_Condition('tournament_id', $row->get_tournament_id()),
				'AND',
				new DB_Condition('played_games', 0),
				'AND',
				new DB_Condition('stage', $row->get_stage())
			));
			if (!$db->select_function(
			    $this->get_config()->get_db_prefix().'_module_tournament_table', 
				'id', 
				'count',
				$condition
			)) {
			    //Set free player, if count of new pairs is odd...
				$condition = new DB_Condition_List(array(
			        new DB_Condition('tournament_id', $row->get_tournament_id()),
				    'AND',
				    new DB_Condition('played_games', 0),
				    'AND',
				    new DB_Condition('stage', $new_stage)
			    ));
				$rows = $this->get_entities($this->get_config(), 'module_tournament_table', $condition);
				$pids = array();
		        for ($i = 0; $i < count($rows); $i ++) {
				    if (
					    !in_array($rows[$i]->get_first_participant(), $pids) && 
						!in_array($rows[$i]->get_second_participant(), $pids)
					) {
				        $pids[] = $rows[$i]->get_first_participant();
						$pids[] = $rows[$i]->get_second_participant();
				    }
					else {
					    $rows[$i]->set_stage(0);
						$rows[$i]->set_current(0);
						$rows[$i]->save();
					}
				}
		        $condition = new DB_Condition_List(array(
			        new DB_Condition('tournament_id', $row->get_tournament_id()),
				    'AND',
				    new DB_Condition('stage', $row->get_stage() + 1)
				));
			    $db->update(
			        array('current' => 1), 
				    $this->get_config()->get_db_prefix().'_module_tournament_table', 
			  	    $condition
			    );
			}
        }
	}
?>