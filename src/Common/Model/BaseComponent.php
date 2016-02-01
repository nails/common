<?php

/**
 * This class brings about uniformity to Nails component models (i.e drivers and skins)
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Nails\Exception\NailsException;

class BaseComponent
{
    protected $sComponentType;
    protected $aComponents;
    protected $aEnabled;

    // --------------------------------------------------------------------------

    /**
     * These should be overridden by the child class to customise which components
     * are loaded in this context.
     */

    /**
     * The name of the module loading the components; used for settings and for
     * finding components which are for the module.
     * @var string
     */
    protected $sModule = null;

    /**
     * The type of domponent to load; filter the domponents by their subType, if any.
     * @var string
     */
    protected $sType = null;

    /**
     * Which app setting (in the $sModule grouping) defines the array of enabled
     * component slugs.
     * @var string
     */
    protected $sEnabledSetting = 'enabled_components';

    // --------------------------------------------------------------------------

    /**
     * Construct the model.
     */
    public function __construct()
    {
        if (empty($this->sComponentType)) {
            throw new NailsException('Missing sComponentType declaration.', 1);
        }

        if (empty($this->sModule)) {
            throw new NailsException('Missing sModule declaration.', 1);
        }

        //  All available components
        $this->aComponents = array();
        $aComponents       = _NAILS_GET_COMPONENTS_BY_TYPE($this->sComponentType) ?: array();

        //  Only accept those which are for the desired module and, if specified, are of the correct sub type.
        foreach ($aComponents as $oComponent) {
            if ($this->sModule == $oComponent->forModule) {
                if (empty($this->sType) || $this->sType == $oComponent->subType) {
                    $this->aComponents[] = $oComponent;
                }
            }
        }

        //  Enabled components
        $aEnabled = appSetting($this->sEnabledSetting, $this->sModule) ?: array();
dumpanddie($this->sEnabledSetting);
        foreach ($this->aComponents as $oComponent) {
            if (in_array($oComponent->slug, $aEnabled)) {
                $this->aEnabled[] = $oComponent;
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all available components
     * @return array
     */
    public function getAll()
    {
        return $this->aComponents;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all enabled components
     * @return array
     */
    public function getEnabled()
    {
        return $this->aEnabled;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches the slugs of all enabled components
     * @return array
     */
    public function getEnabledSlugs()
    {
        $aOut = array();
        foreach ($this->aEnabled as $oComponent) {
            $aOut[] = $oComponent->slug;
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Get a component by it's slug
     * @param  string $sSlug The components's slug
     * @return \stdClass
     */
    public function getBySlug($sSlug)
    {
        foreach ($this->aComponents as $oComponent) {
            if ($sSlug == $oComponent->slug) {
                return $oComponent;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Recursively gets all the settings from the settings array
     * @param  array  $aSettings The array of fieldsets and/or settings
     * @param  string $sSlug     The components's slug
     * @return array
     */
    protected function extractComponentSettings($aSettings, $sSlug)
    {
        $aOut = array();

        foreach ($aSettings as $oSetting) {

            //  If the object contains a `fields` property then consider this a fieldset and inception
            if (isset($oSetting->fields)) {

                $aOut = array_merge(
                    $aOut,
                    $this->extractComponentSettings(
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
