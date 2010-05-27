<?php
    /*
	*
        * delete_filter_controller: class deletes message...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
    class delete_filter_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'delete_filter';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
			//Define current user...
			$user = $this->acl->get_user();
			if (!$user->get_is_admin()) {
			    $this->html->assign('error', 'You are not able to delete filter!');
			}
			else {
			    $filter = $this->get_entity(
			        $this->get_config(),
				    'module_tournament_filter',
				    array('id', $this->get_request('fid'))
			    );
				if ($filter->get_id()) {
				    $filter->delete(array(
						$this->get_config()->get_db_prefix().'_tournament_filter_xrel' => 'filter_id'
					));
					$this->html->assign('success', 1);
				}
				else {
				    $this->html->assign('error', 'Unknown filter');
				}
			}
			$this->display(true);
		}
	}
?>