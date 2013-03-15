<?php

namespace Lolmvc\Controller;

/**
 * Controller to display 404 errors
 *
 * @uses Controller
 * @author Matt Wallace <matt@lolmvc.com>
 * @package lolmvc\Controller
 * @defaultAction error
 */
class Error404 extends Controller {
    public function __construct($appname, $action, $args) {
        parent::__construct($appname, $action, $args);
        // set the layout
        $this->setLayout('main');

        //execute the action
        $this->invokeAction($this);
    }

    /**
     * The only action for the Error404 controller.  Handles displaying the 404
     * error to the user.
     *
     * @param array $args
     * @access private
     * @return void
     * @action
     * @args []
     * @args ["messages"]
     */
    public function error($args) {
        // set the view
        $this->setView('error404');
        $this->addViewVar('title', 'Page Not Found');

        if (DEBUG_ON && !empty($args))
            $this->addViewVar('messages', $args);

        // send the 404 header
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    }
}
