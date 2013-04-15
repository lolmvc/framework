<?php

namespace Skel\Controller;

/**
 * The front controller of the Skel application.
 *
 * @author Matthew Wallace <matt@lolmvc.com>
 * @author mitzip <mitzip@lolmvc.com>
 * @package Skel
 * @subpackage Controller
 */

// have to include the basic app class
require '../../lolmvc/app.php';

/* ============================
 * create a new lolmvc based app
 * ============================ */
$skel = new \Lolmvc\App('skel');

/* ==============================
 * set any optional configuration
 * ============================== */
// Load a local configuration
$skel->useLocalConfig();

/* ==============================
 * run the app
 * ============================== */
$skel->run();
