<?php

namespace Skel\Controller;

/**
 * The main (and only) controller for the Skel app.
 *
 * @uses SkelController
 * @author Matt Wallace <matt@lolmvc.com>
 * @package Skel\Controller
 * @defaultAction home
 */
class Main extends SkelController {
    /**
     * Constructor
     *
     * @param string $appName
     * @param string $action
     * @param array $args
     * @access public
     * @return void
     */
    public function __construct($appName, $action, $args) {
        parent::__construct($appName, $action, $args);
    }

    /**
     * The only action for the main controller of the Skell app
     *
     * @param array $args
     * @access public
     * @return void
     * @action
     * @args []
     */
    public function home($args) {
        // set view variables
        $this->view->viewName = "main";
        $this->view->title    = "Skel main page";
    }
}
