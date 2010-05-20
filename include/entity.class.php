<?php
    /*
	*
        * Entity: object representation of database table entity...
	* @author Khramkov Ivan.
	* 
	*/
	include_once('genericfunctions.inc.php');
    require_once(dirname(__FILE__).'/db.class.php');
    class Entity {
		/*
		* Fields of entity with assigned values
		*@var array
		*/
	    private $properties = array();
		/*
		* Entity identifier
		*@var integer
		*/
		private $id = 0;
		/*
		* Database driver
		*@var string
		*/
		private $db;
		/*
		* Entity table
		*@var string
		*/
		private $table_name;
		/*
		* Constructor
		*@param object $config
		*@param string|null $table_name
		*@param object|array|null $params
		*@param array|null $order
		*/
	    function __construct($config, $table_name = NULL, $params = array(), $order = NULL) {
	        if ($config && count($params)) {
			    $this->db = $this->_get_all_from_base($config, $table_name, $params, $order);
			    if ($this->db) {
			        $this->properties = $this->db->get_row();
			        $this->id = $this->properties['id'];
		        }
			}
			else {
			    $this->db = new DB($config);
		        $this->table_name = $config->get_db_prefix().'_'.$table_name;
			}
        }
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->db);
		}
		/*
		*@function __call
		*@param string $method
		*@return array|null $params
		*/
		public function __call($method, $params = NULL) {
		    //get or set value from entity..
		    $method = get_method($method);
			switch ($method[0]) {
			    case 'get':
				    return (isset($this->properties[$method[1]]))? $this->properties[$method[1]] : NULL; break;
				case 'set': $this->properties[$method[1]] = $params[0]; break;
				default: return NULL;
			}
		}
		/*
		*@function get_properties
		*@return array
		*/
		public function get_properties() {
		    return $this->properties;
		}
		/*
		*@function set_properties
		*@param array $properties
		*/
		public function set_properties($properties) {
		    $this->properties = $properties;
		}
		/*
		*@function save
		*/
		public function save() {
		    if (!$this->id) {
			    $this->id = $this->db->insert($this->properties, $this->table_name);
				$this->properties['id'] = $this->id;
			}   
			else {
			    $this->db->update($this->properties, $this->table_name, new DB_Condition('id', $this->id));
			} 
		}
		/*
		*@function delete
		*@param array $linked
		*/
		public function delete($linked = array()) {
		    $this->db->delete($this->table_name, new DB_Condition('id', $this->id));
			//Delete entites from linked tables, where field $field compared with id of deleted entity ($this->id)...
			foreach ($linked as $table_name => $field) {
			    $this->db->delete($table_name, new DB_Condition($field, $this->id));    
			}
		}
		/*
		*@function get_entity
		*@param object $config
		*@param string|null $table_name
		*@param object|array|null $params
		*@return object
		*/
		public function get_entity($config, $table_name = NULL, $params = NULL, $order = NULL) {
		    return new Entity($config, $table_name, $params, $order);
		}
		/*
		*@function get_entities
		*@param object $config
   	         *@param string|null $table_name
		*@param array $params
		*@param array|null $order
		*@param array|null $limit
		*@return array
		*/
		public function get_entities($config, $table_name = NULL, $params = array(), $order = NULL, $limit = NULL) {
		    $this->_get_all_from_base($config, $table_name, $params, $order, $limit);
		    return $this->_get_list_of_entities($config, $table_name);
		}
		/*
		*@function get_entities_count
		*@param object $config
   	        *@param string|null $table_name
		*@param array $params
		*@return integer
		*/
		public function get_entities_count($config, $table_name = NULL, $params = array()) {
		    $this->_get_all_from_base($config, $table_name, $params, $order, $limit);
		    return $this->db->get_row_count();
		}
		/*
		*@function get_entities_from
		*@param object $config
   	    *@param string $to_table_name
		*@param string $from_table_name
		*@param array $params
		*@param array|object $condition
		*@param array|null $order
		*@param array|null $limit
		*@return array
		*/
		public function get_entities_from($config, $to_table_name, $from_table_name, $params = array('id', 'id'), $condition = array(), $order = NULL, $limit = NULL) {
		    $query = new DB_Query_SELECT();
			if (is_object($condition)) {
		        $query->setup(array($params[1]), $config->get_db_prefix().'_'.$from_table_name, $condition);
		    }
		    else {
		        $query->setup(array($params[1]), $config->get_db_prefix().'_'.$from_table_name);
		        $c = count($condition);
		        for ($i = 0; $i < $c; $i += 2) {
				    if ($c > 2 && $i < $c - 2) {
		                $query->add_condition($condition[$i], $condition[$i + 1], new DB_Operator('='), array('AND', false));
				    }
					else {
					    $query->add_condition($condition[$i], $condition[$i + 1]);
					}
		    	}
            }
			if ($order) {
		        $query->set_order_by($order[0], $order[1]);
			}
			if ($limit) {
			    $query->set_limit($limit[0], $limit[1]);
			}
			$this->db = new DB($config);
			$this->db->query = new DB_Query_SELECT();
			$this->db->query->setup(
			    array('*'), 
				$config->get_db_prefix().'_'.$to_table_name, 
				new DB_Condition($params[0], new DB_Condition_value($query), new DB_Operator('IN'))
			);
			return $this->_get_list_of_entities($config, $to_table_name);
		}
		/*
		*@function _get_all_from_base
		*@param object $config
   	    *@param string $table_name
		*@param array $params
		*@param array|null $order
		*@param array|null $limit
		*@return object|null
		*/	
		private function _get_all_from_base($config, $table_name = NULL, $params = array(), $order = NULL, $limit = NULL) {
		    $this->db = new DB($config);
		    $empty = true;
		    if (isset($table_name)) {
			    $empty = false;
		        $this->table_name = $config->get_db_prefix().'_'.$table_name;
		        $this->db->query = new DB_Query_SELECT();
				if ($order) {
				    $this->db->query->set_order_by($order[0], $order[1]);
				}
				if ($limit) {
				    $this->db->query->set_limit($limit[0], $limit[1]);
				}
		        if (is_object($params)) {
		            $this->db->query->setup(array('*'), $this->table_name, $params);
		        }
		        else {
		            $this->db->query->setup(array('*'), $this->table_name);
		            $c = count($params);
		            for ($i = 0; $i < $c; $i += 2) {
					    if ($c > 2 && $i < $c - 2) {
		                    $this->db->query->add_condition($params[$i], $params[$i + 1], new DB_Operator('='), array('AND', false));
						}
						else {
						    $this->db->query->add_condition($params[$i], $params[$i + 1]);
						}
		    	    }
                }
            }
			return ($empty)? NULL : $this->db;
		}
		/*
		*@function _get_list_of_entities
		*@param object $config
   	    *@param string $table_name
		*@return array
		*/
		private function _get_list_of_entities($config, $table_name) {
		    $result = array();
			if ($this->db) {
			    $items = $this->db->get_all();
			    for ($i = 0; $i < count($items); $i ++) {
				    $result[$i] = new Entity($config, $table_name);
					$result[$i]->properties = $items[$i];
					$result[$i]->id = $items[$i]['id'];
				}
		    }
			return $result;
		}
	}
?>