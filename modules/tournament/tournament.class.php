<?php
    /*
	*
    * Tournament module, represents tournament...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournament';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);	
			if (!$this->get_id()) {
			    return true;
			}
			//If signig up date is expired...
			$cojp = $this->get_joined_participants();
			if ($this->get_sign_up_ends() < time() && $cojp < $this->get_min_participants()) {
			    $result = $this->get_entity($this->get_config(), 'tournament_result', array('tournament_id', $this->get_id()));
				$result->set_winner_id(-2);
				$result->save();
			    $this->set_play_ends(time());
				$this->set_min_participants(0);
				$this->save();
				return true;
			}
			if ($this->get_sign_up_ends() < time() && $cojp < $this->get_max_participants()) {
				$this->set_max_participants($cojp);
				$this->save();
				return true;
			}
			//If playing date is expired
			else {
                $table = $this->get_table();
				if ($this->get_play_ends() < time() && $table->get_winner() == 0) {
				    $table->set_winner($this);
                }  
			}
		}
		/*
		*@function apply_filter
		*@param integer $fid
		*/
		public function apply_filter($fid) {
		    $frel = $this->get_entity(
			    $this->get_config(), 
				'tournament_filter_xrel', 
				array('tournament_id', $this->get_id(), 'filter_id', $fid)
			);
			if (!$frel->get_id()) {
			    $frel->set_tournament_id($this->get_id());
				$frel->set_filter_id($fid);
				$frel->save();
			}
		}
		/*
		*@function run_filters
		*@param string $name
		*@return integer
		*/
		public function run_filters($name) {
		    $query = new DB_Query_SELECT();
			$query->setup(
			    array('filter_id'), 
				$this->get_config()->get_db_prefix().'_tournament_filter_xrel', 
				new DB_Condition('tournament_id', $this->get_id())
			);
			$filters = $this->get_modules(
			    array('tournament_filter', 'tournament'), 
				new DB_Condition('id', new DB_Condition_Value($query), new DB_Operator('IN'))
			);
		    foreach ($filters as $key => $filter) {
			    if (!$filter->admit($name)) {
				    return 0;
				}
			}
			return 1;
		}
		/*
		*@function get_date
		*@param string $period
		*@param string $format
		*@return string
		*/
		public function get_date($period = 'signup_starts', $format = '.') {
		    eval('$date = $this->_get_formatted_date($this->get_'.$period.'(), $format);');
			return $date;
		}
		/*
		*@function is_user_joined
		*@param integer $uid
		*@return integer
		*/
		public function is_user_joined($uid) {
		    $entity = $this->get_entity(
			    $this->get_config(), 
				'tournament_entity', 
				array('tournament_id', $this->get_id(), 'entity_id', $uid, 'entity_type', 0)
			);
			return ($entity->get_id())? 1 : 0;
		}
		/*
		*@function join
		*@param integer $entity_id
		*@param integer $entity_type
		*/
		public function join($entity_id, $entity_type = 0) {
		    $entity_ = $this->get_entity($this->get_config(), 'tournament_entity');
			$entity_->set_tournament_id($this->get_id());
			$entity_->set_entity_id($entity_id);
			$entity_->set_entity_type($entity_type);
			$entity_->save();
			unset($entity);
		}
		/*
		*@function get_table
		*@return object
		*/
		public function get_table() {
		    return $this->get_module(array('tournament_table', 'tournament'), array('tournament_id', $this->get_id()));
		}
		/*
		*@function get_joined_participants
		*@return integer
		*/
		public function get_joined_participants() {
		    $db = new DB($this->get_config());
			return $db->select_function(
			    $this->get_config()->get_db_prefix().'_tournament_entity', 
				'id', 
				'count', 
				new DB_Condition('tournament_id', $this->get_id())
			);
		}
		/*
		*@function get_state()
		*@return array
		*/
		public function get_state() {
		    return (
			    ($this->get_play_ends() <= time())? 
				    array('value' => 2, 'title' => 'Finished') : 
					(($this->get_play_starts() <= time())? 
					    array('value' => 1, 'title' => 'Playing') : array('value' => 0, 'title' => 'Signing up')
				)
			);
		}
		/*
		*@function get_players
		*@return array
		*/
		public function get_players() {
		    $query = new DB_Query_SELECT();
			$query->setup(
			    array('entity_id'), 
				$this->get_config()->get_db_prefix().'_tournament_entity', 
				new DB_Condition_List(array(
				    new DB_Condition('tournament_id', $this->get_id()),
					'AND',
					new DB_Condition('entity_type', 0)
				))
			);
			return $this->get_entities(
			    $this->get_config(), 
				'players', 
				new DB_Condition('player_id', new DB_Condition_Value($query), new DB_Operator('IN'))
			);
		}
		/*
		*@function send_notification
		*@param string $title
		*@param string $text
		*/
		public function send_notification($title, $text) {
			$players = $this->get_players();
			foreach ($players as $key => $player) {
			    $message = $this->get_module('message');
				$message->set_content($text);
				$message->send(
				    0,
					$player->get_player_id(),
					$title,
					's'
				);
			}
		}
		/*
		*@function get_system_type
		*@return string
		*/
		public function get_system_type () {
		    return ($this->get_type())? 'knock out' : 'circular';
		}
		/*
		*@function build_table
		*/
		public function build_table() {
		    if (!$this->get_type()) {
			    $this->_create_initial_table($this->get_players());
			}
			else {
			    $players = $this->get_players();
				shuffle($players);
			    $this->_create_pairs($players);
			}
		}
		
		private function _create_initial_table($players) {
		   $pids = array();
		   for ($i = 0; $i < count($players); $i ++) {
			    for ($j = $i + 1; $j < count($players); $j ++) {
				    $table = $this->get_entity($this->get_config(), 'module_tournament_table');
					$table->set_tournament_id($this->get_id());
					$table->set_first_participant($players[$i]->get_player_id());
					$table->set_second_participant($players[$j]->get_player_id());
					$table->save();
				}
			}
		}
		
		private function _create_pairs($players) {
		   $pids = array();
		   for ($i = 0; $i < count($players); $i +=2) {
			    $table = $this->get_entity($this->get_config(), 'module_tournament_table');
				$table->set_tournament_id($this->get_id());
				$table->set_first_participant($players[$i]->get_player_id());
				if (isset($players[$i+1])) {
					$table->set_second_participant($players[$i + 1]->get_player_id());
			    }
				else {
				    $table->set_second_participant(0);
				}
				$table->set_stage(1);
				$table->set_current(1);
				$table->save();
			}
		}
		
		private function _get_formatted_date($date, $format = ".") {
		    if (!$date) {
			    return '';
			}
		    return ($format == "/")? date('m'.$format.'d'.$format.'Y', $date) : date('d'.$format.'m'.$format.'Y', $date);
		}
	}
?>