<?php
    /*
	*
        * create_filter_controller: class creates a filter...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../../include/controller.class.php');
	require_once(dirname(__FILE__).'/../../../include/form_validator.class.php');
    class create_filter_controller extends Controller {
	    /*
		* Name of the controller
		*@var string
		*/
	    protected $name = 'create_filter';
		/*
		*@function run
		*@param array $params
		*/
		public function run($params = array()) {
		    $user = $this->acl->get_user();
			if (!$user || !$user->get_is_admin()) {
			    $this->error('Access denied!');
			}
			if ($this->get_request('form')) {
			    $filter = $this->get_entity($this->get_config(), 'module_tournament_filter');
				$tablefield = explode('.', $this->get_request('tablefield'));
				$filter->set_table_name($tablefield[0]);
				$filter->set_field($tablefield[1]);
				$filter->set_operator($this->get_request('operator'));
				$filter->set_value($this->get_request('value'));
				$filter->save();
			}
			$this->html->assign('fields', $this->get_config()->get_tournament_filter_fields());
			$this->html->assign('operators', array(
			    '=' => 'Equal',
				'!=' => 'Not equal',
				'<' => 'Less',
				'<=' => 'Less or equal',
				'>' => 'Greater',
				'>=' => 'Greater or equal',
			));
			$this->display(true);
		}
	}
?>