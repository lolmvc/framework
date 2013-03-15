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
 * @author  mitzip <mitzip@lolmvc.com>
 * @author  Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc\Controller
 */
abstract class Controller {
	/**
	 * Holds the rendered HTML
	 *
	 * @var string
	 * @access private
	 */
	private $renderedHTML;

	/**
	 * The template object
	 *
	 * @var \Assets\Template
	 * @access private
	 */
	private $template;

	/**
	 * Name of the class family
	 *
	 * @var string
	 * @access private
	 */
	private $classShortName;

	/**
	 * The model object providing API that accesses the Database
	 *
	 * @var
	 * @access protected
	 */
	protected $model;

	/**
	 * The name of the class that provides the view.  Must be set by the inheriting
	 * class using function setView().
	 *
	 * @var string
	 * @access private
	 */
	private $viewName;

	/**
	 * The name of the class that provides the layout.  Must be set by the
	 * inheriting class using function setLayout().
	 *
	 * @var string
	 * @access private
	 */
	private $layoutName;

	/**
	 * The action that should be executed
	 *
	 * @var ReflectionMethod
	 * @access private
	 */
	private $action;

	/**
	 * The arguments that should be passed to the action when invoked
	 *
	 * @var array
	 * @access private
	 */
	private $arguments;

	/**
	 * Constructor
	 *
	 * @param \ReflectionMethod $action
	 * @param mixed $arguments
	 * @access public
	 * @return void
	 */
	public function __construct($appName, \ReflectionMethod $action, $arguments) {

		// get the class name
		$className = explode('\\', strtolower(get_class($this)));
		$this->classShortName = end($className);

		// get the model
		$modelName = "\\$appName\\Model\\$this->classShortName";
		$this->model = new $modelName();

		// prepare the template
		$this->template = new \Lolmvc\Service\Template($this->classShortName);
		$this->addViewVar("controllerClass", $this->classShortName);

		// store the action information
		$this->action = $action;
		$this->arguments = $arguments;
	}

	/**
	 * Invokes the render method of the templating engine to generate
	 * the page content.
	 *
	 * @access public
	 * @return void
	 */
	public function loadView() {
		if (empty($this->viewName))
			trigger_error('Each action must set a value for $viewName', E_USER_ERROR);
		if (empty($this->layoutName))
			trigger_error('Each action must set a value for $layoutName', E_USER_ERROR);
		$this->content = $this->template->render($this->viewName, $this->layoutName);
	}

	/**
	 * Echos the rendered HTML
	 *
	 * @access public
	 * @return void
	 */
	public function renderPage() {
		echo $this->content;
	}

	/**
	 * Sets the $viewName property with the requested view name
	 *
	 * @param string $view
	 * @access public
	 * @return void
	 */
	public function setView($view) {
		$this->viewName = $view;
	}

	/**
	 * Sets the $layoutName property with the requested layout name
	 *
	 * @param mixed $layout
	 * @access public
	 * @return void
	 */
	public function setLayout($layout) {
		$this->layoutName = "$layout";
	}

	/**
	 * Adds a variabe to the template to be used when rendering the HTML
	 *
	 * @param string $key
	 * @param mixed $var
	 * @access public
	 * @return void
	 */
	public function addViewVar($key, $var) {
		if ($key == "content")
			trigger_error("Cannot use 'content' as a view variable name", E_USER_ERROR);
		$this->template->$key = $var;
	}

	/**
	 * Invokes the action that was specified at the instantiation of the
	 * controller
	 *
	 * @param Controller $object
	 * @access protected
	 * @return void
	 */
	protected function invokeAction($object) {
		try {
			$this->action->invoke($object, $this->arguments);  //TODO: try/catch
		} catch (Lolmvc\Service\PageNotFoundException $e) {
		}
	}
}
