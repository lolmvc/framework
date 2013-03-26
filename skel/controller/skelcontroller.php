<?php

namespace Skel\Controller;

/**
 * Application specific abstract controller for the Skel application
 *
 * @abstract
 * @uses Lolmvc\Controller\BaseController
 * @author Matt Wallace <matt@lolmvc.com>
 * @package Skel\Controller
 */
abstract class SkelController extends \Lolmvc\Controller\BaseController {

    /**
     * Constructor
     *
     * @param string $appName  Name of the application
     * @param string $action   Name of the action
     * @param array  $args     Array of the action arguments
     * @param string $layout   Layout name (default is "main")
     * @access public
     * @return void
     */
    public function __construct($appName, $action, $args, $layout="main") {
        parent::__construct($appName);

        // create the view  (using the supplied templating engine for this example)
        $this->view = new \Lolmvc\Service\Template($appName, $this->classShortName);

        // add base variables to the view
        $this->view->layoutName = $layout;
        $this->view->controller = $this->classShortName;

        // execute the action
        $this->$action($args);
        $this->renderPage();
    }
}
