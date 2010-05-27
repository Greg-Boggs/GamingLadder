<?php
    /*
	*
         * Tournament_filter module, represents tournament's filters...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Tournament_filter extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'tournament_filter';
		/*
		* Section of the module
		*@var string
		*/
	    protected $section = 'tournament';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
		/*
		*@function apply_filter
		*@param integer $tid
		*@return integer
		*/
		public function is_applied_to($tid) {
		    $frel = $this->get_entity(
			    $this->get_config(), 
				'tournament_filter_xrel', 
				array('tournament_id', $tid, 'filter_id', $this->get_id())
			);
			return ($frel->get_id())? 1 : 0;
		}
		/*
		*@function admit
		*@param string $name
		*@return integer
		*/
		public function admit($name) {
		    $condition = NULL;
		    if (
			    is_numeric($this->get_value()) || 
				$this->get_operator() == '<' ||
				$this->get_operator() == '<=' ||
				$this->get_operator() == '>' ||
				$this->get_operator() == '>='
			) {
			    $condition = new DB_Condition($this->get_field(), $this->get_value(), new DB_Operator($this->get_operator()));
			}
			else {
			    $neg = ($this->get_operator() == '!=')? true : false;
				$condition = new DB_Condition(
				    $this->get_field(), 
					str_replace('*', '%', $this->get_value()), 
					new DB_Operator('LIKE', $neg)
				);
			}
			$condition = new DB_Condition_List(array(
			    $condition,
				'AND',
				new DB_Condition('name', $name)
			));
			$db = new DB($this->get_config());
			return ($db->select_function(
			    $this->get_config()->get_db_prefix().'_'.$this->get_table_name(),
				'name',
				'count',
				$condition
			))? 1 : 0;
		}
	}
?>