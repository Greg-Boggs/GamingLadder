<?php
    /*
	*
    * Message module, represents private message...
	* @author Khramkov Ivan.
	* 
	*/
    require_once(dirname(__FILE__).'/../../include/module.class.php');
    class Message extends Module {
	    /*
		* Name of the module
		*@var string
		*/
	    protected $name = 'message';
		/*
		* Constructor
		*@param object $config
		*@param array|null $params
		*/
	    function __construct($config, $params = NULL) {
		    parent::__construct($config, $params);
		}
		/*
		*@function get_sent_message
		*@param string $controller_name
		*/
		public function get_sent_date() {
		    $this->get_module('user', array('player_id', $this->get_sender_id()));
			$seconds = parent::__call('get_sent_date');
			return $seconds;
		}
		/*
		*@function send
		*@param integer $sender_id
		*@param integer $reciever_id
		*@param string $topic_title
		*@param string $content
		*TODO: add some special parameters, such as system signature, etc...
		*/
		public function send($sender_id, $reciever_id, $topic_title, $content) {
		    //Get user, who sends...
		    $sender = $this->get_module('user', array('player_id', $sender_id));
			//Get topic object
			$topic = $this->get_module('topic', array('title', $topic_title));
			//If topic with title $topic_title doesn't exists, create new record to database...
			if (!$topic->get_id()) {
			    $topic->set_title($topic_title);
			    $topic->save();
			}
			//Reference...
			$xref = new Entity($this->get_config(), 'message_relation');
			$xref->set_topic_id($topic->get_id());
		    $this->set_sender_id($sender_id);
			$this->set_reciever_id($reciever_id);
			$this->set_content($content);
			$this->save();
			$xref->set_message_id($this->get_id());
			$xref->save();
			$this->set_relation_ref_id($xref->get_id());
			$this->set_sent_date(time());
			$this->save();
		}
		/*
		*@function delete
		*TODO: solve, how delete message...
		*/
		public function delete() {
		    parent::delete(array($this->get_config()->db_prefix.'_message_relation' => 'message_id'));
			//Look, we leave topic!
			
		}
	}
?>