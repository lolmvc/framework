<?php

/**
* Defines constants that need to be accessible throughout the
* application
*
* @author Mitzip <mitzip@lolmvc.com>
* @package MVC
*/
class Config {
    /**
    * Constructor
    *
    * @access public
    * @return void
    */
    public function __construct() {

        /* ==============================
         * Locale information
         * ============================== */
        define('TIMEZONE', 'America/Chicago');
        define('LOCALE_CATEGORY', LC_ALL);
        define('LOCALE_STRING', 'en_US');

        /* ==============================
         * Debug flag (true/false)
         * ============================== */
        define('DEBUG', false);

        define('CUSTOM_404', false);

        define('TEMPLATE_BASE', realpath(dirname(__FILE__)));
    }
}
