<?php

/**
 * The class abstracts CI's Typography class.
 *
 * @todo (Pablo - 2018-04-18) - Remove dependency on CI
 *
 * @package                   Nails
 * @subpackage                common
 * @category                  Service
 * @author                    Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

class Typography
{
    /**
     * The CI_Typography object
     * @var \CI_Typography
     */
    private $oTypography;

    // --------------------------------------------------------------------------

    /**
     * FormValidation constructor.
     */
    public function __construct()
    {
        require_once FCPATH . 'vendor/codeigniter/framework/system/libraries/Typography.php';
        $this->oTypography = new \CI_Typography();
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter FormValidation class
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
            return call_user_func_array([$this->oTypography, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter FormValidation class
     *
     * @param  string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oTypography->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter FormValidation class
     *
     * @param  string $sProperty The property to set
     * @param  mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oTypography->{$sProperty} = $mValue;
    }
}
