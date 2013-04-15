<?php

namespace Skel\Controller;

/**
 * The main (and only) controller for the Skel app.
 *
 * @uses SkelController
 * @author Matt Wallace <matt@lolmvc.com>
 * @author mitzip <mitzip@lolmvc.com>
 * @package Skel
 * @subpackage Controller
 * @defaultAction home
 * @noModel
 */
class Main extends \Lolmvc\Controller\Base {
   /**
   * Constructor
   *
   * @param string $appName
   * @param object $meta Annotation Class
   * @param string $action
   * @param array $args
   * @access public
   * @return void
   */
   public function __construct($appName, $meta, $action, $args) {
      parent::__construct($appName, $meta, $action, $args);
   }

   /**
   * The only action for the main controller of the Skell app
   *
   * @param array $args
   * @access public
   * @return void
   * @action
   * @args []
   */
   public function home($args) {
      // create the view  (using the supplied templating engine for this example)
      $this->view = new \Lolmvc\Service\Template(strtolower($this->appName), strtolower($this->className));

      // add base variables to the view
      $this->view->layoutName = 'main';
      $this->view->controller = $this->className;

      // set view variables
      $this->view->viewName = "main";
      $this->view->title    = "Skel main page";
   }
}
