<?php

/**
 * The class abstracts CodeIgniter's Uri class.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 * @todo        Remove dependency on CI
 */

namespace Nails\Common\Service;

/**
 * Class Uri
 *
 * @package Nails\Common\Service
 *
 * @property $keyval     = array();
 * @property $uri_string = '';
 * @property $segments   = array();
 * @property $rsegments  = array();
 *
 * @method filter_uri(&$str)
 * @method segment($n, $no_result = null)
 * @method rsegment($n, $no_result = null)
 * @method uri_to_assoc($n = 3, $default = [])
 * @method ruri_to_assoc($n = 3, $default = [])
 * @method assoc_to_uri($array)
 * @method slash_segment($n, $where = 'trailing')
 * @method slash_rsegment($n, $where = 'trailing')
 * @method segment_array()
 * @method rsegment_array()
 * @method total_segments()
 * @method total_rsegments()
 * @method uri_string()
 * @method ruri_string()
 */
class Uri
{
    /**
     * The CodeIgniter Uri object
     *
     * @var \CI_URI
     */
    private $oUri;

    // --------------------------------------------------------------------------

    /**
     * Uri constructor.
     */
    public function __construct()
    {
        $oCi        = get_instance();
        $this->oUri = $oCi->uri;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Uri class
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {
            return call_user_func_array([$this, $sMethod], $aArguments);
        } else {
            return call_user_func_array([$this->oUri, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Uri class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oUri->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Uri class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oUri->{$sProperty} = $mValue;
    }
}
