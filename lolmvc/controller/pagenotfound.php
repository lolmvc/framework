<?php

namespace Lolmvc\Controller;

/**
 * Controller to display 404 errors
 *
 * @uses Controller
 * @author Matt Wallace <matt@lolmvc.com>
 * @author mitzip <mitzip@lolmvc.com>
 * @package Lolmvc
 * @subpackage Controller
 * @defaultAction error
 * @nomodel
 */
class PageNotFound extends Base {
    /**
     * Constructor
     *
     * @param string $appName
     * @param string $action
     * @param string $args
     * @access public
     * @return void
     */
    public function __construct($appName, $annotationClass, $action, $args) {
        parent::__construct($appName, $annotationClass, $action, $args);

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
         // create the view
        $this->view = new \Lolmvc\Service\Template($this->appName, $this->className);
        $this->view->layoutName = "main";
        // set the view
        $this->view->viewName = 'pagenotfound';
        $this->view->title = 'Page Not Found';

        if (DEBUG && !empty($args))
            $this->view->messages = $args;

        // send the 404 header
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    }
}
