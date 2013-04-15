<?php

namespace Lolmvc\Service;

/**
 * Determines which controller is requested in the URI and creates an
 * instance of it.
 *
 * The remainder of the URI (Action and params) are
 * passed to the controller's constructor so that the constructor can
 * determine the appropriate actions.
 *
 * Example:
 *
 * The following code is how the App object handles routing and status 404
 * responses.
 *
 * <code>
 * try {
 *    $router = new \Lolmvc\Service\Router($_SERVER['REQUEST_URI'], 'Skel');
 * } catch (\Lolmvc\Service\PageNotFoundException $e) {
 *    // get the error404 classname
 *    if (CUSTOM_404)
 *       $error404namespace = $appname;
 *    else
 *       $error404namespace = "Lolmvc";
 *
 *    $request = "PathNotFound";
 *    $message = $e->getMessage();
 *    if (!empty($message))
 *       $request .= "/error/$message/";
 *    $router = new
 *    \Lolmvc\Service\Route($request, $error404namespace);
 * }
 * </code>
 *
 * @author  Matthew Wallace <matt@lolmvc.com>
 * @author  Mitzip <mitzip@lolmvc.com>
 * @package Lolmvc
 * @subpackage Service
 * @todo re-evaluate usage of error pages
 */
class Route {
	/**
	 * Constructor
	 *
	 * @param mixed $uri
	 * @param string $appName
	 * @access public
	 * @return void
	 */
	public function __construct($uri, $appName) {

		// remove any query string from URI
		$uri = strtok($uri, '?') ?: $uri;

		// remove leading/trailing '/' and explode
		$uri = trim($uri, '/');

		// parse URI into array, splitting on forward slash
		$parsedURI = explode('/', $uri);

		// grab requested controller name, if blank then use the default (if any)
		$controllerName = empty($parsedURI[0]) ? ucfirst(DEFAULT_CONTROLLER) : ucfirst($parsedURI[0]);
	    $controllerFQCN = "\\$appName\\Controller\\$controllerName";

		// Check early if the controller is valid
		if (!class_exists($controllerFQCN)) {
			throw new PageNotFoundException("Controller class does not exist.");
		}
		// set the parser to ignore phpdoc annotations
		\MattRWallace\Exegesis\AnnotationParser::setBlacklist(
			\MattRWallace\Exegesis\AnnotationParser::PHPDOC);

		// create the AnnotationClass for the controller and get the actions
		$annotationClass = new \MattRWallace\Exegesis\AnnotationClass($controllerFQCN);

		// set action if there are any in URI
		if (isset($parsedURI[1])) {
			$action = $parsedURI[1];

			$actionsFromAnnotation = $this->getActions($annotationClass);

			// if the action is in the list then set its name and get args
			foreach ($actionsFromAnnotation as $annotationAction) {
				if ($annotationAction->getName() == $action) {
					$action = $annotationAction;
					$args   = array_slice($parsedURI, 2);
					break;
				}
			}
		}
	    // if no action passed to router, check for default action
		else if ($annotationClass->hasAnnotation('@defaultAction')) {
			$action = $this->getDefaultAction($annotationClass);
			$args   = array_slice($parsedURI, 1);
		}

		// invalid action passed to router
		if (!is_object($action)) {
			throw new PageNotFoundException("Invalid action");
		}

		// if no args were specified by action method, no way to route
		if (!($argLists = $action->getAnnotationValue('@args'))) {
			throw new PageNotFoundException("No argument lists specified");
		}

		// iterate through argument lists trying to find a match to args passed
		foreach ($argLists as $arglist) {
			if ((count($arglist) < count($args) && in_array(null, $arglist)) ||
				count($arglist) == count($args)) {
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

		// no arguments passed to router, throw back a 404
		if (!isset($parameters)) {
			throw new PageNotFoundException("No argument lists matched the provided arguments");
		}


		// finally attempt to create the controller
		try {
			new $controllerFQCN($appName, $annotationClass, $action->getName(), $parameters);
		} catch (PageNotFoundException $e) {
			throw new PageNotFoundException("Failed to create the controller (".$e->getMessage().")");
		}
	}

    /**
     * Helper function to return a list of actions for the selected class.
     *
     * @param AnnotationClass $annotationClass
     * @access private
     * @see \MattRWallace\Exegesis\AnnotationClass
     * @return Array $actions
     */
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

    /**
     * Returns the default action if there is one, if not an empty string
     * is returned.
     *
     * @param AnnotationClass $annotationClass
     * @access public
     * @return AnnotationMethod The default action
     * @see \MattRWallace\Exegesis\AnnotationClass
     * @see \MattRWallace\Exegesis\AnnotationMethod
     */
	public function getDefaultAction($annotationClass) {
		$action = $annotationClass->getAnnotationValue('@defaultAction')[0];
		try {
			$action = $annotationClass->getMethod($action);
		} catch (\ReflectionException $e) {
			return '';
		}

		return new \MattRWallace\Exegesis\AnnotationMethod($annotationClass->getName(), $action->getName());
	}
}
