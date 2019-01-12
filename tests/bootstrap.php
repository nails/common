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
     * We create an oject here  so that we can leverage the constructor
     * to set up the environment, and the destructor to tear it down.
     */
    $oTesting = new \Nails\Testing($sEntryPoint);

    /*
     *---------------------------------------------------------------
     * Nails Bootstrapper
     *---------------------------------------------------------------
     */
    \Nails\Bootstrap::run($sEntryPoint);


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

    \Nails\Functions::define('NAILS_CI_SYSTEM_PATH', realpath(dirname(__FILE__) . '/../vendor/codeigniter/framework/system') . '/');
    \Nails\Functions::define('BASEPATH', NAILS_CI_SYSTEM_PATH);
}