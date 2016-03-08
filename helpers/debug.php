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
     * Alias to dump()
     * @return void
     */
    function dumpanddie()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dump')) {

    /**
     * Dumps data, similar to var_dump(), will dump each variable it is passed
     * @return void
     */
    function dump()
    {
        //  Allow multiple values to be passed to dump()
        foreach (func_get_args() as $iKey => $mVar) {

            if (is_string($mVar)) {

                $sOut = '(string) ' . $mVar;

            } elseif (is_int($mVar)) {

                $sOut = '(int) ' . $mVar;

            } elseif (is_bool($mVar)) {

                $mVar = $mVar === true ? "true" : "false" ;
                $sOut = '(bool) ' . $mVar;

            } elseif (is_float($mVar)) {

                $sOut = '(float) ' . $mVar;

            } elseif (is_null($mVar)) {

                $sOut = '(null) null';

            } else {

                $sOut = print_r($mVar, true);
            }

            /**
             * Check the environment; we only output/die in
             * non-production environments
             */

            if (Environment::not('PRODUCTION')) {

                //  If we're not on the CLI then wrap in <pre> tags
                if (!isCli()) {
                    $sOut = '<pre>' . $sOut . '</pre>';
                }

                echo "\n\n" . $sOut . "\n\n";
            }
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
