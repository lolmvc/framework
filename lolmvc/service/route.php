<?php
namespace Lolmvc\Service;

/**
 * Determines which controller is requested in the URI and creates an
 * instance of it.  The remainder of the URI (Action and params) are
 * passed to the controller's constructor so that the onstructor can
 * determine the appropriate actions.
 *
 * Example:
 *
 * <code>
 * $router = new \Assets\Router($_SERVER['REQUEST_URI']);
 *
 * if ($controller = $router.getController()) {
 *    // utilize controller functionality
 * }
 * else {
 *    // handle error
 * }
 * </code>
 *
 * @author  Mitzip <mitzip@lolmvc.com>
 * @author  Matthew Wallace <matt@lolmvc.com>
 * @package MVC
 */
class Route {
	/**
	 * Holds the controller instance
	 *
	 * @var
	 * @access private
	 */
	private $controllerObject;

	/**
	 * Constructor
	 *
	 * @param mixed $uri
	 * @access public
	 * @return void
	 */
	public function __construct($uri, $appName) {

		/* =============================
		 *  Find the correct controller
		 * ============================= */

		// remove leading/trailing '/' and explode
		$uri = preg_replace('/^\//', '', $uri);
		$uri = preg_replace('/\/$/', '', $uri);
		$parsedURI = explode('/', $uri);

		// grab requested controller name, if blank then use the default (if any)
        $controller      = empty($parsedURI[0]) ? ucfirst(DEFAULT_CONTROLLER) : ucfirst($parsedURI[0]);
        $controllerClass = "\\$appName\\Controller\\$controller";

		// get the action or ''
		$action = isset($parsedURI[1]) ? $parsedURI[1] : '';
		$args   = '';

		// Check if the controller is valid
		try {
			if (class_exists($controllerClass))  {}
        } catch (\LogicException $e) {
			throw new PageNotFoundException("Controller class does not exist (".$e->getMessage().")");
		}


		/* =============================================
		 *  Get controller info and validate the action
		 * ============================================= */

		// set the parser to ignore phpdoc annotations
		\MattRWallace\Exegesis\AnnotationParser::setBlacklist(\MattRWallace\Exegesis\AnnotationParser::PHPDOC);

		// create the AnnotationClass for the controller and get the actions
		$annotationClass = new \MattRWallace\Exegesis\AnnotationClass($controllerClass);
		$actions = $this->getActions($annotationClass);

		// if the action is in the list then set its name and get args
		foreach ($actions as $anAction)
			if ($anAction->getName() == $action) {
				$action = $anAction;
				$args   = array_slice($parsedURI, 2);
				break;
			}

		// otherwise check for a default
		if (is_string($action) && $annotationClass->hasAnnotation('@defaultAction')) {
			$action = $this->getDefaultAction($annotationClass);
			$args   = array_slice($parsedURI, 1);
		}

		// Invalid action and no default, switch to Error404
		//if (is_string($action)) {
		if (!is_object($action)) {
			throw new PageNotFoundException("Invalid action with no default");
		}


		/* =====================
		 *  Map names to values
		 * ===================== */

		$parameters = null;

		// get the argument lists
		$argLists = $action->getAnnotationValue('@args');

		// if there are no argument lists then bail
		if (!isset($argLists)) {
			throw new PageNotFoundException("No argument lists specified");
		}

		foreach ($argLists as $arglist) {
			if (count($arglist) == count($args) ||
				(count($arglist) < count($args) && in_array(null, $arglist))
			) {
				// check for empty list
				if (empty($args))
					$parameters = [];
				else {
					foreach($arglist as $index => $arg) {
						if ($arg == null)
							$parameters['args'] = array_slice($args, $index);
						else
							$parameters[$arg] = $args[$index];
					}
				}
			}
		}

		// no match
		if (!is_array($parameters)) {
			throw new PageNotFoundException("No argument lists matched the provided arguments");
		}


		/* =======================
		 *  Create the controller
		 * ======================= */

		try {
			$this->controllerObject = new $controllerClass($appName, $action->getName(), $parameters);
		} catch (PageNotFoundException $e) {
			throw new PageNotFoundException("Failed to create the controller (".$e->getMessage().")");
		}
	}

	private function getActions($annotationClass) {
		// Get a list of public methods and determine which are actions
		$publics = $annotationClass->getMethods(\ReflectionMethod::IS_PUBLIC);

		$actions = [];

		foreach ($publics as $public) {
			if ($public->hasAnnotation('@action'))
				array_push($actions, $public);
		}
		return $actions;
	}

	public function getDefaultAction($annotationClass) {
		$action = $annotationClass->getAnnotationValue('@defaultAction')[0];
		try {
			$action = $annotationClass->getMethod($action);
		} catch (\ReflectionException $e) {
			return '';
		}

		return new \MattRWallace\Exegesis\AnnotationMethod($annotationClass->getName(), $action->getName());
	}

	/**
	 * Returns the Controller instance
	 *
	 * @access public
	 * @return Controller
	 */
	public function getController() {
		return $this->controllerObject;
	}
}
