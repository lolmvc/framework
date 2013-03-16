<?php

/**
 * The index of the Skel application.
 *
 * @author Matthew Wallace <matt@lolmvc.com>
 * @package Skel\Webroot
 */

// have to include the basic app class
require_once("../../lolmvc/app.php");

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
