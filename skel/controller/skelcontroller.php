<?php

namespace Skel\Controller;

/**
 * Abstract controller for the Skel application
 *
 * @abstract
 * @uses \Lolmvc\Controller\BaseController
 * @author Matt Wallace <matt@lolmvc.com>
 * @package \Skel\Controller
 */
abstract class SkelController extends \Lolmvc\Controller\BaseController {
    protected $classShortName;

    public function __construct($appName, $action, $args, $layout="main") {
        // get the class name
        $className = explode('\\', strtolower(get_class($this)));
        $this->classShortName = end($className);

        parent::__construct($appName, $this->classShortName);

        // create the view  (using the supplied templating engine for this example)
        $this->view = new \Lolmvc\Service\Template($appName, $this->classShortName);

        // add base variables to the view
        $this->view->layoutName = $layout;
        $this->view->controller = $this->classShortName;

        // execute the action
        $this->$action($args);
    }
}