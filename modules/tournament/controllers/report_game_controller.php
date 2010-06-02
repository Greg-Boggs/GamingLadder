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
			$existed_game->set_tournament_id($tournament->get_id());
		    $existed_game->set_game_dt($game->get_reported_on());
			$existed_game->save();
			$row->save();
			if ($row->get_played_games() == $tournament->get_games_to_play() && $tournament->get_type()) {
			    $this->_set_next_stage($row, $user, $competitor, $tournament->get_games_to_play());
			}
			$this->html->assign('winner', $this->_check_end($tournament, $row));
			$this->html->assign('tid', $tournament->get_id());
			$this->display(true);
		}
		
		private function _check_end($tournament, $row = NULL) {
		    if ($tournament->get_type()) {
			    $db = new DB($this->get_config());
				$count = $db->select_function(
			        $this->get_config()->get_db_prefix().'_module_tournament_table', 
				    'id',
				    'count',
				    new DB_Condition_List(array(
				        new DB_Condition('tournament_id', $tournament->get_id()),
					    'AND',
					    new DB_Condition('stage', $row->get_stage())
				)));
				if ($count == 1) {
				    $table = $tournament->get_table();
			        return $table->set_winner($tournament);
			    }
			}
			else {
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
			}
			return NULL;
		}
		
		private function _set_next_stage($row, $user, $competitor, $games_to_play) {
		    $new_stage = $row->get_stage() + 1;
			//Look for free user
			$query = new DB_Query_SELECT();
			$query->setup(
			    array('first_participant'), 
				$this->get_config()->get_db_prefix().'_module_tournament_table',
				new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $row->get_tournament_id()),
					'AND',
					new DB_Condition('stage', $new_stage)
				))
			);
			$next_row = $this->get_entity($this->get_config(), 'module_tournament_table', new DB_Condition_List(array(
		        new DB_Condition('second_participant', 0),
				'AND',
				new DB_Condition('stage', $row->get_stage()), 
				'AND',
				new DB_Condition('tournament_id', $row->get_tournament_id()),
				'AND',
				new DB_Condition('first_participant', new DB_Condition_Value($query), new DB_Operator('IN', true))
				
			)));
			if (!$next_row->get_id()) {
			    //Search for the opponent of new stage...
		   	    $rows = $this->get_entities(
			        $this->get_config(), 
				    'module_tournament_table', 
				    array('stage', $row->get_stage(), 'current', 1, 'tournament_id', $row->get_tournament_id()),
				    array('second_participant', 'DESC')
			    );
			    for ($i = 0; $i < count($rows); $i ++) {
			        if ($user->get_player_id() == $rows[$i]->get_first_participant() ||
				        $user->get_player_id() == $rows[$i]->get_second_participant()) {
					    //Look for the next pair
					    if (($i + 1) % 2 == 0) {
				            $the_row = (isset($rows[$i - 1]))? $rows[$i - 1] : NULL; 
					    }
					    else {
					        $the_row = (isset($rows[$i + 1]))? $rows[$i + 1] : NULL;
					    }
					    break;
				    }
			    }
			    if (!$the_row) {
			        //Create new row
			        $this->_new_free_pair($row->get_tournament_id(), $new_stage, $user->get_player_id());
				}
				else {
				    $next_row = $this->get_entity(
			            $this->get_config(), 
				        'module_tournament_table',
					    new DB_Condition_List(array(
					        new DB_Condition('tournament_id', $row->get_tournament_id()),
						    'AND',
						    new DB_Condition('stage', $new_stage),
						    'AND',
						    new DB_Condition('second_participant', 0),
						    'AND',
						    new DB_Condition_List(array(
					            new DB_Condition('first_participant', $the_row->get_first_participant()),
						        'OR',
						        new DB_Condition('first_participant', $the_row->get_second_participant()),	
					        ))
					    ))
					);
					if (!$next_row->get_id()) {
					    $this->_new_free_pair($row->get_tournament_id(), $new_stage, $user->get_player_id());
					}
					else {
					    $next_row->set_second_participant($user->get_player_id());
			            $next_row->save();
					}
				}
			}
			else {
			    $this->_new_free_pair(
				    $row->get_tournament_id(), 
					$new_stage, 
					$next_row->get_first_participant(),
					$user->get_player_id()
				);
			}
			$db = new DB($this->get_config());
			$count = $db->select_function(
			    $this->get_config()->get_db_prefix().'_module_tournament_table', 
				'id',
				'count',
				new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $row->get_tournament_id()),
					'AND',
					new DB_Condition('stage', $row->get_stage()),
					'AND',
					new DB_Condition('played_games', $games_to_play, new DB_Operator('<')),
					'AND',
					new DB_Condition('second_participant', 0, new DB_Operator('!='))
			    ))
			);
			if (!$count) {
			    $db->update(
				    array('current' => 0),
					$this->get_config()->get_db_prefix().'_module_tournament_table',
					new DB_Condition_List(array(
				        new DB_Condition('tournament_id', $row->get_tournament_id()),
					    'AND',
					    new DB_Condition('stage', $row->get_stage())
			        ))
				);
				$db->update(
				    array('current' => 1),
					$this->get_config()->get_db_prefix().'_module_tournament_table',
					new DB_Condition_List(array(
				        new DB_Condition('tournament_id', $row->get_tournament_id()),
					    'AND',
					    new DB_Condition('stage', $new_stage)
			        ))
				);
			}
        }
		
		private function _new_free_pair($tid, $stage, $pid, $pid2 = 0) {
		    $the_row = $this->get_entity($this->get_config(), 'module_tournament_table');
		    $the_row->set_tournament_id($tid);
			$the_row->set_stage($stage);
			$the_row->set_first_participant($pid);
			$the_row->set_second_participant($pid2);
			$the_row->save();
		}
	}
?>