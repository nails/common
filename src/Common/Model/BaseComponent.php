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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Component;
use Nails\Common\Service\AppSetting;
use Nails\Components;
use Nails\Factory;

/**
 * Class BaseComponent
 *
 * @package Nails\Common\Model
 */
abstract class BaseComponent
{
    /** @var string */
    protected $sComponentType;

    /** @var Component[] */
    protected $aComponents;

    /** @var Component[]|Component */
    protected $mEnabled;

    // --------------------------------------------------------------------------

    /**
     * These should be overridden by the child class to customise which components
     * are loaded in this context.
     */

    /**
     * The name of the module loading the components; used for settings and for
     * finding components which are for the module.
     *
     * @var string
     */
    protected $sModule = null;

    /**
     * The type of component to load; filter the components by their subType, if any.
     *
     * @var string
     */
    protected $sType = null;

    /**
     * Which app setting (in the $sModule grouping) defines the array of enabled
     * component slugs.
     *
     * @var string
     */
    protected $sEnabledSetting = null;

    /**
     * Define whether multiple components can be enabled
     *
     * @var bool
     */
    protected $bEnableMultiple = true;

    // --------------------------------------------------------------------------

    /**
     * BaseComponent constructor.
     *
     * @throws NailsException
     * @throws FactoryException
     */
    public function __construct()
    {
        if (empty($this->sComponentType)) {
            throw new NailsException('Missing sComponentType declaration.');

        } elseif (empty($this->sModule)) {
            throw new NailsException('Missing sModule declaration.');
        }

        //  All available components
        $this->aComponents = [];
        $aComponents       = Components::filter($this->sComponentType);

        //  Only accept those which are for the desired module and, if specified, are of the correct sub type.
        foreach ($aComponents as $oComponent) {
            if ($this->sModule == $oComponent->forModule) {
                if (empty($this->sType) || $this->sType == $oComponent->subType) {
                    $this->aComponents[] = $oComponent;
                }
            }
        }

        //  Define the enabled setting
        if (empty($this->sEnabledSetting)) {
            $this->sEnabledSetting = 'enabled_' . $this->sComponentType;
            if (!empty($this->sType)) {
                $this->sEnabledSetting .= '_' . $this->sType;
            }
        }

        //  Enabled components
        if ($this->isMultiple()) {

            $this->mEnabled = [];
            $aEnabled       = appSetting($this->sEnabledSetting, $this->sModule, []);

            foreach ($this->aComponents as $oComponent) {
                if (in_array($oComponent->slug, $aEnabled)) {
                    $this->mEnabled[] = $oComponent;
                }
            }

        } else {

            $sEnabled = appSetting($this->sEnabledSetting, $this->sModule);
            foreach ($this->aComponents as $oComponent) {
                if ($oComponent->slug === (string) $sEnabled) {
                    $this->mEnabled = $oComponent;
                    break;
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the model supports enabling multiple components.
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->bEnableMultiple;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all available components
     *
     * @return Component[]
     */
    public function getAll(): array
    {
        return $this->aComponents;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all available components as a flat array
     *
     * @return string[]
     */
    public function getAllFlat(): array
    {
        $aComponents = $this->getAll();
        return array_combine(
            array_map(function (Component $oComponent) {
                return $oComponent->slug;
            }, $aComponents),
            array_map(function (Component $oComponent) {
                return $oComponent->name;
            }, $aComponents)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches the enabled component, or array of components if bEnableMultiple is true
     *
     * @return Component[]|Component
     */
    public function getEnabled()
    {
        return $this->mEnabled;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches the slug of the enabled components, or array of slugs if bEnableMultiple is true
     *
     * @return array|string
     */
    public function getEnabledSlug()
    {
        if ($this->isMultiple()) {

            $aOut = [];
            foreach ($this->getEnabled() as $oComponent) {
                $aOut[] = $oComponent->slug;
            }
            return $aOut;

        } else {

            return !empty($this->mEnabled->slug) ? $this->mEnabled->slug : null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get a component by it's slug
     *
     * @param string $sSlug The components's slug
     *
     * @return Component|null
     */
    public function getBySlug($sSlug): ?Component
    {
        foreach ($this->getAll() as $oComponent) {
            if ($sSlug == $oComponent->slug) {
                return $oComponent;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the setting key for this component
     *
     * @return string|null
     */
    public function getSettingKey(): ?string
    {
        return $this->sEnabledSetting;
    }

    // --------------------------------------------------------------------------

    /**
     * Save which components are enabled
     *
     * @param array|string $mSlug The slug to set as enabled, or array of slugs if bEnableMultiple is true
     *
     * @return $this
     * @throws FactoryException
     * @throws NailsException
     */
    public function saveEnabled($mSlug): self
    {
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');

        if ($this->isMultiple()) {

            $mSlug = (array) $mSlug;
            $mSlug = array_filter($mSlug);
            $mSlug = array_unique($mSlug);

        } else {
            $mSlug = trim($mSlug);
        }

        $aSetting = [
            $this->sEnabledSetting => $mSlug,
        ];

        if (!$oAppSettingService->set($aSetting, $this->sModule)) {
            throw new NailsException($oAppSettingService->lastError());
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Recursively gets all the settings from the settings array
     *
     * @param array  $aSettings The array of field sets and/or settings
     * @param string $sSlug     The components's slug
     *
     * @return array
     * @throws FactoryException
     */
    protected function extractComponentSettings($aSettings, $sSlug)
    {
        $aOut = [];

        foreach ($aSettings as $oSetting) {

            //  If the object contains a `fields` property then consider this a field set and inception
            if (isset($oSetting->fields)) {

                $aOut = array_merge(
                    $aOut,
                    $this->extractComponentSettings(
                        $oSetting->fields,
                        $sSlug
                    )
                );

            } else {

                $mValue = appSetting($oSetting->key, $sSlug);
                if (is_null($mValue) && isset($oSetting->default)) {
                    $mValue = $oSetting->default;
                }
                $aOut[$oSetting->key] = $mValue;
            }
        }

        return $aOut;
    }
}
