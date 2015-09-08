<?php

/**
 * The class provides a wrapper for Mustache so it can be loaded like a normal CI library
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class Mustache
{
    private $_mustachio;

    // --------------------------------------------------------------------------

    /**
     * Loads Mustache
     */
    public function __construct()
    {
        Mustache_Autoloader::register();
        $this->_mustachio = new Mustache_Engine;
    }

    // --------------------------------------------------------------------------

    /**
     * Routes all and any calls to this library to the Mustache library
     * @param  string $method      The method being called
     * @param  array  $arguments The arguments being passed to the method
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->_mustachio, $method), $arguments);
    }
}
