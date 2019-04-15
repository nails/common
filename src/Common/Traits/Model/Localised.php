<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Resource;
use Nails\Common\Service\Locale;
use Nails\Common\Traits\GetCountCommon;
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
     * Enforce models implement getTableName
     *
     * @return string
     */
    abstract public function getTableName();

    // --------------------------------------------------------------------------

    /**
     * Enforce models implement getTableAlias
     *
     * @return string
     */
    abstract public function getTableAlias();

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
        $sTable  = $this->getLocalisedTableName();
        $sAlias  = $this->getLocalisedTableAlias();

        $oDb = Factory::service('Database');
        $oDb->join(
            $sTable . ' ' . $sAlias,
            $sAlias . '.' . $this->tableIdColumn . ' = ' . $this->getTableAlias() . '.' . $this->tableIdColumn
        );

        if (!array_key_exists('select', $aData)) {
            $aData['select'] = [
                $sAlias . '.*',
            ];
        }

        if (!array_key_exists('locale', $aData)) {
            $oUserLocale = $oLocale->get();
        } else {
            list($sLanguage, $sRegion) = $oLocale::parseLocaleString($aData['locale']);
            $oUserLocale = $this->getLocale($sLanguage, $sRegion);
        }

        $sUserLanguage = $oUserLocale->getLanguage();
        $sUserRegion   = $oUserLocale->getRegion();

        $oDefaultLocale   = $oLocale->getDefautLocale();
        $sDefaultLanguage = $oDefaultLocale->getLanguage();
        $sDefaultRegion   = $oDefaultLocale->getRegion();

        $aData['select'][] = '(SELECT COUNT(*) FROM ' . $sTable . ' sub_1 WHERE sub_1.id = ' . $sAlias . '.id AND sub_1.' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_1.' . static::$sColumnRegion . ' = "' . $sUserRegion . '") exists_exact';
        $aData['select'][] = '(SELECT COUNT(*) FROM ' . $sTable . ' sub_2 WHERE sub_2.id = ' . $sAlias . '.id AND sub_2.' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_2.' . static::$sColumnRegion . ' != "' . $sUserRegion . '") exists_language';

        if (!array_key_exists('or_having', $aData)) {
            $aData['or_having'] = [];
        }

        $aData['or_having'][] = '(exists_exact = 1 AND ' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '" AND ' . static::$sColumnRegion . ' = "' . $sUserRegion . '")';
        $aData['or_having'][] = '(exists_exact = 0 AND ' . static::$sColumnLanguage . ' = "' . $sUserLanguage . '")';
        $aData['or_having'][] = '(exists_exact = 0 AND exists_language = 0 AND ' . static::$sColumnLanguage . ' = "' . $sDefaultLanguage . '" AND ' . static::$sColumnRegion . ' = "' . $sDefaultRegion . '")';
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
        unset($oResource->exists_exact);
        unset($oResource->exists_language);
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
    public function getLocalisedTableName(): string
    {
        return $this->getTableName() . static::$sLocalisedTableSuffix;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the localised table alias
     *
     * @return string
     */
    public function getLocalisedTableAlias(): string
    {
        return $this->getTableAlias() . static::$sLocalisedTableAliasSuffix;
    }
}
