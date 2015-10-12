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

class Language extends Base
{
    /**
     * Constructs the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->config->load('languages');
    }

    // --------------------------------------------------------------------------

    /**
     * Retursn the default language object
     * @return mixed stdClass on success, false on failure
     */
    public function getDefault()
    {
        $default  = $this->config->item('languages_default');
        $language = $this->getByCode($default);

        return !empty($language) ? $language : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default language's code
     * @return mixed stdClass on success, false on failure
     */
    public function getDefaultCode()
    {
        $default = $this->getDefault();
        return empty($default->code) ? false : $default->code;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default language's label
     * @return mixed stdClass on success, false on failure
     */
    public function getDefaultLabel()
    {
        $default = $this->getDefault();
        return empty($default->label) ? false : $default->label;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all defined languages
     * @return array
     */
    public function getAll()
    {
        return $this->config->item('languages');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all defined languages as a flat array
     * @return array
     */
    public function getAllFlat()
    {
        $out       = array();
        $languages = $this->getAll();

        foreach ($languages as $language) {

            $out[$language->code] = $language->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the enabled languages
     * @return array
     */
    public function getAllEnabled()
    {
        $enabled = $this->config->item('languages_enabled');
        $out     = array();

        foreach ($enabled as $code) {

            $out[] = $this->getByCode($code);
        }

        return array_filter($out);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the enabled languages as a flat array
     * @return array
     */
    public function getAllEnabledFlat()
    {
        $out       = array();
        $languages = $this->getAllEnabled();

        foreach ($languages as $language) {

            $out[$language->code] = $language->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a language by it's code
     * @param  string $code The language code
     * @return mixed        stdClass on success, false on failure
     */
    public function getByCode($code)
    {
        $languages = $this->getAll();
        return !empty($languages[$code]) ? $languages[$code] : false;
    }
}
