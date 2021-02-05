<?php

/**
 * Manage countries
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception;
use Nails\Factory;
use Nails\Common\Resource;

/**
 * Class Country
 *
 * @package Nails\Common\Service
 */
class Country
{
    /**
     * The location of the country database
     *
     * @var string
     */
    const DATABASE_COUNTRY = NAILS_APP_PATH . 'vendor/annexare/countries-list/data/countries.json';

    /**
     * The location of the continent database
     *
     * @var string
     */
    const DATABASE_CONTINENT = NAILS_APP_PATH . 'vendor/annexare/countries-list/data/continents.json';

    /**
     * The location of the language database
     *
     * @var string
     */
    const DATABASE_LANGUAGE = NAILS_APP_PATH . 'vendor/annexare/countries-list/data/languages.json';

    // --------------------------------------------------------------------------

    /**
     * @var array
     */
    protected $aCountries = [];

    /**
     * @var array
     */
    protected $aContinents = [];

    /**
     * @var array
     */
    protected $aLanguages = [];

    // --------------------------------------------------------------------------

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->loadDatabase(static::DATABASE_COUNTRY, 'Country', $this->aCountries);
        $this->loadDatabase(static::DATABASE_CONTINENT, 'CountryContinent', $this->aContinents);
        $this->loadDatabase(static::DATABASE_LANGUAGE, 'CountryLanguage', $this->aLanguages);

        /** @var Resource\Country $oCountry */
        foreach ($this->aCountries as $oCountry) {
            $oCountry->continent = $this->getContinent($oCountry->continent);
            $oCountry->languages = array_map(
                function ($sIso) {
                    return $this->getLanguage($sIso);
                },
                $oCountry->languages
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a data file and assigns it to a variable
     *
     * @param string $sPath     The file path of the database
     * @param string $sResource The resource to assign to each item in the database
     * @param array  $aTarget   The target array to populate
     *
     * @return Country
     * @throws Exception\FactoryException
     * @throws Exception\NailsException
     */
    protected function loadDatabase(string $sPath, string $sResource, array &$aTarget): Country
    {
        if (!fileExistsCS($sPath)) {
            throw new Exception\NailsException(
                sprintf(
                    'Database does not exist at %s',
                    $sPath
                )
            );
        }

        $sData = file_get_contents($sPath);
        if (empty($sData)) {
            return $this;
        }

        $aData = json_decode($sData);
        if (is_null($aData)) {
            throw new Exception\NailsException(
                sprintf(
                    'Failed to parse database. %s.',
                    json_last_error_msg()
                )
            );
        }

        foreach ($aData as $sIso => $oDatum) {

            $sIso = strtoupper($sIso);

            if (!is_object($oDatum)) {
                $oDatum = (object) ['name' => $oDatum];
            }
            $oDatum->iso    = $sIso;
            $aTarget[$sIso] = Factory::resource($sResource, null, $oDatum);
        }

        arraySortMulti($aTarget, 'name');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the countries
     *
     * @return Resource\Country[]
     */
    public function getCountries(): array
    {
        return $this->aCountries;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the countries as a flat key=>value array
     *
     * @return string[]
     */
    public function getCountriesFlat(): array
    {
        return $this->flatten($this->aCountries);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a country by its ISO code
     *
     * @param string $sIso the ISO code to search for
     *
     * @return Resource\Country|null
     */
    public function getCountry(string $sIso): ?Resource\Country
    {
        return $this->lookup($sIso, $this->aCountries);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the continents
     *
     * @return Resource\Country\Continent[]
     */
    public function getContinents(): array
    {
        return $this->aContinents;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the continents as a flat key=>value array
     *
     * @return string[]
     */
    public function getContinentsFlat(): array
    {
        return $this->flatten($this->aContinents);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a continent by its ISO code
     *
     * @param string $sIso the ISO code to search for
     *
     * @return Resource\Country\Continent|null
     */
    public function getContinent(string $sIso): ?Resource\Country\Continent
    {
        return $this->lookup($sIso, $this->aContinents);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the languages
     *
     * @return Resource\Country\Language[]
     */
    public function getLanguages(): array
    {
        return $this->aLanguages;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the languages as a flat key=>value array
     *
     * @return string[]
     */
    public function getLanguagesFlat(): array
    {
        return $this->flatten($this->aLanguages);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a language by its ISO code
     *
     * @param string $sIso the ISO code to search for
     *
     * @return Resource\Country\Language|null
     */
    public function getLanguage(string $sIso): ?Resource\Country\Language
    {
        return $this->lookup($sIso, $this->aLanguages);
    }

    // --------------------------------------------------------------------------

    /**
     * Looks up a database by ISO code
     *
     * @param string $sIso
     * @param array  $aDatabase
     *
     * @return Resource
     */
    protected function lookup(string $sIso, array $aDatabase): ?Resource
    {
        return getFromArray(strtoupper($sIso), $aDatabase);
    }

    // --------------------------------------------------------------------------

    /**
     * Flattens a database to a key=>value iso/name pair
     *
     * @param array $aDatabase
     *
     * @return array
     */
    protected function flatten(array $aDatabase): array
    {
        return array_map(
            function (Resource $oItem) {
                return $oItem->name;
            },
            $aDatabase
        );
    }
}
