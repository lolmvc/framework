<?php

namespace Lolmvc\Controller;

/**
 * Abstract class that provides the methods and properties that all
 * controllers will need to inherit.
 *
 * Each app should utilize an abstract base controller class that extends this
 * one.  Such an app level base controller can encapsulate all the application
 * specific properties and methods that you want available to all controllers
 * within your app.
 *
 * @abstract
 * @author  Mitzip <mitzip@lolmvc.com>
 * @author  Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc\Controller
 */
abstract class BaseController {

    /**
     * The model object providing API that accesses the Database.
     *
     * @var Lolmvc\Model\BaseModel
     * @access protected
     */
    protected $model;

    /**
     * The view object that interfaces with the chosen view/templating
     * framework.
     *
     * @var Lolmvc\View\BaseView
     * @access protected
     */
    protected $view;

    /**
     * classShortName
     *
     * @var string
     * @access protected
     */
    protected $classShortName;

    /**
     * Constructor
     *
     * @access public
     * @param string $appName         The application name.
     * @param string $classShortName  The non-fully-qualified class name.
     * @return void
     *
     */
    public function __construct($appName) {
        // get the class name
        $className = explode('\\', strtolower(get_class($this)));
        $this->classShortName = end($className);

	// get the model
	//$modelName = "\\$appName\\Model\\" . ucfirst($classShortName);
	//$this->model = new $modelName();
    }

    /**
     * Renders and echoes the HTML
     *
     * @see \Lolmvc\View\BaseView
     * @access public
     * @return void
     */
    public function renderPage() {
        if (empty($this->view)) {
	    trigger_error('The view was never set! See the documentation.', E_USER_ERROR);
	}

	$this->view->renderPage();
	}
}
