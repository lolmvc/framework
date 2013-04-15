<?php

namespace Lolmvc\Controller;

/**
 * Abstract class that provides the methods and properties that all
 * controllers will need to inherit.
 *
 * Each app should utilize an abstract base controller class that extends this
 * one. Such an app level base controller can encapsulate all the application
 * specific properties and methods that you want available to all controllers
 * within your app.
 *
 * @abstract
 * @author  Mitzip <mitzip@lolmvc.com>
 * @author  Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc
 * @subpackage Controller
 */
abstract class Base {

    /**
     * Name of application using lolmvc framework.
     *
     * Should be the app directory name.
     *
     * @access protected
     */
    protected $appName;

    /**
     * The model object providing API that accesses the Database.
     *
     * @var Lolmvc\Model\Base
     * @access protected
     */
    protected $model;

    /**
     * The view object that interfaces with the chosen view/templating
     * framework.
     *
     * @var Lolmvc\View\Base
     * @access protected
     */
    protected $view;

    /**
     * Class Name
     *
     * The short class name, that is, without namespace.
     *
     * @var string
     * @access protected
     */
    protected $className;

    /**
     * Class metadata from annotations class.
     *
     * Annotations class from router for this class.
     *
     * @var MattRWallace\Exegesis\Annotation
     * @access protected
     */
    protected $meta;

    /**
     * Constructor
     *
     * @access public
     * @param string $appName		The application name
     * @param object $annotationClass	Exegesis annotation object
     * @param string $action		Method to call from router, if any
     * @param array  $args		Arguments, from url, for method, if any
     * @return void
     *
     */
    public function __construct($appName, $annotationClass, $action, $args) {
	// fully qualified class name
	$fqcn = get_class($this);

	// array containing elements of fqcn divided by backslash
	$parsedFQCN = explode('\\', $fqcn);

	// grab class name from end of full qualified class name
	$this->className = end($parsedFQCN);

	// set application name
	$this->appName = $appName;

	// make annotations class available to methods
	$this->meta = $annotationClass;

	// no model?
	$noModel = $this->meta->hasAnnotation('@nomodel')
	    ?: $this->meta->hasAnnotation('@noModel');

	// if model is needed
	$model = "\\$this->appName\\Model\\$this->className";
	$this->model = $noModel ? false : new $model();

	// execute the action
        $this->$action($args);
	$this->renderPage();
    }

    /**
     * Renders and echoes the HTML
     *
     * @see \Lolmvc\View\Base
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
