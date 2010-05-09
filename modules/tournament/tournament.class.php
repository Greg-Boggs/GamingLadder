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
		}
		
		private function _get_formatted_date($date, $format = ".") {
		    if (!$date) {
			    return '';
			}
		    return ($format == "/")? date('m'.$format.'d'.$format.'Y', $date) : date('d'.$format.'m'.$format.'Y', $date);
		}
		
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
			    ($this->get_sign_up_ends() >= time())? 
				    array('value' => 0, 'title' => 'Signing up') : 
					(($this->get_play_ends() >= time())? 
					    array('value' => 1, 'title' => 'Playing') : array('value' => 2, 'title' => 'Played')
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
	}
?>