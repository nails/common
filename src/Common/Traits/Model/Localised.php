<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\ModelException;
use Nails\Common\Resource;
use Nails\Common\Service\Database;
use Nails\Common\Service\Locale;
use Nails\Factory;

/**
 * Trait Localised
 *
 * @package Nails\Common\Traits\Model
 */
trait Localised
{
    /**
     * The name of the column containing the language string
     *
     * @var string
     */
    protected static $sColumnLanguage = 'language';

    /**
     * The name of the column containing the region string
     *
     * @var string
     */
    protected static $sColumnRegion = 'region';

    /**
     * The suffix added to the localised table
     *
     * @var string
     */
    protected static $sLocalisedTableSuffix = '_localised';

    /**
     * The suffix added to the localised table alias
     *
     * @var string
     */
    protected static $sLocalisedTableAliasSuffix = 'l';

    // --------------------------------------------------------------------------

    /**
     * Overloads the getAll to add a Locale object to each resource
     *
     * @param int|null $iPage           The page number of the results, if null then no pagination
     * @param int|null $iPerPage        How many items per page of paginated results
     * @param array    $aData           Any data to pass to getCountCommon()
     * @param bool     $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     *
     * @return array
     */
    public function getAll($iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false)
    {
        $aResult = parent::getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);
        $this->addLocaleToResources($aResult);
        return $aResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Overloads the getCountCommon method to inject localisation query modifiers
     *
     * @param array $aData Any data to pass to parent::getCountCommon()
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function getCountCommon(array $aData = [])
    {
        $this->injectLocalisationQuery($aData);
        parent::getCountCommon($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Formats the input data into a method suitable for
     *
     * @param array $aData The data being passed
     */
    protected function prepareWriteData(array &$aData): parent
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Injects localisation modifiers
     *
     * @param array $aData The data passed to getCountCommon()
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function injectLocalisationQuery(array &$aData): void
    {
        /** @var Locale $oLocale */
        $oLocale = Factory::service('Locale');
        $sTable  = $this->getTableName();
        $sAlias  = $this->getTableAlias();

        /**
         * Restrict to a specific locale by passing in USE_LOCALE to the data array
         * Pass NO_LOCALISE_FILTER to the data array the developer can return items for all locales
         */
        if (array_key_exists('USE_LOCALE', $aData)) {
            if ($aData['USE_LOCALE'] instanceof \Nails\Common\Factory\Locale) {
                $oUserLocale = $aData['USE_LOCALE'];
            } else {
                list($sLanguage, $sRegion) = $oLocale::parseLocaleString($aData['USE_LOCALE']);
                $oUserLocale = $this->getLocale($sLanguage, $sRegion);
            }

            if (!array_key_exists('where', $aData)) {
                $aData['where'] = [];
            }

            $aData['where'][] = ['language', $oUserLocale->getLanguage()];
            $aData['where'][] = ['region', $oUserLocale->getRegion()];

        } elseif (!array_key_exists('NO_LOCALISE_FILTER', $aData)) {

            if (!array_key_exists('select', $aData)) {
                $aData['select'] = [
                    $sAlias . '.*',
                ];
            }

            $oUserLocale      = $oLocale->get();
            $sUserLanguage    = $oUserLocale->getLanguage();
            $sUserRegion      = $oUserLocale->getRegion();
            $oDefaultLocale   = $oLocale->getDefautLocale();
            $sDefaultLanguage = $oDefaultLocale->getLanguage();
            $sDefaultRegion   = $oDefaultLocale->getRegion();

            $sQueryExact    = 'SELECT COUNT(*) FROM ' . $sTable . ' sub_1 WHERE sub_1.id = ' . $sAlias . '.id AND sub_1.' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_1.' . static::$sColumnRegion . ' = "' . $sUserRegion . '"';
            $sQueryLanguage = 'SELECT COUNT(*) FROM ' . $sTable . ' sub_2 WHERE sub_2.id = ' . $sAlias . '.id AND sub_2.' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_2.' . static::$sColumnRegion . ' != "' . $sUserRegion . '"';

            $aConditionals = [
                '((' . $sQueryExact . ') = 1 AND ' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND ' . static::$sColumnRegion . ' = "' . $sUserRegion . '")',
                '((' . $sQueryExact . ') = 0 AND ' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '")',
                '((' . $sQueryExact . ') = 0 AND (' . $sQueryLanguage . ') = 0 AND ' . static::$sColumnLanguage . ' = "' . $sDefaultLanguage . '" AND ' . static::$sColumnRegion . ' = "' . $sDefaultRegion . '")',
            ];

            if (!array_key_exists('where', $aData)) {
                $aData['where'] = [];
            }

            $aData['where'][] = implode(' OR ', $aConditionals);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a locale object to an array of Resources
     *
     * @param array $aResources The array of Resources
     */
    protected function addLocaleToResources(array $aResources): void
    {
        foreach ($aResources as $oResource) {
            $this->addLocaleToResource($oResource);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a locale object to a Resource, and removes the language and region properties
     *
     * @param Resource $oResource The resource to modify
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function addLocaleToResource(Resource $oResource): void
    {
        $oResource->locale = $this->getLocale(
            $oResource->{static::$sColumnLanguage},
            $oResource->{static::$sColumnRegion}
        );
        unset($oResource->{static::$sColumnLanguage});
        unset($oResource->{static::$sColumnRegion});
    }

    // --------------------------------------------------------------------------

    /**
     * Generate a Locale object for a language/region
     *
     * @param string $sLanguage The language to set
     * @param string $sRegion   The region to set
     *
     * @return \Nails\Common\Factory\Locale
     * @throws \Nails\Common\Exception\FactoryException
     */
    private function getLocale(string $sLanguage, string $sRegion): \Nails\Common\Factory\Locale
    {
        return Factory::factory('Locale')
            ->setLanguage(Factory::factory('LocaleLanguage', null, $sLanguage))
            ->setRegion(Factory::factory('LocaleRegion', null, $sRegion));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the localised table name
     *
     * @return string
     */
    public function getTableName($bIncludeAlias = false): string
    {
        $sTable = parent::getTableName() . static::$sLocalisedTableSuffix;
        return $bIncludeAlias ? trim($sTable . ' as `' . $this->getTableAlias() . '`') : $sTable;
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new localised item
     *
     * @param array $aData         The data array
     * @param bool  $bReturnObject Whetehr to return the item's ID or the object on success
     *
     * @return mixed|null
     * @throws ModelException
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        if (!array_key_exists('locale', $aData)) {
            throw new ModelException(
                'Localised item must define a "locale" property when creating'
            );
        } elseif (!($aData['locale'] instanceof \Nails\Common\Factory\Locale)) {
            throw new ModelException(
                '"locale" must be an instance of \Nails\Common\Factory\Locale'
            );
        }

        $oItemLocale = $aData['locale'];
        unset($aData['locale']);

        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        if (empty($aData['id'])) {
            $oDb->set('id', null);
            $oDb->insert(parent::getTableName());
            $aData['id'] = $oDb->insert_id();
        }

        $aData['language'] = $oItemLocale->getLanguage();
        $aData['region']   = $oItemLocale->getRegion();

        $iItemId = parent::create($aData, false);

        if (empty($iItemId)) {
            return null;
        }

        if (!$bReturnObject) {
            return $iItemId;
        }

        return $this->getById($iItemId, ['USE_LOCALE' => $oItemLocale]);
    }

    // --------------------------------------------------------------------------

    /**
     * Update a localised item
     *
     * @param int   $iId   The ID of the item being updated
     * @param array $aData The data array
     *
     * @return bool
     * @throws ModelException
     */
    public function update($iId, array $aData = [])
    {
        if (!array_key_exists('locale', $aData)) {
            throw new ModelException(
                'Localised item must define a "locale" property when creating'
            );
        } elseif (!($aData['locale'] instanceof \Nails\Common\Factory\Locale)) {
            throw new ModelException(
                '"locale" must be an instance of \Nails\Common\Factory\Locale'
            );
        }

        $oItemLocale = $aData['locale'];
        unset($aData['locale']);

        $aData['language'] = $oItemLocale->getLanguage();
        $aData['region']   = $oItemLocale->getRegion();

        return parent::update($iId, $aData);
    }
}
