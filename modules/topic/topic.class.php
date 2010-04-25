<?php
    /*
	*
    * User module, represents message topic...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Topic extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'topic';
		/*
		* Constructor
		*@param object $config
		*@param integer|null $user_id
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
		
		public function run_controller($controller_name, $params = array()) {
		    if (!$this->acl->check_access()) {
			    $this->error('You have not permission to access to the message service');
			}
			return parent::run_controller($controller_name, $params);
		}
		
		public function get_sender() {
		    return $this->get_module('user', array('player_id', $this->get_sender_id()));
		}
		
		public function get_reciever() {
		    return $this->get_module('user', array('player_id', $this->get_reciever_id()));
		}
		
		public function get_sent_date() {;
		    return date('d.m.Y H:i:s', $this->tz_offset + parent::__call('get_sent_date'));
		}
		
		public function get_read_date() {
		    $date = parent::__call('get_read_date');
		    return ($date)? date('d.m.Y H:i:s', $this->tz_offset + $date) : NULL;
		}
		
		public function delete_topic ($box, $totally = false) {
		    if ($totally) {
			    $this->delete(array($this->get_config()->get_db_prefix().'_module_message' => 'topic_id'));
			}
			else {
			    if ($box == 'outbox') {
				    $this->set_deleted_by_sender(1);
				}
				else {
				    $this->set_deleted_by_reciever(1);
				}
				$this->save();
			}
		}
	}
?>