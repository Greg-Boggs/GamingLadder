<?php
/*
*
* Module: abstract class, represents abstract module.
* @author Khramkov Ivan.
*
*/
require_once(dirname(__FILE__) . '/simple.class.php');

class Module extends Simple
{
    /*
    * Name of the module
    *@var string
    */
    protected $name;
    /*
    * Name of the section
    *@var string
    */
    protected $section;

    /*
    * Constructor
    *@param object $config
    *@param integer|null $id
    */
    function __construct($config, $params = array())
    {
        $table_name = 'module_' . $this->name;
        if (!isset($this->section)) {
            $this->section = $this->name;
        }
        parent::__construct($config, $table_name, $params);
    }

    /*
    *@function run_controller
    *@param string $controller_name
    *@param array $params
    *@return string
    */
    public function run_controller($controller_name, $params = array())
    {
        $path = dirname(__FILE__) . '/../modules/' . $this->section . '/controllers/' . $controller_name . '_controller.php';
        if (file_exists($path)) {
            require_once($path);
            eval('$controller = new ' . $controller_name . '_controller($this->get_config());');
            $controller->html->set_template_dir(dirname(__FILE__) . '/../modules/' . $this->section . '/templates/');
            $controller->run($params);
            return $controller->content;
        } else {
            throw new Exception('No controller!');
        }
    }
}

?>