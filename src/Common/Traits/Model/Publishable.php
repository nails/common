<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Factory;

/**
 * Trait Publishable
 *
 * @package Nails\Common\Traits\Model
 */
trait Publishable
{
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
     * Fetches all objects and formats them, optionally paginated
     *
     * @param int|null|array $iPage           The page number of the results, if null then no pagination; also accepts an $aData array
     * @param int|null       $iPerPage        How many items per page of paginated results
     * @param mixed          $aData           Any data to pass to getCountCommon()
     * @param bool           $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     *
     * @return Resource[]
     */
    abstract public function getAll($iPage = null, $iPerPage = null, array $aData = [], bool $bIncludeDeleted = false): array;

    // --------------------------------------------------------------------------

    /**
     * Fetches all objects, optionally paginated. Returns the basic query object with no formatting.
     *
     * @param int|null|array $iPage           The page number of the results, if null then no pagination; also accepts an $aData array
     * @param int|null       $iPerPage        How many items per page of paginated results
     * @param array          $aData           Any data to pass to getCountCommon()
     * @param bool           $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     *
     * @return \CI_DB_mysqli_result
     */
    abstract public function getAllRawQuery($iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false): \CI_DB_mysqli_result;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the is_published flag
     *
     * @return string|null
     */
    public function getColumnIsPublished(): ?string
    {
        return $this->getColumn('is_published', 'is_published');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for the date an item is published
     *
     * @return string|null
     */
    public function getColumnDatePublished(): ?string
    {
        return $this->getColumn('date_published', 'date_published');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for the date an item expires
     *
     * @return string|null
     */
    public function getColumnDateExpire(): ?string
    {
        return $this->getColumn('date_expire', 'date_expire');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns only published items
     *
     * @param array $aData The query data
     *
     * @return Resource[]
     * @throws FactoryException
     */
    public function getAllPublished(array $aData): array
    {
        return $this->getAll(
            $this->addPublishedQueryConditionals($aData)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the raw query for only published items
     *
     * @param array $aData The query data
     *
     * @return \CI_DB_mysqli_result
     * @throws FactoryException
     */
    public function getAllPublishedRawQuery(array $aData): array
    {
        return $this->getAllRawQuery(
            $this->addPublishedQueryConditionals($aData)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns only unpublished items
     *
     * @param array $aData The query data
     *
     * @return Resource[]
     * @throws FactoryException
     */
    public function getAllUnpublished(array $aData): array
    {
        return $this->getAll(
            $this->addPublishedQueryConditionals($aData, false)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the raw query for only unpublished items
     *
     * @param array $aData The query data
     *
     * @return \CI_DB_mysqli_result
     * @throws FactoryException
     */
    public function getAllUnpublishedRawQuery(array $aData): array
    {
        return $this->getAllRawQuery(
            $this->addPublishedQueryConditionals($aData, false)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the query data for published/unpublished queries
     *
     * @param array $aData        The data to manipulate
     * @param bool  $bIsPublished Whether to query for published or unpublished items
     *
     * @return array
     * @throws FactoryException
     */
    protected function addPublishedQueryConditionals(array $aData, bool $bIsPublished = true): array
    {
        /** @var \DateTime $oNow */
        $oNow                 = Factory::factory('DateTime');
        $sColumnIsPublished   = $this->getColumnIsPublished();
        $sColumnDatePublished = $this->getColumnDatePublished();
        $sColumnDateExpire    = $this->getColumnDateExpire();

        if (empty($aData['where'])) {
            $aData['where'] = [];
        }

        if ($sColumnIsPublished) {
            $aData['where'][] = [$sColumnIsPublished, $bIsPublished];
        }

        if ($sColumnDatePublished) {
            $aData['where'][] = sprintf(
                '(`%1$s` IS NULL OR `%1$s` %2$s %3$s)',
                $sColumnDatePublished,
                $bIsPublished ? '<=' : '>',
                $oNow->format('Y-m-d H:i:s')
            );
        }

        if ($sColumnDateExpire) {
            $aData['where'][] = sprintf(
                '(`%1$s` IS NULL OR `%1$s` %2$s %3$s)',
                $sColumnDateExpire,
                $bIsPublished ? '>' : '<=',
                $oNow->format('Y-m-d H:i:s')
            );
        }

        return $aData;
    }
}
