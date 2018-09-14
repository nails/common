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

use Nails\Environment;
use Nails\Factory;

if (!function_exists('dump')) {

    /**
     * Dumps data, similar to var_dump(), will dump each variable it is passed
     * @return void
     */
    function dump()
    {
        $sOut = '';

        //  Allow multiple values to be passed to dump()
        foreach (func_get_args() as $iKey => $mVar) {

            $sOut .= "\n\n";

            if (is_string($mVar)) {

                $sOut .= '(string) ' . $mVar;

            } elseif (is_int($mVar)) {

                $sOut .= '(int) ' . $mVar;

            } elseif (is_bool($mVar)) {

                $mVar = $mVar === true ? "true" : "false";
                $sOut .= '(bool) ' . $mVar;

            } elseif (is_float($mVar)) {

                $sOut .= '(float) ' . $mVar;

            } elseif (is_null($mVar)) {

                $sOut .= '(null) null';

            } elseif (!isCli()) {

                $sOut .= htmlentities(print_r($mVar, true));

            } else {

                $sOut .= print_r($mVar, true);
            }
        }

        $sOut = trim($sOut);

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

// --------------------------------------------------------------------------

if (!function_exists('d')) {

    /**
     * Alias to dump()
     * @return void
     */
    function d()
    {
        call_user_func_array('dump', func_get_args());
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dumpAndDie')) {

    /**
     * Calls dump() and immediately exits
     * @return void
     */
    function dumpAndDie()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dd')) {

    /**
     * Alias to dumpAndDie()
     * @return void
     */
    function dd()
    {
        call_user_func_array('dumpAndDie', func_get_args());
    }
}

// --------------------------------------------------------------------------

if (!function_exists('here')) {

    /**
     * Outputs a 'here at date()' string using dumpAndDie(); useful for debugging.
     *
     * @param  mixed $mDump The variable to dump
     *
     * @return void
     */
    function here($mDump = null)
    {
        $oNow = Factory::factory('DateTime');

        //  Dump payload if there
        if (!is_null($mDump)) {
            dump($mDump);
        }

        dumpAndDie('Here @ ' . $oNow->format('H:i:s'));
    }
}

// --------------------------------------------------------------------------

if (!function_exists('lastQuery')) {

    /**
     * Dumps the last known query
     *
     * @param  boolean $bDie Whether to kill the script
     *
     * @return void
     */
    function lastQuery($bDie = true)
    {
        $oDb        = Factory::service('Database');
        $sLastQuery = $oDb->last_query();

        // --------------------------------------------------------------------------

        if ($bDie) {
            dumpAndDie($sLastQuery);
        } else {
            dump($sLastQuery);
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('last_query')) {

    /**
     * Alias of lastQuery()
     *
     * @param  boolean $bDie Whether to kill the script
     *
     * @return void
     */
    function last_query($bDie = true)
    {
        lastquery($bDie);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dumpJson')) {

    /**
     * Dumps the passed variable as a JSON encoded string, setting JSON headers
     *
     * @param  mixed $mData The variable to dump
     *
     * @return void
     */
    function dumpJson($mData)
    {
        header('Content-Type: application/json');
        echo json_encode($mData, JSON_PRETTY_PRINT);
        die();
    }
}
