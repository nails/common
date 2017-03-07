<?php

/**
 * The class abstracts CI's Config class.
 *
 * @todo        - remove dependency on CI
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Library;

class Config
{
    /**
     * The database object
     * @var \CI_Config
     */
    private $oConfig;

    // --------------------------------------------------------------------------

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $oCi           = get_instance();
        $this->oConfig = $oCi->config;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Config class
     *
     * @param  string $sMethod    The method being called
     * @param  array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {

            return call_user_func_array([$this, $sMethod], $aArguments);

        } else {

            return call_user_func_array([$this->oConfig, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Config class
     *
     * @param  string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oConfig->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Config class
     *
     * @param  string $sProperty The property to set
     * @param  mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oConfig->{$sProperty} = $mValue;
    }
}
