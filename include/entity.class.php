<?php
    /*
	*
        * Entity: object representation of database table entity...
	* @author Khramkov Ivan.
	* 
	*/
	/*
	*@function get_method
	*@param string $method_str
	*@return array
	*/
    function get_method($method_str) {
	     $result = explode('_', $method_str);
		 if (count($result) > 2) {
		     $tmp = $result;
			 array_shift($result);
			 $result = implode('_', $result);
			 $tmp[1] = $result;
			 $result = $tmp;
		 }
		 return $result;
	}
	
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
		*/
	    function __construct($config, $table_name = NULL, $params = array()) {
	        $this->db = new DB($config);
		    $empty = true;
		    if (isset($table_name)) {
		        $this->table_name = $config->db_prefix.'_'.$table_name;
		        $this->db->query = new DB_Query_SELECT();
		        if (get_class($params) == 'DB_Condition') {
		            $empty = false;
		            $this->db->query->setup(array('*'), $this->table_name, $params);
		        }
		        else {
		            $this->db->query->setup(array('*'), $this->table_name);
		            if (!is_array($params) && isset($params)) {
		                $params = array('id', $params);
		            }
		            if (isset($params[0])) {
		                $empty = false;
		                for ($i = 0; $i < count($params); $i += 2) {
		                    $this->db->query->add_condition($params[$i], $params[$i + 1]);
		    	        }
                    }
                }
		        if(!$empty) {
		            $this->properties = $this->db->get_row();
			        $this->id = $this->properties['id'];
		        }
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
		protected function __call($method, $params = NULL) {
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
		*@function get_entity
		*@param string|null $table_name
		*@param string|null $param
		*@param string|integer|float|null $value
		*@return object
		*/
		public function get_entity($table_name = NULL, $param = NULL, $value = NULL) {
		    return new Entity($base_name, $entity_name, array($param, $value));
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
	}
?>