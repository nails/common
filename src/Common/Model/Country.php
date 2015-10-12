<?php

/**
 * Manage countries
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

class Country extends Base
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
