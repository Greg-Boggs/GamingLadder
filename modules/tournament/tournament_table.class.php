<?php
    /*
	*
         * Tournament_table module, represents tournament's stroke...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament_table extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournament_table';
		/*
		* Section of the module
		*@var string
		*/
	    protected $section = 'tournament';
		/*
		* Rows of tournament table
		*@var string
		*/
		private $rows;
		/*
		* Knock out stage
		*@var string
		*/
		private $stage;
		/*
		* Knock out pairs of current stage
		*@var string
		*/
		private $pairs;
		/*
		* Knock out free in the stage participant
		*@var string
		*/
		private $free = NULL;
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
			$this->rows = $this->get_entities(
			    $this->get_config(), 
				'module_tournament_table', 
				array('tournament_id', $this->get_tournament_id()),
				array('id', 'ASC')
			);
		}
		
		public function get_rows() {
		    return $this->rows;
		}
		
		public function get_row_count() {
		    return count($this->rows);
		}
		
		public function get_participant_by_id($pid) {
		    //TODO: different types of participants...
		    return $this->get_module('user', array('player_id', $pid));
		}
		
		public function get_ordered_participants() {
		    $pids = array();
			$participants = array();
		    foreach($this->rows as $key => $row) {
			    if (!in_array($row->get_first_participant(), $pids)) {
				    $participants[] = $this->get_participant_by_id($row->get_first_participant());
					$pids[] = $row->get_first_participant();
				}
				if (!in_array($row->get_second_participant(), $pids)) {
				    $participants[] = $this->get_participant_by_id($row->get_second_participant());
					$pids[] = $row->get_second_participant();
				}
			}
			return $participants;
		}
		
		public function get_the_row($first_participant, $second_participant) {
		    foreach($this->rows as $key => $row) {
			    if (
				    ($row->get_first_participant() == $first_participant && $row->get_second_participant() == $second_participant) ||
					($row->get_first_participant() == $second_participant && $row->get_second_participant() == $first_participant)
				) {
				    return $row;
				}
			}
			return NULL;
		}
		
		public function get_knock_out_stage_situation($stage) {
		    $pids = array();
			$pairs = array();
		    $rows = $this->get_entities(
			    $this->get_config(), 
				'module_tournament_table', 
				array('stage', $stage, 'current', 1, 'tournament_id', $this->get_tournament_id())
			);
			return $rows;
		}
		
		public function get_knock_out_stage_count() {
		    $db = new DB($this->get_config());
			$this->stage = $db->select_function(
			    $this->get_config()->get_db_prefix().'_module_tournament_table',
				'stage',
				'max',
				new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $this->get_tournament_id()),
					'AND',
					new DB_Condition('current', 1)
				))
			);
			return $this->stage;
		}
		
		public function get_winner() {
		    $result = $this->get_entity($this->get_config(), 'tournament_result', array('tournament_id', $this->get_tournament_id()));
			return ($result->get_id())? $this->get_participant_by_id($result->get_winner_id()) : NULL;
		}
		
		public function set_winner($tournament) {
		    $winner = NULL;
		    if ($tournament->get_type()) {
			    //Get the winner of the last stage...
				$query = new DB_Query_SELECT();
				$query->setup(
				    array('max(stage)'),
					$this->get_config()->get_db_prefix().'_module_tournament_table',
					new DB_Condition('tournament_id', $this->get_tournament_id())
				);
				$condition = new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $this->get_tournament_id()),
					'AND',
					new DB_Condition('stage', new DB_Condition_Value($query), new DB_Operator('IN'))
				));
				$row = $this->get_entity($this->get_config(), 'module_tournament_table', $condition);
				$result = $this->get_entity(
				    $this->get_config(), 
					'tournament_result', 
					array('tournament_id', $tournament->get_id())
				);
				$result->set_winner_id(
				    (($row->get_first_result())? $row->get_first_participant() : $row->get_second_participant())
				);
				$result->save();
				$winner = $this->get_participant_by_id($result->get_winner_id());
			}
			else {
			    //Calculate count for every player...
				$players = $this->_get_total_score();
				//Get players with maximal count
				$max = -1;
				$maximals = array();
				foreach ($players as $pid => $info) {
				    $max = ($max <= $info['count'])? $info['count'] : $max;
				}
				foreach ($players as $pid => $info) {
				    if ($info['count'] == $max) {
					    $maximals[] = $pid;
					}
				}
				//Calculate Berger Coefficient.
				$max_id = 0;
				$max = 0;
				foreach ($maximals as $key => $pid) {
				    $tmp = $this->_get_berger_coefficient($pid, $players);
				    if ($max <= $tmp) {
					    $max = $tmp;
						$max_id = $pid;
					}
				}				
				$result = $this->get_entity(
				    $this->get_config(), 
					'tournament_result', 
					array('tournament_id', $tournament->get_id())
				);
				$result->set_winner_id($max_id);
				$result->save();
				$winner = $this->get_participant_by_id($max_id);
			}
			$tournament->set_play_ends(time());
			$tournament->save();
			return $winner;
		}
		
		private function _get_total_score() {
		    $users = array();
		    foreach($this->rows as $key => $row) {
			    $users[$row->get_first_participant()]['count'] += $row->get_first_result();
				$users[$row->get_first_participant()]['competitors'][] = $row->get_second_participant();
				$users[$row->get_second_participant()]['count'] += $row->get_second_result();
				$users[$row->get_second_participant()]['competitors'][] = $row->get_first_participant();
			}
			return $users;
		}
		
		private function _get_berger_coefficient($pid, $players) {
	        $result = 0;
		    foreach ($players[$pid]['competitors'] as $key => $id) {
			    $result += $players[$id]['count'];
			}
			return $result;
		}
		
	}
?>