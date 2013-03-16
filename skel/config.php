<?php

namespace Skel;

/**
* Defines constants that need to be accessible throughout the
* application.
*
* @author Mitzip <mitzip@lolmvc.com>
* @author Matt Wallace <matt@lolmvc.com>
* @package Skel
*/
class Config {
    /**
    * Constructor
    *
    * @access public
    * @return void
    */
    public function __construct() {
        /* =========================
         * Default controller
         * ========================= */
        define('DEFAULT_CONTROLLER', 'main');
    }
}
