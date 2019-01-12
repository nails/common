<?php

/**
 * The class abstracts CodeIgniter's Output class.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 * @todo        Remove dependency on CI
 */

namespace Nails\Common\Service;

use Nails\Factory;

class Output
{
    /**
     * The CodeIgniter Output object
     *
     * @var \CI_Output
     */
    private $oOutput;

    // --------------------------------------------------------------------------

    /**
     * Output constructor.
     */
    public function __construct()
    {
        $oCi           = get_instance();
        $this->oOutput = $oCi->output;

        // --------------------------------------------------------------------------

        //  If a display method has been defined, configure CodeIgniter to use it
        if (method_exists($this, 'display')) {
            get_instance()->hooks->addHook(
                'display_override',
                [
                    'classref' => $this,
                    'method'   => 'display',
                    'params'   => [],
                ]
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Output class
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
            return call_user_func_array([$this->oOutput, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Output class
     *
     * @param  string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oOutput->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Output class
     *
     * @param  string $sProperty The property to set
     * @param  mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oOutput->{$sProperty} = $mValue;
    }
}
