<?php

namespace Lolmvc\Controller;

/**
 * Controller to display 404 errors
 *
 * @uses Controller
 * @author Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc\Controller
 * @defaultAction error
 */
class Error404 extends BaseController {
    /**
     * Constructor
     *
     * @param string $appName
     * @param string $action
     * @param string $args
     * @access public
     * @return void
     */
    public function __construct($appName, $action, $args) {
        // get the class name
        $className = explode('\\', strtolower(get_class($this)));
        $this->classShortName = end($className);

        parent::__construct($appName, $this->classShortName);

        // create the view
        $this->view = new \Lolmvc\Service\Template($appName, $this->classShortName);
        $this->view->layoutName = "main";

        $this->controllerName = $this->classShortName;

        $this->$action($args);
    }

    /**
     * The only action for the Error404 controller.
     *
     * Handles displaying the 404 error to the user.
     *
     * @param array $args
     * @access public
     * @return void
     * @action
     * @args []
     * @args ["messages"]
     */
    public function error($args) {
        // set the view
        $this->view->viewName = 'error404';
        $this->view->title = 'Page Not Found';

        if (DEBUG && !empty($args))
            $this->view->messages = $args;

        // send the 404 header
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    }
}
