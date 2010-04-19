<?php
    /*
	*
        * get_players_controller: class returns JSON representation of players list...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class get_players_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'get_players';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $prefix = str_replace(' ', '', $this->get_request('prefix'));
			$users = $this->get_entities($this->get_config(), 'players', new DB_Condition('name', "%$prefix%", new DB_Operator('LIKE')));
			$result = "{'values': [";
			if (!count($users)) {
			    $result .= "]}";
			}
			else {
			    foreach ($users as $key => $user) {
			        $result .= "{'id': ".$user->get_player_id().", 'name': '".$user->get_name()."'},";
			    }
			    $result = str_replace(',|', ']}', $result.'|');
			}
			$this->html->assign('users', $result);
			$this->display(true);
		}
	}
?>