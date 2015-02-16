<?php

if (!function_exists('dumpanddie'))
{
    /**
     * Alias to dump($var, true)
     * @param  mixed $var The variable to dump
     * @return void
     */
    function dumpanddie($var = null)
    {
        dump($var, true);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dump'))
{
    /**
     * Dumps data, similar to var_dump()
     * @param mixed The variable to dump
     * @return void
     */
    function dump($var = null, $die = false)
    {
        if (is_string($var)) {

            $output = '<pre>(string) ' . $var . '</pre>';

        } elseif (is_int($var)) {

            $output = '<pre>(int) ' . $var . '</pre>';

        } elseif (is_bool($var)) {

            $var = ($var === true) ? "true" : "false" ;
            $output = '<pre>(bool) ' . $var . '</pre>';

        } elseif (is_float($var)) {

            $output = '<pre>(float) ' . $var . '</pre>';

        } elseif (is_null($var)) {

            $output = '<pre>(null) null</pre>';

        } else {

            $output = '<pre>' . print_r($var, true) . '</pre>';
        }

        /**
         * Check the global ENVIRONMENT setting. We only output/die in
         * non-production environments
         */

        if (ENVIRONMENT != 'PRODUCTION') {

            //  Continue execution unless instructed otherwise
            if ($die !== false) {

                die("\n\n" . $output . "\n\n");
            }

            echo "\n\n" . $output . "\n\n";
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('here'))
{
    /**
     * Outputs a 'here at date()' string using dumpanddie(); useful for debugging.
     * @param  mixed $dump The variable to dump
     * @return void
     */
    function here($dump = null)
    {
        $now = gmdate('H:i:s');

        //  Dump payload if there
        if (!is_null($dump)) {

            dump($dump);
        }

        dumpanddie('Here @ ' . $now);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('lastquery'))
{
    /**
     * Dumps the last known query
     * @param  boolean $die Whetehr to die or not
     * @return void
     */
    function lastquery($die = true)
    {
        $lastQuery = get_instance()->db->last_query();

        // --------------------------------------------------------------------------

        if ($die) {

            dumpanddie($lastQuery);

        } else {

            dump($lastQuery);
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('last_query'))
{
    /**
     * Alias of lastquery()
     * @param  boolean $die Whether to die or not
     * @return void
     */
    function last_query($die = true)
    {
        lastquery($die);
    }
}
