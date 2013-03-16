<?php
namespace Lolmvc\Service;

/**
 * A templating engine that extends the BaseView.
 *
 * Variables may to be added to the view, the names of the view template and
 * the layout template should be set the same way that other variables are.
 * The predetermined names for these settings are 'viewName' and 'layoutName'.
 *
 * TODO: The classname was an application specific need in the original
 * application. The requirement to pass it to the constructor should be
 * replaced by setting it as a normal view variable in the app's controller.
 *
 * Example:
 *
 * <code>
 * $this->view = new \Lolmvc\Service\Template('mysite', $classShortName);
 * $this->view->pageTitle   = "My home page";
 * $this->view->pageHeading = "This is my home page";
 * $template->renderPage();
 * </code>
 *
 * TODO: Add information about how to structure template file locations and why
 * app name is required.
 *
 * TODO: Explain how multiple JavaScript files are handled
 *
 * Note: Original logic derived from Chad Emrys Minick's "Simple PHP template engine"
 *
 * @author	Matt Wallace <matt@lolmvc.com>
 * @author  Chad Emrys Minick
 * @link http://codeangel.org/articles/simple-php-template-engine.html
 * @package Lolmvc\Service
 */
class Template implements \Lolmvc\View\BaseView {
	/**
	 * Array containing "name" => value pairs for variables that will be used
	 * by the views/layouts.
	 *
	 * @var array
	 * @access private
	 */
    private $vars = array();

    /**#@+
     * @var string
     * @access private
     */

    /**
     * The name of the view template
     */
    private $viewName;

    /**
     * The name of the layout template
     */
    private $layoutName;

    /**
     * The name of the controller
     */
    private $controllerName;

    /**
     * The name of the application
     */
    private $appName;

    /**#@-*/

    /**
     * Constructor
     *
     * @param string $appName         Name of the application
     * @param string $controllerName
     * @access public
     * @return void
     */
	public function __construct($appName, $controllerName) {
        $this->controllerName = $controllerName;
        $this->appName = $appName;
        $this->vars['js'] = array();
	}

	/**
     * Allows setting of view variable values.
     *
     * Example:
     *
     * To set a value for a view variable named 'key' to the value 'value' ...
     *
     * <code>
     * $template->key = $value;
     * </code>
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
     * Allows retrieving of view variable values.
     *
     * Example:
     *
     * To print the value of a previously set view variable named 'key' ...
     *
     * <code>
     * echo $template->key;
     * </code>
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
	 * Renders and displays the HTML using the privided templates and the
     * variables that have been added to the templating engine.
	 *
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
