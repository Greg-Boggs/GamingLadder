<?php
/*
*
* Simple: simple class, provides database and html interface...
* @author Khramkov Ivan.
*
*/
require_once(dirname(__FILE__) . '/entity.class.php');
require_once(dirname(__FILE__) . '/html.class.php');
require_once(dirname(__FILE__) . '/acl.class.php');
require_once(dirname(__FILE__) . '/genericfunctions.inc.php');

class Simple extends Entity
{
    /*
    * Configuration
    *@var object
    */
    private $config;
    /*
    * HTML object
    *@var object
    */
    var $html;
    /*
    * ACL
    *@var object
    */
    var $acl;

    /*
    * Constructor
    *@param object $config
    */
    function __construct($config, $table_name = NULL, $params = array())
    {
        parent::__construct($config, $table_name, $params);
        $this->config = $config;
        date_default_timezone_set($config->get_ladder_timezone());
        $this->html = new HTML($this->config);
        $this->html->register_object('application', $this, array('load_module'));
        $this->acl = new ACL($this->config);
    }

    /*
    * Destructor
    */
    function __destruct()
    {
        unset($this->config);
        unset($this->html);
        unset($this->acl);
        parent::__destruct();
    }

    /*
    *@function error
    *@param string $message
    *@param integer $error_code
    */
    public function error($message, $error_code = 404)
    {
        throw new Exception($message, $error_code);
    }

    /*
    *@function get_config
    *@return object
    */
    public function get_config()
    {
        return $this->config;
    }

    /*
    *@function set_config
    *@param object $config
    */
    public function set_config($config)
    {
        $this->config = $config;
    }

    /*
    *@function get_module
    *@param array|string $module
    *@param array|null $params
    *@param object|null $config
    *@return object
    */
    public function get_module($module, $params = NULL, $config = NULL)
    {
        $module_section = (is_array($module)) ? $module[1] : $module;
        $module_name = (is_array($module)) ? $module[0] : $module;
        $path = dirname(__FILE__) . '/../modules/' . $module_section . '/' . $module_name . '.class.php';
        if (file_exists($path)) {
            require_once($path);
            $config = ($config) ? $config : $this->get_config();
            eval(
                '$module = new ' . first_letter($module_name) . '($config, $params);'
            );
            return $module;
        } else {
            throw new Exception('No module!');
        }
    }

    /*
    *@function get_modules
    *@param array|string $module
    *@param array|null $params
    *@param array|null $order
    *@param array|null $limit
    *@param object|null $config
    *@return object
    */
    public function get_modules($module, $params = array(), $order = NULL, $limit = NULL, $config = NULL)
    {
        $config = ($config) ? $config : $this->get_config();
        $module_section = (is_array($module)) ? $module[1] : $module;
        $module_name = (is_array($module)) ? $module[0] : $module;
        $entities = $this->get_entities($config, 'module_' . $module_name, $params, $order, $limit);
        $result = array();
        $path = dirname(__FILE__) . '/../modules/' . $module_section . '/' . $module_name . '.class.php';
        if (file_exists($path)) {
            require_once($path);
            $class_name = first_letter($module_name);
            for ($i = 0; $i < count($entities); $i++) {
                eval('$result[$i] = new ' . $class_name . '($config);');
                $result[$i]->set_properties($entities[$i]->get_properties());
            }
            return $result;
        } else {
            throw new Exception('No module!');
        }
    }

    /*
    *@function get_modules_count
    *@param string $module_name
    *@param array|null $params
    *@param object|null $config
    *@return integer
    */
    public function get_modules_count($module_name, $params = array(), $config = NULL)
    {
        $config = ($config) ? $config : $this->get_config();
        return $this->get_entities_count($config, 'module_' . $module_name, $params);
    }

    /*
    *@function load_module
    *@param string $module_name
    *@param string $module_action
    *@param variant|null $param
    *return string;
    */
    public function load_module($module_name, $module_action, $param = NULL)
    {
        $module = $this->get_module($module_name);
        return $module->run_controller($module_action, $param);
    }

    /*
    *@function get_request
    *@param string $param_name
    *@param array|string|null $request_method
    *return string;
    */
    public function get_request($param_name, $request_method = NULL)
    {
        $result = NULL;
        if ($request_method) {
            if (is_array($request_method)) {
                foreach ($request_method as $key => $method) {
                    eval('$result = $_' . strtoupper($method) . '[$param_name];');
                    if (isset($result)) {
                        return $result;
                    }
                }
            } else {
                eval('$result = $_' . strtoupper($request_method) . '[$param_name];');
            }
        } else {
            $result = (isset($_GET[$param_name])) ? $_GET[$param_name] : $_POST[$param_name];
        }
        return $result;
    }
}

?>