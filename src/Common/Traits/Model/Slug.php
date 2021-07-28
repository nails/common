<?php

namespace Nails\Common\Traits\Model;

use Behat\Transliterator\Transliterator;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Resource;
use Nails\Common\Service\Database;
use Nails\Factory;

/**
 * Trait Slug
 *
 * @package Nails\Common\Traits\Model
 */
trait Slug
{
    /**
     * Whether to automatically set slugs
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_SLUG instead
     */
    protected $tableAutoSetSlugs;

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
     * Returns the column to use as the ID
     *
     * @return string
     */
    abstract public function getColumnId(): string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the label
     *
     * @return string|null
     */
    abstract public function getColumnLabel(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     *
     * @param bool $bIncludeAlias Whether to include the table's alias
     *
     * @throws ModelException
     * @return string
     */
    abstract public function getTableName(bool $bIncludeAlias = false): string;

    // --------------------------------------------------------------------------

    /**
     * Returns item(s) by a column and its value
     *
     * @param string $sColumn      The column to search on
     * @param mixed  $mValue       The value(s) to look for
     * @param array  $aData        Any additional data to pass in
     * @param bool   $bReturnsMany Whether the method expects to return a single item, or many
     *
     * @return Resource[]|Resource|null
     * @throws ModelException
     */
    abstract protected function getByColumn($sColumn, $mValue, array $aData, $bReturnsMany = false);

    // --------------------------------------------------------------------------

    /**
     * Sorts items into a specific order based on a specific column
     *
     * @param array  $aItems      The items to sort
     * @param array  $aInputOrder The order to sort them in
     * @param string $sColumn     The column to sort on
     *
     * @return array
     */
    abstract protected function sortItemsByColumn(array $aItems, array $aInputOrder, $sColumn): array;

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extracted column for a query
     *
     * @param string $sColumn         The column to extract
     * @param array  $aData           The query data
     * @param bool   $bIncludeDeleted Whether to include deleted items
     *
     * @return mixed[]
     * @throws FactoryException
     * @throws ModelException
     */
    abstract public function getAllColumn(string $sColumn, array $aData, bool $bIncludeDeleted = false): array;

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically generates slugs or not
     *
     * @return bool
     */
    public function isAutoSetSlugs()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetSlugs
        return $this->tableAutoSetSlugs ?? (defined('static::AUTO_SET_SLUG') ? static::AUTO_SET_SLUG : false);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the slug is immutable
     *
     * @return bool
     */
    public function isSlugImmutable(): bool
    {
        return defined('static::AUTO_SET_SLUG_IMMUTABLE') ? static::AUTO_SET_SLUG_IMMUTABLE : true;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the slug on the $aData array
     *
     * @param array $aData     The data being passed to the model
     * @param bool  $bIsCreate Whether the method is being called in a create context or not
     * @param int   $iIgnoreId The ID of an item to ignore
     *
     * @return $this
     * @throws ModelException
     */
    protected function setDataSlug(array &$aData, bool $bIsCreate = true, int $iIgnoreId = null): self
    {
        if ($this->isAutoSetSlugs() &&
            empty($aData[$this->getColumnSlug()]) &&
            ($bIsCreate || !$this->isSlugImmutable())
        ) {

            $sLabelColumn = $this->getColumnSlugSource();

            if (empty($sLabelColumn)) {
                throw new ModelException(static::class . '::create() `label` column not set');
            }

            /**
             * We only want to set slugs if:
             * - It is a create operation
             * - it is an update operation and the label column is defined (else, lease it as it is)
             */
            if ($bIsCreate || array_key_exists($this->getColumnSlugSource(), $aData)) {

                if (empty($aData[$sLabelColumn])) {
                    throw new ModelException(sprintf(
                        '%s::create() "%s" is required when automatically generating slugs.',
                        static::class,
                        $sLabelColumn
                    ));
                }

                $aData[$this->getColumnSlug()] = $this->generateSlug(
                    $aData,
                    $iIgnoreId
                );
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for slugs
     *
     * @return string|null
     */
    public function getColumnSlug(): ?string
    {
        /**
         * @todo (Pablo 2021-04-23) - enfore string (not string|null) when trait is
         * not added by default (i.e models must explicitly say they support slugs)
         */
        return $this->getColumn('slug', 'slug');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for generating slugs
     *
     * @return string|null
     * @throws ModelException
     */
    public function getColumnSlugSource(): ?string
    {
        /**
         * @todo (Pablo 2021-04-23) - enfore string (not string|null) when trait is
         * not added by default (i.e models must explicitly say they support slugs)
         */
        return $this->getColumnLabel();
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a unique slug
     *
     * @param array $aData     The data to use when generating a slug
     * @param int   $iIgnoreId The ID of an item to ignore
     *
     * @return string
     * @throws ModelException
     */
    protected function generateSlug(array $aData = [], int $iIgnoreId = null)
    {
        $sSlug    = $this->generateSlugBase($aData);
        $iCounter = 0;

        do {

            $sSlugTest = $iCounter
                ? $sSlug . '-' . $iCounter
                : $sSlug;

            $iCounter++;

        } while (!$this->isValidSlug($sSlugTest, $iIgnoreId, $aData));

        return $sSlugTest;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the slug's base, compiled from the label
     *
     * @param string $sLabel The item's label
     * @param string $sKey   The key to use from the supplied data array
     *
     * @return string
     * @throws ModelException
     */
    protected function generateSlugBase(array $aData, string $sKey = null): string
    {
        $sKey = $sKey ?? $this->getColumnSlugSource();

        if (!array_key_exists($sKey, $aData)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Key `%s` is required when generating slugs',
                    $sKey
                )
            );
        }

        return Transliterator::transliterate(
            getFromArray($sKey, $aData)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * checks whether a slug is valid
     *
     * @param string   $sSlug     The slug to check
     * @param int|null $iIgnoreId An ID to ignore when checking
     * @param array    $aData     Any data to pass to generateSlugHook()
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function isValidSlug(string $sSlug, int $iIgnoreId = null, array $aData = []): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        if ($iIgnoreId) {
            $oDb->where($this->getColumnId() . ' !=', $iIgnoreId);
        }

        $oDb->where($this->getColumnSlug(), $sSlug);
        $this->generateSlugHook($aData);

        return $oDb->count_all_results($this->getTableName()) === 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides a hook for the extending model to manipulate the query
     *
     * @param array $aData
     */
    protected function generateSlugHook(array $aData = []): void
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's slug
     *
     * @param string|null $sSlug The slug of the object to fetch
     * @param array       $aData Any data to pass to getCountCommon()
     *
     * @return Resource|null
     */
    public function getBySlug(?string $sSlug, array $aData = []): ?Resource
    {
        return !empty($sSlug)
            ? $this->getByColumn($this->getColumnSlug(), $sSlug, $aData)
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their slugs
     *
     * @param array $aSlugs              An array of slugs to fetch
     * @param array $aData               Any data to pass to getCountCommon()
     * @param bool  $bMaintainInputOrder Whether to maintain the input order
     *
     * @return Resource[]
     */
    public function getBySlugs($aSlugs, array $aData = [], $bMaintainInputOrder = false): array
    {
        $aItems = $this->getByColumn($this->getColumnSlug(), $aSlugs, $aData, true);
        return $bMaintainInputOrder
            ? $this->sortItemsByColumn($aItems, $aSlugs, $this->getColumnSlug())
            : $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the slugs of the objects returned by the query
     *
     * @param array $aData           The query data
     * @param bool  $bIncludeDeleted Whether to include deleted items
     *
     * @return string[]
     * @throws FactoryException
     * @throws ModelException
     */
    public function getSlugs(array $aData = [], bool $bIncludeDeleted = false): array
    {
        return $this->getAllColumn($this->getColumnSlug(), $aData, $bIncludeDeleted);
    }
}
