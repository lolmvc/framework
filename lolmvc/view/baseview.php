<?php

namespace Lolmvc\View;

/**
 * Interface that any templating engines or view objects must implement in
 * order for the framework to trigger the rendering of HTML.
 *
 * TODO: If the rendering of html is at the discretion of the controller should
 * we be enforcing even this much or should we allow them to create custom
 * views/templating engines as they see fit and trigger the rendering in their
 * own way in their custom base controller?
 *
 * @author  Matthew Wallace <matt@lolmvc.com>
 * @package Lolmvc\View
 */
interface BaseView {
    /**
     * The name of the function that should render the HTML and echo it.
     *
     * @access public
     * @return void
     */
    public function renderPage();
}
