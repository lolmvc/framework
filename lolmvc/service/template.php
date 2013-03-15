<?php
namespace Lolmvc\Service;

/**
 * Template, a simple templating engine to aid in clean separation of presentation
 * logic from application logic.
 *
 * After creating an instance of the Template class you can use it to generate
 * your html by calling the render method.
 *
 * <code>
 * $template = new \Assets\Template();
 * $renderedHTML = $template->render($viewClass, $templateClass);
 * echo $renderedHTML;
 * </code>
 *
 * @author	Matt Wallace <matt@lolmvc.com>
 * @author  Chad Emrys Minick
 * @link http://codeangel.org/articles/simple-php-template-engine.html
 * @package Lolmvc
 */
class Template {
	/**
	 * Array containing "name" => value pairs for variables that will be used
	 * by the views/layouts.
	 *
	 * @var array
	 * @access private
	 */
	private $vars = array();			// Array that holds the values needed by the views

	private $controllerName;

	public function __construct($controllerName) {
		$this->controllerName = $controllerName;
	}

	/**
	 * Allows retrieving of values from {@link vars} using the form <code>echo
	 * $template->key;</code>
	 *
	 * @param string $name
	 * @access public
	 * @return mixed
	 */
	public function &__get($name) {
		return $this->vars[$name];
	}

	/**
	 * Allows setting values in {@link vars} using the form <code>$template->key = $value;</code>
	 *
	 * @param string $name
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function __set($name, $value) {
		if ($name == 'viewName'   ||
			$name == 'layoutName' ||
			$name == 'content'
		) trigger_error("template.php: Cannot bind variable", E_USER_ERROR);

		$this->vars[$name] = $value;
	}

	/**
	 * Renders all the HTML
	 *
	 * @param string $viewName Name of the view class to instantiate
	 * @param string $layoutName Name of the layout class to instantiate
	 * @access public
	 * @return void
	 */
    public function render($viewName, $layoutName) {
		if (array_key_exists('viewName'  , $this->vars) ||
			array_key_exists('layoutName', $this->vars) ||
			array_key_exists('content'   , $this->vars)
		) trigger_error("template.php: Cannot bind variable", E_USER_ERROR);

		extract($this->vars);

        ob_start();
        if ($this->view != 'error404')
            include("view/$this->controllerName/$viewName.php");
        else {
            include("view/$viewName.php");
        }

        $content = ob_get_clean();

        ob_start();
        include("layout/$layoutName.php");
        return ob_get_clean();
	}
}
