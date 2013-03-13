<?php
/********************************
 * create a new lolmvc based app
 *******************************/
$skel = new \Lolmvc\App('skel');

/********************************
 * set any optional configuration
 *******************************/
// set flag that a local config should be loaded 
$skel->useLocalConfig();

/********************************
 * run the app
 *******************************/
$skel->run();
