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

use Nails\Factory;

class Country
{
    protected $oConfig;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        $this->oConfig = Factory::service('Config');
        $this->oConfig->load('countries');
    }

    // --------------------------------------------------------------------------

    /**
     * Get all defined countries
     * @return array
     */
    public function getAll()
    {
        return $this->oConfig->item('countries');
    }

    // --------------------------------------------------------------------------

    /**
     * Get all defined countries as a flat array
     * @return array
     */
    public function getAllFlat()
    {
        $aOut       = array();
        $aCountries = $this->getAll();

        foreach ($aCountries as $oCountry) {
            $aOut[$oCountry->code] = $oCountry->label;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Get a country by it's code
     * @param  string $sCode The code to look for
     * @return mixed         stdClass on success, false on failure
     */
    public function getByCode($sCode)
    {
        $aCountries = $this->getAll();

        return ! empty($aCountries[$sCode]) ? $aCountries[$sCode] : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Get all the defined continents
     * @return array
     */
    public function getAllContinents()
    {
        return $this->oConfig->item('continents');
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
     * @param  string $sCode The continents code
     * @return mixed         stdClass on success, false on failure
     */
    public function getContinentByCode($sCode)
    {
        $aContinents = $this->getAll();

        return ! empty($aContinents[$sCode]) ? $aContinents[$sCode] : false;
    }
}
