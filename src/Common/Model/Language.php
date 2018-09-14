<?php

/**
 * Language model
 *
 * @package     Nails
 * @subpackage  common
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

class Language
{
    protected $oCi;

    // --------------------------------------------------------------------------

    /**
     * Constructs the model
     */
    public function __construct()
    {
        $this->oCi =& get_instance();
        $this->oCi->config->load('languages');
    }

    // --------------------------------------------------------------------------

    /**
     * Retursn the default language object
     * @return mixed stdClass on success, false on failure
     */
    public function getDefault()
    {
        $sDefault  = $this->oCi->config->item('languages_default');
        $oLanguage = $this->getByCode($sDefault);

        return !empty($oLanguage) ? $oLanguage : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default language's code
     * @return mixed stdClass on success, false on failure
     */
    public function getDefaultCode()
    {
        $oDefault = $this->getDefault();
        return empty($oDefault->code) ? false : $oDefault->code;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default language's label
     * @return mixed stdClass on success, false on failure
     */
    public function getDefaultLabel()
    {
        $oDefault = $this->getDefault();
        return empty($oDefault->label) ? false : $oDefault->label;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all defined languages
     * @return array
     */
    public function getAll()
    {
        return $this->oCi->config->item('languages');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all defined languages as a flat array
     * @return array
     */
    public function getAllFlat()
    {
        $aOut       = array();
        $aLanguages = $this->getAll();

        foreach ($aLanguages as $oLanguage) {
            $aOut[$oLanguage->code] = $oLanguage->label;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the enabled languages
     * @return array
     */
    public function getAllEnabled()
    {
        $aEnabled = $this->oCi->config->item('languages_enabled');
        $aOut     = array();

        foreach ($aEnabled as $sCode) {
            $aOut[] = $this->getByCode($sCode);
        }

        return array_filter($aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the enabled languages as a flat array
     * @return array
     */
    public function getAllEnabledFlat()
    {
        $aOut       = array();
        $aLanguages = $this->getAllEnabled();

        foreach ($aLanguages as $oLanguage) {
            $aOut[$oLanguage->code] = $oLanguage->label;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a language by it's code
     * @param  string $sCode The language code
     * @return mixed         stdClass on success, false on failure
     */
    public function getByCode($sCode)
    {
        $aLanguages = $this->getAll();
        return !empty($aLanguages[$sCode]) ? $aLanguages[$sCode] : false;
    }
}
