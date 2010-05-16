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
		
		public function get_ordered_participants_by_stage($stage) {
		    $pids = array();
			$participants = array();
		    foreach($this->rows as $key => $row) {
			    if (!in_array($row->get_first_participant(), $pids) && $row->get_stage() == $stage) {
				    $participants[] = $this->get_participant_by_id($row->get_first_participant());
					$pids[] = $row->get_first_participant();
				}
				if (!in_array($row->get_second_participant(), $pids) && $row->get_stage() == $stage) {
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
		
		public function get_the_row_by_stage($stage, $participant) {
		    foreach($this->rows as $key => $row) {
			    if (
				    ($row->get_first_participant() == $participant || $row->get_second_participant() == $participant) && 
					$row->get_stage() == $stage
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
				array('stage', $stage, 'tournament_id', $this->get_tournament_id())
			);
			foreach ($rows as $key => $row) {
			    $pids[] = $row->get_first_participant();
				$pids[] = $row->get_second_participant();
				$pairs[] = array(
				    'first' => $this->get_participant_by_id($row->get_first_participant()),
					'second' => $this->get_participant_by_id($row->get_second_participant())
				);
			}
			$this->pairs = $pairs;
			//Search fo free participant
			$players = $this->get_ordered_participants_by_stage($stage - 1);
			$free = 0;
			foreach ($players as $key => $player) {
			    if (!in_array($player->get_player_id(), $pids)) {
				    $this->free = $player;
					break;
				}
			}
			return array('free' => $this->free, 'rows' => $pairs);
		}
		
		public function get_knock_out_stage_count() {
		    $db = new DB($this->get_config());
			$this->stage = $db->select_function(
			    $this->get_config()->get_db_prefix().'_module_tournament_table',
				'stage',
				'max',
				new DB_Condition('tournament_id', $this->get_tournament_id())
			);
			return $this->stage;
		}
		
		public function get_winner() {
		    $this->get_knock_out_stage_situation($this->stage);
		    if (count($this->pairs) == 1 && !$this->free) {
			    $row = $this->get_the_row($this->pairs[0]['first']->get_player_id(), $this->pairs[0]['second']->get_player_id());
			    $player = ($row->get_first_result() > $row->get_second_result())? $row->get_first_participant() : $row->get_second_participant();
				return ($player == $this->pairs[0]['first']->get_player_id())? $this->pairs[0]['first'] : $this->pairs[0]['second'];
			}
			return NULL;
		}
		
	}
?>