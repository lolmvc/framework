<?php

namespace Lolmvc\Service;

/**
 * Exception that can be thrown at any point in the app to notify its
 * controller that a 404 has occured and trigger the 404 page to display.
 *
 * @author Matt Wallace <matt@lolmvc.com>
 * @package Lolmvc\Service
 */
class PageNotFoundException extends \Exception {
    /**
     * Constructor
     *
     * @param string $message
     * @access public
     * @return void
     */
    public function __construct($message) {
        parent::__construct($message);
    }
}
