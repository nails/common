<?php

/**
 * Language model
 *
 * @package     Nails
 * @subpackage  module-testimonial
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

class NAILS_Language_model extends NAILS_Model
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

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_LANGUAGE_MODEL')) {

    class Language_model extends NAILS_Language_model
    {
    }
}
