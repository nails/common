<?php

/**
 * This class brings about uniformity to Nails driver models.
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

class BaseDriver
{
    /**
     * These should be overridden by the child class to customise which drivers are loaded
     * @var string
     */
    protected $sModule         = null;
    protected $sType           = null;
    protected $sEnabledSetting = 'enabled_drivers';

    // --------------------------------------------------------------------------


    protected $aDrivers  = array();
    protected $aEnabled  = array();
    protected $Instances = array();

    // --------------------------------------------------------------------------

    /**
     * Construct the model.
     */
    public function __construct()
    {
        //  All available drivers
        $this->aDrivers = _NAILS_GET_DRIVERS($this->sModule, $this->sType) ?: array();

        //  Enabled drivers
        $aEnabled = appSetting($this->sEnabledSetting, $this->sModule) ?: array();

        foreach ($this->aDrivers as $oDriver) {
            if (in_array($oDriver->slug, $aEnabled)) {
                $this->aEnabled[] = $oDriver;
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all available drivers
     * @return array
     */
    public function getAll()
    {
        return $this->aDrivers;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all enabled drivers
     * @return array
     */
    public function getEnabled()
    {
        return $this->aEnabled;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches athe slugs of all enabled drivers
     * @return array
     */
    public function getEnabledSlugs()
    {
        $aOut = array();
        foreach ($this->aEnabled as $oDriver) {
            $aOut[] = $oDriver->slug;
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Get a driver by it's slug
     * @param  string $sSlug The driver's slug
     * @return \stdClass
     */
    public function getBySlug($sSlug)
    {
        foreach ($this->aDrivers as $oDriver) {
            if ($sSlug == $oDriver->slug) {
                return $oDriver;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    public function getInstance($sSlug)
    {
        if (isset($this->aInstances[$sSlug])) {

            return $this->aInstances[$sSlug];

        } else {

            foreach ($this->aEnabled as $oDriver) {
                if ($sSlug == $oDriver->slug) {

                    $this->aInstances[$sSlug] = _NAILS_GET_DRIVER_INSTANCE($oDriver);

                    //  Apply driver configurations
                    $aSettings = array(
                        'sSlug' => $oDriver->slug
                    );
                    if (!empty($oDriver->data->settings)) {
                        $aSettings = array_merge(
                            $aSettings,
                            $this->extractDriverSettings(
                                $oDriver->data->settings,
                                $oDriver->slug
                            )
                        );
                    }

                   $this->aInstances[$sSlug]->setConfig($aSettings);

                    return $this->aInstances[$sSlug];
                }
            }

        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Recursively gets all the settings from the settings array
     * @param  array  $aSettings The array of fieldsets and/or settings
     * @param  string $sSlug     The driver's slug
     * @return array
     */
    protected function extractDriverSettings($aSettings, $sSlug)
    {
        $aOut = array();

        foreach ($aSettings as $oSetting) {

            //  If the object contains a `fields` property then consider this a fieldset and inception
            if (isset($oSetting->fields)) {

                $aOut = array_merge(
                    $aOut,
                    $this->extractDriverSettings(
                        $oSetting->fields,
                        $sSlug
                    )
                );

            } else {

                $sValue = appSetting($oSetting->key, $sSlug);
                if (is_null($sValue) && isset($oSetting->default)) {
                    $sValue = $oSetting->default;
                }
                $aOut[$oSetting->key] = $sValue;
            }
        }

        return $aOut;
    }
}
