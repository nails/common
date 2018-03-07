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

use Nails\Factory;

class View
{
    /**
     * An array of data which is passed to the views
     * @var array
     */
    protected $aData = [];

    // --------------------------------------------------------------------------

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->aData = &getControllerData();
    }

    // --------------------------------------------------------------------------

    /**
     * Get an item from the view data array
     *
     * @param string $sKey The key to retrieve
     *
     * @return array|mixed|null
     */
    public function getData($sKey = null)
    {
        if (is_null($sKey)) {
            return $this->aData;
        } elseif (array_key_exists($sKey, $this->aData)) {
            return $this->aData[$sKey];
        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Add an item to the view data array, or update an existing item
     *
     * @param string|array $mKey   The key, or keys (in a key value pair), to set
     * @param mixed        $mValue The value to set
     *
     * @throws \Exception
     * @returns $this
     */
    public function setData($mKey, $mValue = null)
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mSubValue) {
                $this->setData($sKey, $mSubValue);
            }
        } elseif (is_string($mKey) || is_numeric($mKey)) {
            $this->aData[$mKey] = $mValue;
        } else {
            throw new \Exception('Key must be a string or a numeric');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Unset an item from the view data array
     *
     * @param string|array $mKey The key, or keys, to unset
     *
     * @return $this
     */
    public function unsetData($mKey)
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sSubKey) {
                $this->unsetData($sSubKey);
            }
        } else {
            unset($this->aData[$mKey]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a view
     *
     * @param string|array $mView   The view to load, or an array of views to load
     * @param array        $aData   Data to pass to the view(s)
     * @param boolean      $bReturn Whether to return the view(s) or not
     *
     * @return mixed
     */
    public function load($mView, $aData = [], $bReturn = false)
    {
        if (is_array($mView)) {
            $sOut = '';
            foreach ($mView as $sView) {
                if ($bReturn) {
                    $sOut .= $this->load($sView, $aData, $bReturn);
                } else {
                    $this->load($sView, $aData, $bReturn);
                }
            }
            return $bReturn ? $sOut : $this;
        } elseif (is_string($mView)) {

            $aData  = array_merge($this->getData(), (array) $aData);
            $oInput = Factory::service('Input');
            $oCi    = function_exists('get_instance') ? get_instance() : null;

            if ($oInput::isCli() || empty($oCi->load)) {
                extract($aData);
                include $mView;
            } elseif (!$bReturn) {
                $oCi->load->view($mView, $aData, $bReturn);
                return $this;
            } else {
                return $oCi->load->view($mView, $aData, $bReturn);
            }
        } else {
            return $this;
        }
    }
}
