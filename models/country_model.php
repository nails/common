<?php

/**
 * Fetch countries
 *
 * @package     Nails
 * @subpackage  common
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

class NAILS_Country_model extends NAILS_Model
{
    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->config->load('countries');
    }

    // --------------------------------------------------------------------------

    /**
     * Get all defined countries
     * @return array
     */
    public function getAll()
    {
        return $this->config->item('countries');
    }

    // --------------------------------------------------------------------------

    /**
     * Get all defined countries as a flat array
     * @return array
     */
    public function getAllFlat()
    {
        $out       = array();
        $countries = $this->getAll();

        foreach ($countries as $c) {

            $out[$c->code] = $c->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Get a country by it's code
     * @param  string $code The code to look for
     * @return mixed        stdClass on success, false on failure
     */
    public function getByCode($code)
    {
        $countries = $this->getAll();

        return ! empty($countries[$code]) ? $countries[$code] : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Get all the defined continents
     * @return array
     */
    public function getAllContinents()
    {
        return $this->config->item('continents');
    }

    // --------------------------------------------------------------------------

    /**
     * Get all defined continents as a flat array
     * @return array
     */
    public function getAllContinentsFlat()
    {
        return $this->getAllContinents();
    }

    // --------------------------------------------------------------------------

    /**
     * Get a continent by it's code
     * @param  string $code The continents code
     * @return mixed        stdClass on success, false on failure
     */
    public function getContinentByCode($code)
    {
        $continents = $this->getAll();

        return ! empty($continents[$code]) ? $continents[$code] : false;
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

if (! defined('NAILS_ALLOW_EXTENSION_COUNTRY_MODEL')) {

    class Country_model extends NAILS_Country_model
    {
    }
}
