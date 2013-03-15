<?php
namespace Lolmvc;

/**
 * Each application is able to create its front controller easily creating an App object which can then be "run".
 *
 * The root namespace of the application must be passed or the router will not be able to find your application specific code.
 *
 * Example:
 * <code>
 *   $skel = new \Lolmvc\App('skel');
 *   $skel->run();
 * </code>
 *
 * @author Mitzip <mitzip@lolmvc.com>
 * @author Matthew Wallace <matt@lolmvc.com>
 * @package lolmvc
 */
class App {
    /**
     * The name of the app. Should be the same as the app's namespace
     *
     * @var
     * @private
     */
    private $appName;

    /**
     * Constructor
     *
     * @param string $appName
     * @access public
     * @return void
     */
    function __construct($appName) {
        $this->appName = ucfirst($appName);

        /**
        * Initialize the built-in PHP class autoloader.
         */
        // TODO: remove the include path modification when autoloader is done
        set_include_path(get_include_path() . PATH_SEPARATOR . realpath('../../') . PATH_SEPARATOR . realpath('../../vendor'));
        spl_autoload_extensions('.php');
        spl_autoload_register();

        // load configuration values
        new \Config();
    }

    /**
     * useLocalConfig
     *
     * Loads a local config for the app
     *
     * @access public
     * @return void
     */
    public function useLocalConfig() {
        $configClass = "\\$this->appName\\Config";
        new $configClass();
    }

    /**
     * run
     *
     * Called after all settings are complete and triggers the app to actually "run".
     * This means that the router created and generates the controller which allows us
     * to trigger the display of the rendered webpage.
     *
     * @access public
     * @return void
     */
    public function run() {
        $this->processSettings();

        // create the router, generate the page and display
        try {
            $router = new \Lolmvc\Service\Route($_SERVER['REQUEST_URI'], $this->appName);
		} catch (\Lolmvc\Service\PageNotFoundException $e) {
			// get the error404 classname
			if (CUSTOM_404)
				$error404namespace = $appname;
			else
                $error404namespace = "lolmvc";

            $request = "error404";
            $message = $e->getMessage();
            if (!empty($message))
				$request .= "/$message/";
            $router = new \Lolmvc\Service\Route($request, $error404namespace);
        }

        $controller = $router->getController();
        $controller->renderPage();
    }

    /**
     * processSettings
     *
     * Called as the first step of running the app.  Now that it is too late to change any
     * globally set configurations we can process all the settings.
     *
     * @access private
     * @return void
     */
    private function processSettings() {
        // Set Timezone
        date_default_timezone_set(TIMEZONE);

        // Set Locale
        setlocale(LOCALE_CATEGORY, LOCALE_STRING);

         // Debug settings
        if (DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_NOTICE);
            ini_set('display_errors', '1');
        }
    }
}
