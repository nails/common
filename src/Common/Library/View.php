<?php

/**
 * The class provides a convenient way to load views
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Library;

class View
{
    /**
     * Reference to the CI super object
     * @var \CI_Controller
     */
    protected $oCi;

    // --------------------------------------------------------------------------

    /**
     * An array of data which is passed to the views
     * @var array
     */
    protected static $aData = [];

    // --------------------------------------------------------------------------

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->oCi = get_instance();
    }

    // --------------------------------------------------------------------------

    /**
     * Get an item from the view data array
     *
     * @param string $sKey The key to retrieve
     *
     * @return array|mixed|null
     */
    public static function getData($sKey = null)
    {
        if (is_null($sKey)) {
            return static::$aData;
        } elseif (array_key_exists($sKey, static::$aData)) {
            return static::$aData[$sKey];
        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Add an item to the view data array, or update an existing item
     *
     * @param string $mKey   The key to set
     * @param mixed  $mValue The value to set
     *
     * @throws \Exception
     */
    public static function setData($mKey = null, $mValue = null)
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mSubValue) {
                static::setData($sKey, $mSubValue);
            }
        } elseif (is_string($mKey) || is_numeric($mKey)) {
            static::$aData[$mKey] = $mValue;
        } else {
            throw new \Exception('Key must be a string or a numeric');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unset an item from the view data array
     *
     * @param string $sKey The key to unset
     */
    public static function unsetData($sKey)
    {
        if (is_array($sKey)) {
            foreach ($sKey as $sSubKey) {
                static::unsetData($sSubKey);
            }
        } else {
            unset(static::$aData[$sKey]);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a view
     *
     * @param string $sView   The view to load
     * @param array  $aData   Data to pass to the view
     * @param bool   $bReturn Whether to return the view or not
     *
     * @return mixed
     */
    public function load($sView, $aData = [], $bReturn = false)
    {
        $aData = array_merge(static::getData(), (array) $aData);

        if (!$bReturn) {
            $this->oCi->load->view($sView, $aData, $bReturn);
            return $this;
        } else {
            return $this->oCi->load->view($sView, $aData, $bReturn);
        }
    }
}
