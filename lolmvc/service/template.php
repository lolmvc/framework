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
 * @package MVC
 */
class Template implements \Lolmvc\View\View {
	/**
	 * Array containing "name" => value pairs for variables that will be used
	 * by the views/layouts.
	 *
	 * @var array
	 * @access private
	 */
    private $vars = array();			// Array that holds the values needed by the views
    private $viewName;
    private $layoutName;
    private $controllerName;
    private $appName;

	public function __construct($appName, $controllerName) {
        $this->controllerName = $controllerName;
        $this->appName = $appName;
        $this->vars['js'] = array();
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
        if ($name == 'viewName')
            return $this->viewName;
        if ($name == 'layoutName')
            return $this->layoutName;

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
		if ($name == 'content')
            trigger_error("template.php: Cannot use the variable 'content'", E_USER_ERROR);

        // catch the viewName
        if ($name == 'viewName')
            $this->viewName = $value;

        if ($name == 'layoutName')
            $this->layoutName = $value;

        // if javascript then push onto vars['js']
        if ($name == 'js')
            array_push($this->vars['js'], $value);
        else
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
    public function renderPage() {

        // change the working directory
        $oldcwd = getcwd();
        chdir(TEMPLATE_BASE);

        if (empty($this->viewName))
            trigger_error('No view name was set!', E_USER_ERROR);
        if (empty($this->layoutName))
            trigger_error('no layout name was set!', E_USER_ERROR);

        extract($this->vars);

        // Run through the view
        ob_start();
        include(lcfirst($this->appName)."/view/$this->controllerName/$this->viewName.php");
        $content = ob_get_clean();

        // Wrap with the layout
        ob_start();
        include(lcfirst($this->appName)."/layout/$this->layoutName.php");

        echo ob_get_clean();

        chdir($oldcwd);
    }
}
