<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Resource;

/**
 * Trait Searchable
 *
 * @package Nails\Common\Traits\Model
 */
trait Searchable
{
    /**
     * The columns which should be included when searching by keyword
     *
     * @var array
     * @deprecated
     */
    protected $searchableFields = [];

    // --------------------------------------------------------------------------

    /**
     * Returns an array of columns which should be searchable. Each field is passed
     * directly to the `column` parameter in getCountCommon() so can be in any form
     * accepted by that.
     *
     * @return string[]
     */
    abstract public function getSearchableColumns(): array;

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
     * Counts all objects
     *
     * @param array $aData           An array of data to pass to getCountCommon()
     * @param bool  $bIncludeDeleted Whether to include deleted objects or not
     *
     * @return int
     * @throws ModelException
     */
    abstract public function countAll(array $aData = [], bool $bIncludeDeleted = false): int;

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $tableAlias
     *
     * @param bool $bIncludeSeparator Whether to include the prefix separator
     *
     * @return string
     */
    abstract public function getTableAlias($bIncludeSeparator = false);

    // --------------------------------------------------------------------------

    /**
     * Returns the property to use to trigger search in the query object
     *
     * @return string
     */
    public function getSearchKey(): string
    {
        return 'keywords';
    }

    // --------------------------------------------------------------------------

    /**
     * Searches for objects, optionally paginated.
     *
     * @param string   $sKeywords        The search term
     * @param int|null $iPage            The page number of the results, if null then no pagination
     * @param int|null $iPerPage         How many items per page of paginated results
     * @param mixed    $aData            Any data to pass to getCountCommon()
     * @param bool     $bIncludeDeleted  If non-destructive delete is enabled then this flag allows you to include
     *                                   deleted items
     *
     * @return \stdClass
     */
    public function search($sKeywords, $iPage = null, $iPerPage = null, array $aData = [], bool $bIncludeDeleted = false)
    {
        //  If the second parameter is an array then treat as if called with search($sKeywords, null, null, $aData);
        if (is_array($iPage)) {
            $aData = $iPage;
            $iPage = null;
        }

        $aData[$this->getSearchKey()] = $sKeywords;

        return (object) [
            'page'    => $iPage,
            'perPage' => $iPerPage,
            'total'   => $this->countAll($aData),
            'data'    => $this->getAll($iPage, $iPerPage, $aData, $bIncludeDeleted),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Mutates the data array and adds the conditionals for searching
     *
     * @param array $aData The query data array
     *
     * @return $this
     */
    protected function applySearchConditionals(array &$aData): self
    {
        $sKey = $this->getSearchKey();
        if (empty($aData[$sKey])) {
            return $this;
        }

        $sKeywords = $aData[$sKey];

        if (empty($aData['or_like'])) {
            $aData['or_like'] = [];
        }

        $sAlias             = $this->getTableAlias(true);
        $aSearchableColumns = array_filter($this->getSearchableColumns());

        foreach ($aSearchableColumns as $mField) {

            //  If the field is an array then search across the columns concatenated together
            if (is_array($mField)) {

                $sMappedFields = array_map(function ($sInput) use ($sAlias) {
                    if (strpos($sInput, '.') !== false || preg_match('/^\(.*\)$/', $sInput)) {
                        return $sInput;
                    } else {
                        return $sAlias . $sInput;
                    }
                }, $mField);

                $aData['or_like'][] = ['CONCAT_WS(" ", ' . implode(',', $sMappedFields) . ')', $sKeywords];

            } elseif (preg_match('/^\(.*\)$/', $mField)) {
                $aData['or_like'][] = [$mField, $sKeywords];

            } elseif (strpos($mField, '.') !== false) {
                $aData['or_like'][] = [$mField, $sKeywords];

            } else {
                $aData['or_like'][] = [$sAlias . $mField, $sKeywords];
            }
        }

        return $this;
    }
}
