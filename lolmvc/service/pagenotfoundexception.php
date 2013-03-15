<?php

namespace Lolmvc\Service;

/**
 * Exception that can be thrown by almost any entitiy in the MVC to notify its
 * controller that a 404 has occured.
 *
 * @author Matt Wallace <matt@lolmvc.com>
 * @package Service
 *
 */
class PageNotFoundException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}
