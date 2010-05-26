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
		*@function get_date
		*@param string $period
		*@param string $format
		*@return string
		*/
		public function get_date($period = 'signup_starts', $format = '.') {
		    eval('$date = $this->_get_formatted_date($this->get_'.$period.'(), $format);');
			return $date;
		}
		
		public function is_user_joined($uid) {
		    $entity = $this->get_entity(
			    $this->get_config(), 
				'tournament_entity', 
				array('tournament_id', $this->get_id(), 'entity_id', $uid, 'entity_type', 0)
			);
			return ($entity->get_id())? 1 : 0;
		}
		
		public function join($entity_id, $entity_type = 0) {
		    $entity_ = $this->get_entity($this->get_config(), 'tournament_entity');
			$entity_->set_tournament_id($this->get_id());
			$entity_->set_entity_id($entity_id);
			$entity_->set_entity_type($entity_type);
			$entity_->save();
			unset($entity);
		}
		
		public function get_table() {
		    return $this->get_module(array('tournament_table', 'tournament'), array('tournament_id', $this->get_id()));
		}
		
		public function get_joined_participants() {
		    $db = new DB($this->get_config());
			return $db->select_function(
			    $this->get_config()->get_db_prefix().'_tournament_entity', 
				'id', 
				'count', 
				new DB_Condition('tournament_id', $this->get_id())
			);
		}
		
		public function get_state() {
		    return (
			    ($this->get_play_ends() <= time())? 
				    array('value' => 2, 'title' => 'Finished') : 
					(($this->get_play_starts() <= time())? 
					    array('value' => 1, 'title' => 'Playing') : array('value' => 0, 'title' => 'Signing up')
				)
			);
		}
		
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
		
		public function get_system_type () {
		    return ($this->get_type())? 'knock out' : 'circular';
		}
		
		public function build_table() {
		    if (!$this->get_type()) {
			    $this->_create_initial_table($this->get_players());
			}
			else {
			    $players = $this->get_players();
				shuffle($players);
			    $this->_create_initial_table($players, 1);
			}
		}
		
		private function _create_initial_table($players, $stage = 0) {
		   $pids = array();
		   for ($i = 0; $i < count($players); $i ++) {
			    for ($j = $i + 1; $j < count($players); $j ++) {
				    $table = $this->get_entity($this->get_config(), 'module_tournament_table');
					$table->set_tournament_id($this->get_id());
					$table->set_first_participant($players[$i]->get_player_id());
					$table->set_second_participant($players[$j]->get_player_id());
					if ($stage) {
					    if (!in_array($players[$i]->get_player_id(), $pids) && !in_array($players[$j]->get_player_id(), $pids)) {
					        $table->set_stage($stage);
							$table->set_current(1);
							$pids[] = $players[$i]->get_player_id();
							$pids[] = $players[$j]->get_player_id();
						}
				    }
					$table->save();
				}
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