<?php

/**
 * The class abstracts CI's Zip class.
 *
 * @todo - remove dependency on CI
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

/**
 * Class Zip
 * @package Nails\Common\Service
 */
class Zip
{
    /**
     * The zip object
     * @var \CI_Zip
     */
    private $oZip;

    // --------------------------------------------------------------------------

    /**
     * Zip constructor.
     */
    public function __construct()
    {
        $oCi = get_instance();
        $oCi->load->library('zip');
        $this->oZip = $oCi->zip;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Zip class
     *
     * @param  string $sMethod The method being called
     * @param  array $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {
            return call_user_func_array([$this, $sMethod], $aArguments);
        } else {
            return call_user_func_array([$this->oZip, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Zip class
     *
     * @param  string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oZip->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Zip class
     *
     * @param  string $sProperty The property to set
     * @param  mixed $mValue The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oZip->{$sProperty} = $mValue;
    }
}
