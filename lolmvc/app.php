<?php
namespace Lolmvc;

/**
 * Each application is able to create its front controller easily creating an App object which can then be "run".
 *
 * The root namespace of the application must be passed or the router will not be able to find your application specific code.
 *
 * Example:
 *
 * The following code should be in the index.php within your webroot for a
 * site/application
 *
 * <code>
 *   // Include the file with the App class.
 *   // Once the App object is constructed all your classes can be found and
 *   //   included by the autoloader.
 *   require_once("../../lolmvc/app.php");
 *
 *   // Create a new App with the name of the app.
 *   // The name of the app should be the same as the name of the folder and
 *   //   namespace for your app.
 *   $skel = new \Lolmvc\App('skel');
 *
 *   // Optional configuration can be done here.  See function defenitions
 *   //   below for the possibilities.
 *
 *   // Siginal the app to execute
 *   $skel->run();
 * </code>
 *
 * @author Mitzip <mitzip@lolmvc.com>
 * @author Matthew Wallace <matt@lolmvc.com>
 * @package Lolmvc
 */
class App {
    /**
     * The name of the app, should be the same as the app's namespace
     *
     * @var
     * @private
     */
    private $appName;

    /**
     * Constructor
     *
     * @param string $appName  The name of the application
     * @access public
     * @return void
     */
    function __construct($appName) {
        $this->appName = ucfirst($appName);

       /**
        * Initialize the built-in PHP class autoloader.
        */
        require 'service/autoloader.php';
        $loader = new Service\Autoloader([['MattRWallace\\Exegesis' => 'vendor']]);
        $loader->importComposerNamespaces()
            ->register();

        new \Config();
    }

    /**
     * Loads a local configuration file for the app.
     *
     * The format for the file is identical to the framework level
     * configuration but exists in the root of the application. See the
     * local configuration for the Skel example app for more information.
     *
     * @access public
     * @return void
     */
    public function useLocalConfig() {
        $configClass = "\\$this->appName\\Config";
        new $configClass();
    }

    /**
     * Called after all settings are complete and triggers the app to actually "run".
     *
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
     * Called as the first step of running the app.
     *
     * Helper function that sets the app state now that all configuration
     * values have been imported.
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
