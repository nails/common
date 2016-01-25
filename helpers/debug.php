<?php

/**
 * This file provides debug related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;
use Nails\Environment;

if (!function_exists('dumpanddie')) {

    /**
     * Alias to dump($mVar, true)
     * @param  mixed $mVar The variable to dump
     * @return void
     */
    function dumpanddie($mVar = null)
    {
        dump($mVar, true);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dump')) {

    /**
     * Dumps data, similar to var_dump()
     * @param  mixed   $mVar The variable to dump
     * @param  boolean $bDie Whether to kill the script
     * @return void
     */
    function dump($mVar = null, $bDie = false)
    {
        if (is_string($mVar)) {

            $sOut = '<pre>(string) ' . $mVar . '</pre>';

        } elseif (is_int($mVar)) {

            $sOut = '<pre>(int) ' . $mVar . '</pre>';

        } elseif (is_bool($mVar)) {

            $mVar = ($mVar === true) ? "true" : "false" ;
            $sOut = '<pre>(bool) ' . $mVar . '</pre>';

        } elseif (is_float($mVar)) {

            $sOut = '<pre>(float) ' . $mVar . '</pre>';

        } elseif (is_null($mVar)) {

            $sOut = '<pre>(null) null</pre>';

        } else {

            $sOut = '<pre>' . print_r($mVar, true) . '</pre>';
        }

        /**
         * Check the environment; we only output/die in
         * non-production environments
         */

        if (Environment::not('PRODUCTION')) {

            //  Continue execution unless instructed otherwise
            if ($bDie !== false) {

                die("\n\n" . $sOut . "\n\n");
            }

            echo "\n\n" . $sOut . "\n\n";
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('here')) {

    /**
     * Outputs a 'here at date()' string using dumpanddie(); useful for debugging.
     * @param  mixed $mDump The variable to dump
     * @return void
     */
    function here($mDump = null)
    {
        $oNow = Factory::factory('DateTime');

        //  Dump payload if there
        if (!is_null($mDump)) {

            dump($mDump);
        }

        dumpanddie('Here @ ' . $oNow->format('H:i:s'));
    }
}

// --------------------------------------------------------------------------

if (!function_exists('lastquery')) {

    /**
     * Dumps the last known query
     * @param  boolean $bDie Whether to kill the script
     * @return void
     */
    function lastquery($bDie = true)
    {
        $oDb        = Factory::service('Database');
        $sLastQuery = $oDb->last_query();

        // --------------------------------------------------------------------------

        if ($bDie) {

            dumpanddie($sLastQuery);

        } else {

            dump($sLastQuery);
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('last_query')) {

    /**
     * Alias of lastquery()
     * @param  boolean $bDie Whether to kill the script
     * @return void
     */
    function last_query($bDie = true)
    {
        lastquery($bDie);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dumpjson')) {

    /**
     * Dumps the passed variable as a JSON encoded string, setting JSON headers
     * @param  mixed $mData The variable to dump
     * @return void
     */
    function dumpjson($mData)
    {
        header('Content-Type: application/json');
        echo json_encode($mData, JSON_PRETTY_PRINT);
        die();
    }
}
