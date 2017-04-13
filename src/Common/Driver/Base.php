<?php

/**
 * Payment driver base
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Interface
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Driver;

abstract class Base
{
    protected $sSlug;
    protected $sLabel;
    protected $iLogoId;
    protected $oSettings;

    // --------------------------------------------------------------------------

    /**
     * Construct the driver
     */
    public function __construct()
    {
        $this->sLabel    = 'Untitled Driver';
        $this->oSettings = new \stdClass();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of existing properties based on the default settings and database
     * overrides, anything left over is placed into the $oSettings object.
     *
     * @param $aConfig
     *
     * @return $this
     */
    public function setConfig($aConfig)
    {
        foreach ($aConfig as $sKey => $mValue) {
            if (property_exists($this, $sKey)) {
                $this->{$sKey} = $mValue;
            } else {
                $this->oSettings->{$sKey} = $mValue;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Safely retrieve a value from the $oSettings object
     *
     * @param  string $sProperty the property to retrieve
     *
     * @return mixed
     */
    protected function getSetting($sProperty = null)
    {
        if (property_exists($this->oSettings, $sProperty)) {
            return $this->oSettings->{$sProperty};
        } elseif (is_null($sProperty)) {
            return $this->oSettings;
        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return the driver's slug
     * @return string
     */
    public function getSlug()
    {
        return $this->sSlug;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the driver's label
     * @return string
     */
    public function getLabel()
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the driver's logo ID
     * @return integer
     */
    public function getLogoId()
    {
        return (int) $this->iLogoId ?: null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the URL of the driver's logo
     *
     * @param  integer $iWidth  The bounding width
     * @param  integer $iHeight The bounding height
     *
     * @return string
     */
    public function getLogoUrl($iWidth = null, $iHeight = null)
    {
        $iLogoId = $this->getLogoId();

        if (!empty($iLogoId) && !empty($iWidth) && !empty($iHeight)) {
            return cdnScale($iLogoId, $iWidth, $iHeight);
        } elseif (!empty($iLogoId)) {
            return cdnServe($iLogoId);
        } else {
            return null;
        }
    }
}
