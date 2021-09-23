<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Factory\Locale\Language;
use Nails\Common\Factory\Locale\Region;
use Nails\Common\Factory\Locale\Script;
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
     * Returns the column name for specific columns of interest
     *
     * @param string      $sColumn  The column to query
     * @param string|null $sDefault The default value if not defined
     *
     * @return string|null
     */
    abstract public function getColumn(string $sColumn, string $sDefault = null): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the is_deleted
     *
     * @return string|null
     */
    abstract public function getColumnIsDeleted(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for the locale language
     *
     * @return string
     */
    public function getLocaleLanguageColumn(): string
    {
        return static::$sColumnLanguage;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for the locale region
     *
     * @return string
     */
    public function getLocaleRegionColumn(): string
    {
        return static::$sColumnRegion;
    }

    // --------------------------------------------------------------------------

    /**
     * Overloads the getAll to add a Locale object to each resource
     *
     * @param int|null|array $iPage           The page number of the results, if null then no pagination; also accepts an $aData array
     * @param int|null       $iPerPage        How many items per page of paginated results
     * @param array          $aData           Any data to pass to getCountCommon()
     * @param bool           $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     *
     * @return Resource[]
     * @throws FactoryException
     */
    public function getAll($iPage = null, $iPerPage = null, array $aData = [], bool $bIncludeDeleted = false): array
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
     * @throws FactoryException
     * @throws ModelException
     */
    protected function getCountCommon(array &$aData = []): void
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
     * @throws FactoryException
     * @throws ModelException
     */
    protected function injectLocalisationQuery(array &$aData): void
    {
        /** @var Locale $oLocale */
        $oLocale         = Factory::service('Locale');
        $sTable          = $this->getTableName();
        $sAlias          = $this->getTableAlias();
        $sColumnLanguage = $this->getLocaleLanguageColumn();
        $sColumnRegion   = $this->getLocaleRegionColumn();

        /**
         * Restrict to a specific locale by passing in USE_LOCALE to the data array
         * Pass NO_LOCALISE_FILTER to the data array the developer can return items for all locales
         */

        if (!array_key_exists('select', $aData)) {
            $aData['select'] = [
                $sAlias . '.*',
            ];
        }

        if (array_key_exists('USE_LOCALE', $aData)) {
            if ($aData['USE_LOCALE'] instanceof \Nails\Common\Factory\Locale) {
                $oUserLocale = $aData['USE_LOCALE'];
            } else {
                [$sLanguage, $sRegion] = $oLocale::parseLocaleString($aData['USE_LOCALE']);
                $oUserLocale = $this->getLocale($sLanguage, $sRegion);
            }

            if (!array_key_exists('where', $aData)) {
                $aData['where'] = [];
            }

            $aData['where'][] = [$sColumnLanguage, $oUserLocale->getLanguage()];
            $aData['where'][] = [$sColumnRegion, $oUserLocale->getRegion()];

        } elseif (!array_key_exists('NO_LOCALISE_FILTER', $aData)) {

            $oUserLocale      = $oLocale->get();
            $sUserLanguage    = $oUserLocale->getLanguage();
            $sUserRegion      = $oUserLocale->getRegion();
            $oDefaultLocale   = $oLocale->getDefautLocale();
            $sDefaultLanguage = $oDefaultLocale->getLanguage();
            $sDefaultRegion   = $oDefaultLocale->getRegion();

            $sQueryExact    = 'SELECT COUNT(*) FROM ' . $sTable . ' sub_1 WHERE sub_1.id = ' . $sAlias . '.id AND sub_1.' . $sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_1.' . $sColumnRegion . ' = "' . $sUserRegion . '"';
            $sQueryLanguage = 'SELECT COUNT(*) FROM ' . $sTable . ' sub_2 WHERE sub_2.id = ' . $sAlias . '.id AND sub_2.' . $sColumnLanguage . ' = "' . $sUserLanguage . '" AND sub_2.' . $sColumnRegion . ' != "' . $sUserRegion . '"';

            if ($oLocale::MODEL_FALLBACK_TO_DEFAULT_LOCALE) {
                $aConditionals = [
                    '((' . $sQueryExact . ') = 1 AND ' . $sColumnLanguage . ' = "' . $sUserLanguage . '" AND ' . $sColumnRegion . ' = "' . $sUserRegion . '")',
                    '((' . $sQueryExact . ') = 0 AND ' . $sColumnLanguage . ' = "' . $sDefaultLanguage . '" AND ' . $sColumnRegion . ' = "' . $sDefaultRegion . '")',
                ];
            } else {
                $aConditionals = [
                    '((' . $sQueryExact . ') = 1 AND ' . $sColumnLanguage . ' = "' . $sUserLanguage . '" AND ' . $sColumnRegion . ' = "' . $sUserRegion . '")',
                    '((' . $sQueryExact . ') = 0 AND ' . $sColumnLanguage . ' = "' . $sUserLanguage . '")',
                    '((' . $sQueryExact . ') = 0 AND (' . $sQueryLanguage . ') = 0 AND ' . $sColumnLanguage . ' = "' . $sDefaultLanguage . '" AND ' . $sColumnRegion . ' = "' . $sDefaultRegion . '")',
                ];
            }

            if (!array_key_exists('where', $aData)) {
                $aData['where'] = [];
            }

            $aData['where'][] = '(' . implode(' OR ', $aConditionals) . ')';
        }

        //  Ensure each row knows about the other items available
        $sQuery = 'SELECT GROUP_CONCAT(CONCAT(`others`.`language`, \'_\', `others`.`region`)) FROM ' . $sTable . ' `others` WHERE `others`.`id` = `' . $sAlias . '`.`id`';
        if (!$this->isDestructiveDelete()) {
            $sQuery .= ' AND `others`.`' . $this->getColumnIsDeleted() . '` = 0';
        }
        $aData['select'][] = '(' . $sQuery . ') available_locales';
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a locale object to an array of Resources
     *
     * @param array $aResources The array of Resources
     *
     * @throws FactoryException
     */
    protected function addLocaleToResources(array $aResources): void
    {
        foreach ($aResources as $oResource) {
            $this->addLocaleToResource($oResource);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a Locale object to a Resource, and removes the language and region properties
     *
     * @param Resource $oResource The resource to modify
     *
     * @throws FactoryException
     */
    protected function addLocaleToResource(Resource $oResource): void
    {
        /** @var Locale $oLocale */
        $oLocale = Factory::service('Locale');

        //  Add the locale for _this_ item
        $oResource->locale = $this->getLocale(
            $oResource->{$this->getLocaleLanguageColumn()},
            $oResource->{$this->getLocaleRegionColumn()}
        );

        //  Set the locales for all _available_ items
        $oResource->available_locales = array_map(function ($sLocale) use ($oLocale) {
            [$sLanguage, $sRegion] = $oLocale::parseLocaleString($sLocale);
            return $this->getLocale($sLanguage, $sRegion);
        }, explode(',', $oResource->available_locales));

        //  Specify which locales are missing
        $oResource->missing_locales = array_diff(
            $oLocale->getSupportedLocales(),
            $oResource->available_locales
        );

        //  Remove internal fields
        unset($oResource->{$this->getLocaleLanguageColumn()});
        unset($oResource->{$this->getLocaleRegionColumn()});
    }

    // --------------------------------------------------------------------------

    /**
     * Generate a Locale object for a language/region
     *
     * @param string $sLanguage The language to set
     * @param string $sRegion   The region to set
     *
     * @return \Nails\Common\Factory\Locale
     * @throws FactoryException
     */
    private function getLocale(string $sLanguage, string $sRegion): \Nails\Common\Factory\Locale
    {
        /** @var Language $oLocaleLanguage */
        $oLocaleLanguage = Factory::factory('LocaleLanguage', null, $sLanguage);
        /** @var Region $oLocaleRegion */
        $oLocaleRegion = Factory::factory('LocaleRegion', null, $sRegion);
        /** @var Script $oLocaleScript */
        $oLocaleScript = Factory::factory('LocaleScript');

        /** @var \Nails\Common\Factory\Locale $oLocale */
        $oLocale = Factory::factory('Locale')
            ->setLanguage($oLocaleLanguage)
            ->setRegion($oLocaleRegion)
            ->setScript($oLocaleScript);

        return $oLocale;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the localised table name
     *
     * @param bool $bIncludeAlias Whether to include the table alias or not
     *
     * @return string
     * @throws ModelException
     */
    public function getTableName(bool $bIncludeAlias = false): string
    {
        $sTable = parent::getTableName() . static::$sLocalisedTableSuffix;
        return $bIncludeAlias ? trim($sTable . ' as `' . $this->getTableAlias() . '`') : $sTable;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a unique slug
     *
     * @param string                       $sLabel    The label from which to generate a slug
     * @param int                          $iIgnoreId The ID of an item to ignore
     * @param \Nails\Common\Factory\Locale $oLocale   The locale to restrict the tests against
     *
     * @return string
     * @throws ModelException
     */
    protected function generateSlug(array $aData = [], int $iIgnoreId = null, \Nails\Common\Factory\Locale $oLocale = null)
    {
        if (empty($oLocale)) {
            throw new ModelException(sprintf(
                '%s: A locale must be defined when generating slugs for a localised item',
                self::class
            ));
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        $oDb->start_cache();
        $oDb->where($this->getLocaleLanguageColumn(), $oLocale->getLanguage());
        $oDb->where($this->getLocaleRegionColumn(), $oLocale->getRegion());
        $oDb->stop_cache();

        $sSlug = parent::generateSlug($aData, $iIgnoreId);

        $oDb->flush_cache();

        return $sSlug;
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new localised item
     *
     * @param array                             $aData         The data array
     * @param bool                              $bReturnObject Whether to return the item's ID or the object on success
     * @param \Nails\Common\Factory\Locale|null $oLocale       The locale to create the item in
     *
     * @return null|int|Resource
     * @throws FactoryException
     * @throws ModelException
     */
    public function create(array $aData = [], $bReturnObject = false, \Nails\Common\Factory\Locale $oLocale = null)
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        if (empty($oLocale)) {
            throw new ModelException(
                self::class . ': A locale must be defined when creating a localised item'
            );
        }

        $aData[$this->getLocaleLanguageColumn()] = $oLocale->getLanguage();
        $aData[$this->getLocaleRegionColumn()]   = $oLocale->getRegion();
        unset($aData['locale']);

        if (empty($aData[$this->getColumn('id')])) {
            $oDb->set($this->getColumn('id'), null);
            $oDb->insert(parent::getTableName());
            $aData[$this->getColumn('id')] = $oDb->insert_id();
            if (empty($aData[$this->getColumn('id')])) {
                throw new ModelException(
                    'Failed to generate parent item for localised object'
                );
            }
            $bCreatedItem = true;
        }

        /**
         * This is to prevent primary key conflicts if a previously deleted item still
         * exists in the table
         */
        if (!$this->isDestructiveDelete()) {
            $this->destroy($aData[$this->getColumn('id')], $oLocale);
        }

        /**
         * Ensure automatic slug generation takes into account locale
         */
        if ($this->isAutoSetSlugs() && empty($aData[$this->getColumn('slug')])) {
            $aData[$this->getColumn('slug')] = $this->generateSlug($aData, null, $oLocale);
        }

        $iItemId = parent::create($aData, false);

        if (empty($iItemId)) {
            if (!empty($bCreatedItem)) {
                $oDb->where($this->getColumn('id'), $aData[$this->getColumn('id')]);
                $oDb->delete(parent::getTableName());
            }
            return null;
        } elseif (!$bReturnObject) {
            return $iItemId;
        }

        return $this->getById($iItemId, ['USE_LOCALE' => $oLocale]);
    }

    // --------------------------------------------------------------------------

    /**
     * Updates an existing object
     *
     * @param int                               $iId     The ID of the object to update
     * @param array                             $aData   The data to update the object with
     * @param \Nails\Common\Factory\Locale|null $oLocale The locale of the object being updated
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function update($iId, array $aData = [], \Nails\Common\Factory\Locale $oLocale = null): bool
    {
        if (empty($oLocale)) {
            throw new ModelException(
                self::class . ': A locale must be defined when updating a localised item'
            );
        }

        unset($aData['locale']);

        /**
         * Ensure automatic slug generation takes into account locale
         */
        if ($this->isAutoSetSlugs() && empty($aData[$this->getColumn('slug')]) && !static::AUTO_SET_SLUG_IMMUTABLE) {
            $aData[$this->getColumn('slug')] = $this->generateSlug($aData, $iId, $oLocale);
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where($this->getLocaleLanguageColumn(), $oLocale->getLanguage());
        $oDb->where($this->getLocaleRegionColumn(), $oLocale->getRegion());

        return parent::update($iId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Marks an object as deleted
     *
     * @param int                               $iId     The ID of the object to mark as deleted
     * @param \Nails\Common\Factory\Locale|null $oLocale The locale of the object being deleted
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function delete($iId, \Nails\Common\Factory\Locale $oLocale = null): bool
    {
        if (empty($oLocale)) {
            throw new ModelException(
                self::class . ': A locale must be defined when deleting a localised item'
            );
        }

        /**
         * An item can be deleted if any of the following are true:
         * - It is the only item
         * - It is not the default locale
         */

        /** @var Locale $oLocale */
        $oLocaleService = Factory::service('Locale');
        $oItem          = $this->getById($iId, ['USE_LOCALE' => $oLocale]);

        if (count($oItem->available_locales) === 1 || $oLocaleService->getDefautLocale() !== $oLocale) {

            if ($this->isDestructiveDelete()) {
                $bResult = $this->destroy($iId, $oLocale);
            } else {
                $bResult = $this->update(
                    $iId,
                    [$this->getColumnIsDeleted() => true],
                    $oLocale
                );
            }

            if ($bResult) {
                $this->triggerEvent(static::EVENT_DELETED, [$iId, $oLocale]);
                return true;
            }

            return false;

        } elseif (count($oItem->available_locales) > 1 && $oLocaleService->getDefautLocale() === $oLocale) {
            throw new ModelException(
                'Item cannot be deleted as it is the default item and other items still exist.'
            );
        } else {
            throw new ModelException(
                'Item cannot be deleted'
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Permanently deletes an object
     *
     * @param int                               $iId     The ID  of the object to destroy
     * @param \Nails\Common\Factory\Locale|null $oLocale The locale of the item being destroyed
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function destroy($iId, \Nails\Common\Factory\Locale $oLocale = null): bool
    {
        if (empty($oLocale)) {
            throw new ModelException(
                self::class . ': A locale must be defined when deleting a localised item'
            );
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where($this->getLocaleLanguageColumn(), $oLocale->getLanguage());
        $oDb->where($this->getLocaleRegionColumn(), $oLocale->getRegion());

        return parent::destroy($iId);
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * @param int                               $iId     The ID of the object to restore
     * @param \Nails\Common\Factory\Locale|null $oLocale The locale of the item being restored
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function restore($iId, \Nails\Common\Factory\Locale $oLocale = null): bool
    {
        if (empty($oLocale)) {
            throw new ModelException(
                'A locale must be defined when restoring a localised item'
            );

        } elseif ($this->isDestructiveDelete()) {
            throw new ModelException(
                'Restore not available when destructive delete is enabled'
            );

        } elseif ($this->update($iId, [$this->getColumnIsDeleted() => false], $oLocale)) {
            $this->triggerEvent(static::EVENT_RESTORED, [$iId]);
            return true;
        }

        return false;
    }
}
