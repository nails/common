<?php

/**
 * ---------------------------------------------------------------
 * NAILS TESTING STACK
 * ---------------------------------------------------------------
 *
 * This is the kick off point for the Nails testing stack
 * Documentation: https://nailsapp.co.uk
 */

/**
 * If the environment is set to TESTING then we're testing from the app
 * and it is using this file to bootstrap the tests, otherwise this
 * module itself is being tested.
 */

use \Nails\Bootstrap;
use \Nails\Testing;

if (!empty($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'TESTING') {

    /*
     *---------------------------------------------------------------
     * Autoloader
     *---------------------------------------------------------------
     */
    $sEntryPoint = realpath(__DIR__ . '/../../../..') . '/index.php';
    require_once dirname($sEntryPoint) . '/vendor/autoload.php';

    /*
     *---------------------------------------------------------------
     * Nails Testing Environment
     *---------------------------------------------------------------
     * We create an object here  so that we can leverage the constructor
     * to set up the environment, and the destructor to tear it down.
     */
    $oTesting = new Testing($sEntryPoint);

    /*
     *---------------------------------------------------------------
     * Nails Bootstrapper
     *---------------------------------------------------------------
     */
    Bootstrap::run($sEntryPoint);


    /*
     *---------------------------------------------------------------
     * Pre-test hook
     *---------------------------------------------------------------
     */
    $oTesting->setUp();

} else {

    /*
     *---------------------------------------------------------------
     * Autoloader
     *---------------------------------------------------------------
     */
    require 'vendor/autoload.php';

    Bootstrap::setEntryPoint(dirname(__FILE__));
    Bootstrap::setBaseDirectory(dirname(__FILE__));
    Bootstrap::setNailsConstants();
    Bootstrap::setCodeIgniterConstants(
        realpath(dirname(__FILE__) . '/../vendor/codeigniter/framework/system'),
        realpath(dirname(__FILE__) . '/../vendor/codeigniter/framework/application')
    );
}
