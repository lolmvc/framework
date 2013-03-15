<?php
namespace Lolmvc\Controller;

/**
 * Abstract class that provides the methods and properties that all
 * controllers will need to inherit.
 *
 * Concrete controller classes which inherit from Controller must set the
 * values of the private members $viewName and $layoutName by using the
 * protected setter methods for the class to be able to correctly render HTML.
 *
 * @abstract
 * @author  Mitzip <mitzip@lolmvc.com>
 * @author  Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc\Controller
 */
abstract class BaseController {

	/**
	 * The model object providing API that accesses the Database
	 *
	 * @var
	 * @access protected
	 */
    protected $model;

    /**
     * The view object that interfaces with the chosen view/templating
     * framework
     *
     * @var
     * @access protected
     */
    protected $view;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($appName, $classShortName) {
		// get the model
		$modelName = "\\$appName\\Model\\" . ucfirst($classShortName);
		$this->model = new $modelName();
    }

	/**
	 * Renders and echoes the HTML
     *
     * @see \View\View
	 * @access public
	 * @return void
	 */
    public function renderPage() {
        if (empty($this->view))
            trigger_error('The view was never set! See the documentation.', E_USER_ERROR);

		$this->view->renderPage();
	}
}
