<?php
    /*
	*
    * HTML: Manage templates and HTML representation of data
	* @author Khramkov Ivan.
	* 
	*/
    class HTML {
	    /*
		* Smarty engine
		*@var object
		*/
		private $smarty;
		/*
		* Constructor
		*@param object $config
		*/
		function __construct($config) {
		    require_once(dirname(__FILE__).'/SMARTY/libs/Smarty.class.php');
			$this->smarty = $this->_get_smarty(
			    $config->smarty_templates_path, 
				$config->smarty_templates_c_path,
				$config->smarty_cache_path
			);
			$this->register_object('html_entity', $this, array(
			    'redirect',
				'loader',
				'paginate'
			));
		}
		/*
		* Destructor
		*/
		function __destruct() {
		    unset($this->smarty);
		}
		/*
		*@function assign
		*@param string $param_name
		*@param variant|null $param_value
		*/
		public function assign($param_name, $param_value) {
		    $this->smarty->assign($param_name, $param_value);
		}
		/*
		*@function fetch
		*@param string $template_path
		*@return string
		*/
		public function fetch($template_path) {
		    return $this->smarty->fetch($template_path);
		}
		/*
		*@function display
		*@param string $template_path
		*/
		public function display($template_path) {
		    $this->smarty->display($template_path);
		}
		/*
		*@function register_object
		*@param string $obj_name
		*@param object $obj
		*@param array $methods
		*@param boolean $flag
		*/
		public function register_object($obj_name, $obj, $methods, $flag = false) {
		    $this->smarty->register_object($obj_name, $obj, $methods, $flag);
		}
		/*
		*@function get_template_dir
		*@return string
		*/
		public function get_template_dir() {
		    return $this->smarty->template_dir;
		}
		/*
		*@function set_template_dir
		*@param string $template_dir
		*/
		public function set_template_dir($template_dir) {
		    $this->smarty->template_dir = $template_dir;
		}
		/*
		*@function restore_template
		*@param array $array
		*/
		public function restore_template($array) {
		    foreach($array as $param => $value) {
			    $this->smarty->assign($param, $value);
			}
		}
		/*
		*@function redirect
		*@param string $url
		*@return string
		*/
		public function redirect($url) {
		    $template = $this->_get_template();
			$template->assign('url', $url);
			return $template->fetch('redirect.tpl');
		}
		/*
		*@function loader
		*@param string $text
		*@return string
		*/
		public function loader($text = '') {
		    $template = $this->_get_template();
			$template->assign('text', $text);
			return $template->fetch('loader.tpl');
		}
		/*
		*@function paginate
		*@param integer $total
		*@param string $url
		*@param integer $items_per_page
		*@return string
		*/
		public function paginate ($total, $url, $items_per_page = 10, $is_js_url = false) {
			$pre_pages_count = 5;
			$post_pages_count = 5;
		    if ($is_js_url) {
			   $url = explode('(', $url);
			   $url = $url[0];
			}
		    $template = $this->_get_template();
			$current_page = ($_GET['p_c_p'])? $_GET['p_c_p'] : 0;
			$count = ($total % $items_per_page)? 1 : 0;
			$count += (integer)($total / $items_per_page);
			$initial_count = $current_page/$items_per_page - $pre_pages_count;
			$initial_count = ($initial_count >= 0)? $initial_count : 0;
			$pages = array();
			$j = $initial_count * $items_per_page;
			for ($i = $initial_count; $i < $count && $i < $initial_count + $pre_pages_count + $post_pages_count; $i ++) {
			    $pages[$i + 1] = $j;
				$j += $items_per_page;
			}
			$template->assign('pages', $pages);
			$template->assign('current_page', $current_page);
			$template->assign('last_page', $items_per_page * $count - $items_per_page);
			$template->assign('url', $url);
			$template->assign('items_per_page', $items_per_page);
			$template->assign('is_js_url', $is_js_url);
			return $template->fetch('paginator.tpl');
		}
		/*
		*Undocumented method now...
		*/
		public function debug() {
		    $this->smarty->debugging = true;
		}
		/*
		*@function _get_smarty
		*@param string $templates_dir
		*@param string $templates_c_dir
		*@param string $cache_dir
		*@return object
		*/
		private function _get_smarty($templates_dir, $templates_c_dir, $cache_dir) {
		    $smarty = new Smarty();
			$smarty->template_dir = $templates_dir;
		    $smarty->compile_dir = $templates_c_dir;
            $smarty->cache_dir = $cache_dir;
			return $smarty;
		}
		/*
		*@function _get_template
		*@return object
		*/
		private function _get_template() {
		    $root = dirname(__FILE__).'/..';
		    return $this->_get_smarty("$root/templates", "$root/templates_c", "$root/cache");
		}
		
	}
?>
