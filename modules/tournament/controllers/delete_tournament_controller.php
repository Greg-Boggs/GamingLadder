<?php
    /*
	*
        * delete_tournament_controller: class deletes message...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class delete_tournament_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'delete_tournament';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
			//Define current user...
			$user = $this->acl->get_user();
			if (!$user->get_is_admin()) {
			    $this->html->assign('error', 'You are not able to delete tournament!');
			}
			else {
			    $tournament = $this->get_entity(
			        $this->get_config(),
				    'module_tournament',
				    array('id', $this->get_request('tid'))
			    );
				if ($tournament->get_id()) {
				    $tournament->delete(array(
					    $this->get_config()->get_db_prefix().'_module_tournament_table' => 'tournament_id',
						$this->get_config()->get_db_prefix().'_tournament_entity' => 'tournament_id',
						$this->get_config()->get_db_prefix().'_tournament_result' => 'tournament_id',
						$this->get_config()->get_db_prefix().'_tournament_filter_xrel' => 'tournament_id'
					));
					$this->html->assign('success', 1);
				}
				else {
				    $this->html->assign('error', 'Unknown tournament');
				}
			}
			$this->display(true);
		}
	}
?>