<?php

/**
 * Implements the common getcount_common() and getCountCommonParseSort() methods
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Traits;

use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Model\Condition;
use Nails\Common\Helper\Model\GroupBy;
use Nails\Common\Helper\Model\Having;
use Nails\Common\Helper\Model\Like;
use Nails\Common\Helper\Model\NotLike;
use Nails\Common\Helper\Model\OrHaving;
use Nails\Common\Helper\Model\OrLike;
use Nails\Common\Helper\Model\OrNotLike;
use Nails\Common\Helper\Model\OrWhere;
use Nails\Common\Helper\Model\OrWhereIn;
use Nails\Common\Helper\Model\OrWhereNotIn;
use Nails\Common\Helper\Model\Paginate;
use Nails\Common\Helper\Model\Sort;
use Nails\Common\Helper\Model\Where;
use Nails\Common\Helper\Model\WhereIn;
use Nails\Common\Service\Database;
use Nails\Factory;

/**
 * Trait GetCountCommon
 *
 * @package Nails\Common\Traits
 */
trait GetCountCommon
{
    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     *
     * @param array $aData Data passed from the calling method
     **/
    protected function getCountCommon(array &$aData = []): void
    {
        $this->getCountCommonCompileSelect($aData);
        $this->getCountCommonCompileFilters($aData);
        $this->getCountCommonCompileWheres($aData);
        $this->getCountCommonCompileLikes($aData);
        $this->getCountCommonCompileHavings($aData);
        $this->getCountCommonCompileSort($aData);
        $this->getCountCommonCompileGroupBy($aData);
        $this->getCountCommonCompileLimit($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the select statement
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileSelect(array &$aData): void
    {
        if (!empty($aData['select'])) {
            /** @var Database $oDb */
            $oDb = Factory::service('Database');
            $oDb->select($aData['select']);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any active filters back into the $aData array
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileFilters(array &$aData): void
    {
        /**
         * Handle filters
         * Filters are basically an easy way to modify the query's where element.
         */

        //  Checkbox Filters
        if (!empty($aData['cbFilters'])) {

            foreach ($aData['cbFilters'] as $iFilterIndex => $oFilter) {

                /**
                 * If a column isn't specified by the filter then ignore it. This is a
                 * feature/hack to allow the dev to add items to the search box but not
                 * force them to use the default filtering mechanism
                 */
                if (empty($oFilter->getColumn())) {
                    continue;
                }

                $aWhereFilter = [];
                foreach ($oFilter->getOptions() as $iOptionIndex => $oOption) {
                    if (!empty($_GET['cbF'][$iFilterIndex][$iOptionIndex])) {
                        //  Filtering is happening and the item is to be filtered
                        $aWhereFilter[] = $this->getCountCommonCompileFiltersString(
                            $oFilter->getColumn(),
                            $oOption->getValue(),
                            $oOption->isQuery()
                        );
                    } elseif (empty($_GET['cbF']) && $oOption->isSelected()) {
                        //  There's no filtering happening and the item is checked by default
                        $aWhereFilter[] = $this->getCountCommonCompileFiltersString(
                            $oFilter->getColumn(),
                            $oOption->getValue(),
                            $oOption->isQuery()
                        );
                    }
                }

                $aWhereFilter = array_filter($aWhereFilter);

                if (!empty($aWhereFilter)) {

                    if (!isset($aData['where'])) {
                        $aData['where'] = [];
                    }

                    $aData['where'][] = '(' . implode(' OR ', $aWhereFilter) . ')';
                }
            }
        }

        //  Dropdown Filters
        if (!empty($aData['ddFilters'])) {

            foreach ($aData['ddFilters'] as $iFilterIndex => $oFilter) {

                /**
                 * If a column isn't specified by the filter then ignore it. This is a
                 * feature/hack to allow the dev to add items to the search box but not
                 * force them to use the default filtering mechanism
                 */
                if (empty($oFilter->getColumn())) {
                    continue;
                }

                $sWhereFilter = [];

                //  Are we even filtering this filter?
                if (isset($_GET['ddF'][$iFilterIndex])) {

                    //  Does the option exist, if so, filter by it
                    $iSelectedIndex = !empty((int) $_GET['ddF'][$iFilterIndex]) ? (int) $_GET['ddF'][$iFilterIndex] : 0;
                    $oOption        = $oFilter->getOption($iSelectedIndex);

                    if ($oOption && $oOption->getValue() !== null) {
                        $sWhereFilter = $this->getCountCommonCompileFiltersString(
                            $oFilter->getColumn(),
                            $oOption->getValue(),
                            $oOption->isQuery()
                        );
                    }

                } else {

                    //  No filtering happening but does this item have an item checked by default?
                    foreach ($oFilter->getOptions() as $oOption) {
                        if ($oOption->isSelected()) {
                            $sWhereFilter = $this->getCountCommonCompileFiltersString(
                                $oFilter->getColumn(),
                                $oOption->getValue(),
                                $oOption->isQuery()
                            );
                            break;
                        }
                    }
                }

                if (!empty($sWhereFilter)) {

                    if (!isset($aData['where'])) {
                        $aData['where'] = [];
                    }

                    $aData['where'][] = $sWhereFilter;
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the filter string
     *
     * @param string  $sColumn  The column
     * @param mixed   $mValue   The value
     * @param boolean $bIsQuery Whether the value is an SQL wuery or not
     *
     * @return string
     */
    protected function getCountCommonCompileFiltersString($sColumn, $mValue, $bIsQuery): string
    {
        if (is_object($mValue) && ($mValue instanceof \Closure)) {
            $mValue = $mValue();
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        if ($bIsQuery) {
            $aBits = [$mValue];
        } elseif (!is_array($mValue)) {
            $aBits = [
                strpos($sColumn, '`') === false ? $oDb->escape_str($sColumn, false) : $sColumn,
                '=',
                $oDb->escape($mValue),
            ];
        } else {
            $sOperator = ArrayHelper::get(0, $mValue);
            $sValue    = ArrayHelper::get(1, $mValue);
            $bEscape   = (bool) ArrayHelper::get(2, $mValue, true);
            $aBits     = [
                $oDb->escape_str($sColumn, false),
                $sOperator,
                $bEscape ? $oDb->escape($sValue) : $sValue,
            ];
        }

        return trim(implode(' ', $aBits));
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any where's into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileWheres(array &$aData): void
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        /**
         * Parse utility classes into the main data array
         */
        $aMap = [
            Condition::class    => 'where',
            Where::class        => 'where',
            WhereIn::class      => 'where_in',
            WhereNotIn::class   => 'where_not_in',
            OrWhere::class      => 'or_where',
            OrWhereIn::class    => 'or_where_in',
            OrWhereNotIn::class => 'or_where_not_in',
        ];

        $this->parseUtilityClasses($aData, $aMap);

        /**
         * Handle where's
         *
         * This is an array of the various type of where that can be passed in via $aData.
         * The first element is the key and the second is how multiple items within the
         * group should be glued together.
         *
         * Each type of where is grouped in it's own set of parenthesis, multiple groups
         * are glued together with ANDs
         */

        $aWheres = [
            $aMap[Where::class]        => 'AND',
            $aMap[WhereIn::class]      => 'AND',
            $aMap[WhereNotIn::class]   => 'AND',
            $aMap[OrWhere::class]      => 'OR',
            $aMap[OrWhereIn::class]    => 'OR',
            $aMap[OrWhereNotIn::class] => 'OR',
        ];

        $aWhereCompiled = [];

        foreach ($aWheres as $sWhereType => $sWhereGlue) {

            if (!empty($aData[$sWhereType])) {

                $aWhereCompiled[$sWhereType] = [];

                if (is_array($aData[$sWhereType])) {

                    /**
                     * The value is an array. For each element we need to compile as appropriate
                     * and add to $aWhereCompiled.
                     */

                    foreach ($aData[$sWhereType] as $mWhere) {

                        if (is_string($mWhere)) {

                            /**
                             * The value is a straight up string, assume this is a compiled
                             * where string,
                             */
                            $aWhereCompiled[$sWhereType][] = $mWhere;

                        } else {

                            /**
                             * The value is an array, try and determine the various parts
                             * of the query. We use strings which are unlikely to be found
                             * as falsey values (such as null) are perfectly likely.
                             */
                            $sColumn   = $this->extractColumn($mWhere);
                            $sOperator = $this->extractOperator($mWhere);
                            $mVal      = $this->extractValue($mWhere);
                            $bEscape   = $this->extractEscape($mWhere);

                            if ($sColumn) {

                                switch ($sWhereType) {

                                    case $aMap[Where::class]:
                                    case $aMap[OrWhere::class]:

                                        if ($bEscape) {
                                            $mVal = $oDb->escape($mVal);
                                        }

                                        $aWhereCompiled[$sWhereType][] = $sColumn . $sOperator . $mVal;
                                        break;

                                    case $aMap[WhereIn::class]:
                                    case $aMap[OrWhereIn::class]:

                                        if (!is_array($mVal)) {
                                            $mVal = (array) $mVal;
                                        }

                                        if (!empty($mVal)) {
                                            if ($bEscape) {
                                                foreach ($mVal as &$sValue) {
                                                    $sValue = $oDb->escape($sValue);
                                                }
                                            }

                                            $aWhereCompiled[$sWhereType][] = $sColumn . ' IN (' . implode(',', $mVal) . ')';
                                        }
                                        break;

                                    case $aMap[WhereNotIn::class]:
                                    case $aMap[OrWhereNotIn::class]:

                                        if (!is_array($mVal)) {
                                            $mVal = (array) $mVal;
                                        }

                                        if (!empty($mVal)) {
                                            if ($bEscape) {
                                                foreach ($mVal as &$sValue) {
                                                    $sValue = $oDb->escape($sValue);
                                                }
                                            }

                                            $aWhereCompiled[$sWhereType][] = $sColumn . ' NOT IN (' . implode(',', $mVal) . ')';
                                        }
                                        break;
                                }
                            }
                        }
                    }

                } elseif (is_string($aData[$sWhereType])) {

                    /**
                     * The value is a straight up string, assume this is a compiled
                     * where string,
                     */

                    $aWhereCompiled[$sWhereType][] = $aData[$sWhereType];
                }
            }
        }

        /**
         * Now we need to compile all the conditionals into one big super query.
         * $aWhereStr is an array of the compressed where strings... will make
         * sense shortly...
         */

        if (!empty($aWhereCompiled)) {

            $aWhereStr = [];

            foreach ($aWhereCompiled as $sWhereType => $sValue) {
                if (!empty($sValue)) {
                    $aWhereStr[] = '(' . implode(' ' . $aWheres[$sWhereType] . ' ', $sValue) . ')';
                }
            }

            //  And reduce $aWhereStr to an actual string, like the name suggests
            if (!empty($aWhereStr)) {
                $aWhereStr = implode(' AND ', $aWhereStr);
                $oDb->where($aWhereStr, null, false);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any like's into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileLikes(array &$aData): void
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        /**
         * Parse utility classes into the main data array
         */
        $aMap = [
            Like::class      => 'like',
            NotLike::class   => 'not_like',
            OrLike::class    => 'or_like',
            OrNotLike::class => 'or_not_like',
        ];

        $this->parseUtilityClasses($aData, $aMap);

        $aLikes = [
            $aMap[Like::class]      => 'AND',
            $aMap[NotLike::class]   => 'AND',
            $aMap[OrLike::class]    => 'OR',
            $aMap[OrNotLike::class] => 'OR',
        ];

        $aLikeCompiled = [];

        foreach ($aLikes as $sLikeType => $sLikeGlue) {

            if (!empty($aData[$sLikeType])) {

                $aLikeCompiled[$sLikeType] = [];

                if (is_array($aData[$sLikeType])) {

                    /**
                     * The value is an array. For each element we need to compile as appropriate
                     * and add to $aLikeCompiled.
                     */

                    foreach ($aData[$sLikeType] as $mWhere) {

                        if (is_string($mWhere)) {

                            /**
                             * The value is a straight up string, assume this is a compiled
                             * where string,
                             */

                            $aLikeCompiled[$sLikeType][] = $mWhere;

                        } else {

                            /**
                             * The value is an array, try and determine the various parts
                             * of the query. We use strings which are unlikely to be found
                             * as falsey values (such as null) are perfectly likely.
                             */
                            $sColumn = $this->extractColumn($mWhere);
                            $mVal    = $this->extractValue($mWhere);
                            $bEscape = $this->extractEscape($mWhere);

                            if ($bEscape) {
                                $mVal = $oDb->escape_like_str($mVal);
                            }

                            //  Got something?
                            if ($sColumn) {
                                switch ($sLikeType) {

                                    case $aMap[Like::class]:
                                    case $aMap[OrLike::class]:
                                        $aLikeCompiled[$sLikeType][] = $sColumn . ' LIKE "%' . $mVal . '%"';
                                        break;

                                    case $aMap[NotLike::class]:
                                    case $aMap[OrNotLike::class]:
                                        $aLikeCompiled[$sLikeType][] = $sColumn . ' NOT LIKE "%' . $mVal . '%"';
                                        break;
                                }
                            }
                        }
                    }

                } elseif (is_string($aData[$sLikeType])) {

                    /**
                     * The value is a straight up string, assume this is a compiled
                     * where string,
                     */

                    $aLikeCompiled[$sLikeType][] = $aData[$sLikeType];
                }
            }
        }

        /**
         * Now we need to compile all the conditionals into one big super query.
         * $aWhereStr is an array of the compressed where strings... will make
         * sense shortly...
         */

        if (!empty($aLikeCompiled)) {

            $aWhereStr = [];

            foreach ($aLikeCompiled as $sLikeType => $sValue) {
                $aWhereStr[] = '(' . implode(' ' . $aLikes[$sLikeType] . ' ', $sValue) . ')';
            }

            //  And reduce $aWhereStr to an actual string, like the name suggests
            $aWhereStr = implode(' AND ', $aWhereStr);
            $oDb->where($aWhereStr);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any having's into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileHavings(array &$aData): void
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        /**
         * Parse utility classes into the main data array
         */
        $aMap = [
            Having::class   => 'having',
            OrHaving::class => 'or_having',
        ];

        $this->parseUtilityClasses($aData, $aMap);

        /**
         * Handle having's
         *
         * This is an array of the various type of having that can be passed in via $aData.
         * The first element is the key and the second is how multiple items within the
         * group should be glued together.
         *
         * Each type of having is grouped in it's own set of parenthesis, multiple groups
         * are glued together with ANDs
         */

        $aHavings = [
            $aMap[Having::class]   => 'AND',
            $aMap[OrHaving::class] => 'OR',
        ];

        $aHavingCompiled = [];

        foreach ($aHavings as $sHavingType => $sHavingGlue) {

            if (!empty($aData[$sHavingType])) {

                $aHavingCompiled[$sHavingType] = [];

                if (is_array($aData[$sHavingType])) {

                    /**
                     * The value is an array. For each element we need to compile as appropriate
                     * and add to $aHavingCompiled.
                     */

                    foreach ($aData[$sHavingType] as $mHaving) {

                        if (is_string($mHaving)) {

                            /**
                             * The value is a straight up string, assume this is a compiled
                             * having string,
                             */

                            $aHavingCompiled[$sHavingType][] = $mHaving;

                        } else {

                            /**
                             * The value is an array, try and determine the various parts
                             * of the query. We use strings which are unlikely to be found
                             * as falsey values (such as null) are perfectly likely.
                             */

                            $sColumn   = $this->extractColumn($mHaving);
                            $sOperator = $this->extractOperator($mHaving);
                            $mVal      = $this->extractValue($mHaving);
                            $bEscape   = $this->extractEscape($mHaving);

                            if ($bEscape) {
                                $mVal = $oDb->escape($mVal);
                            }

                            //  Got something?
                            if ($sColumn) {

                                switch ($sHavingType) {

                                    case $aMap[Having::class]:
                                    case $aMap[OrHaving::class]:
                                        $aHavingCompiled[$sHavingType][] = $sColumn . $sOperator . $mVal;
                                        break;
                                }
                            }
                        }
                    }

                } elseif (is_string($aData[$sHavingType])) {

                    /**
                     * The value is a straight up string, assume this is a compiled
                     * having string,
                     */

                    $aHavingCompiled[$sHavingType][] = $aData[$sHavingType];
                }
            }
        }

        /**
         * Now we need to compile all the conditionals into one big super query.
         * $aHavingStr is an array of the compressed having strings... will make
         * sense shortly...
         */

        if (!empty($aHavingCompiled)) {

            $aHavingStr = [];

            foreach ($aHavingCompiled as $sHavingType => $sValue) {
                if (!empty($sValue)) {
                    $aHavingStr[] = '(' . implode(' ' . $aHavings[$sHavingType] . ' ', $sValue) . ')';
                }
            }

            //  And reduce $aHavingStr to an actual string, like the name suggests
            if (!empty($aHavingStr)) {
                $aHavingStr = implode(' AND ', $aHavingStr);
                $oDb->having($aHavingStr);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the sort element into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileSort(array &$aData): void
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        /**
         * Parse utility classes into the main data array
         */
        $aMap = [
            Sort::class => 'sort',
        ];

        $this->parseUtilityClasses($aData, $aMap);

        if (!empty($aData['sort'])) {

            /**
             * How we handle sorting
             * =====================
             *
             * - If $aData['sort'] is a string assume it's the field to sort on, use the default order
             * - If $aData['sort'] is a single dimension array then assume the first element (or the element
             *   named 'column') is the column; and the second element (or the element named 'order') is the
             *   direction to sort in
             * - If $aData['sort'] is a multidimensional array then loop each element and test as above.
             *
             **/

            if (is_string($aData['sort'])) {

                //  String
                $oDb->order_by($aData['sort']);

            } elseif (is_array($aData['sort'])) {

                $mColumn = $aData['sort']['column'] ?? reset($aData['sort']);

                if (is_string($mColumn)) {

                    //  Single dimension array
                    $aSort = $this->getCountCommonParseSort($aData['sort']);

                    if (!empty($aSort['column'])) {
                        $oDb->order_by(
                            $aSort['column'],
                            $aSort['order'],
                            $aSort['escape']
                        );
                    }

                } else {

                    //  Multi dimension array
                    foreach ($aData['sort'] as $sort) {

                        $aSort = $this->getCountCommonParseSort($sort);

                        if (!empty($aSort['column'])) {
                            $oDb->order_by(
                                $aSort['column'],
                                $aSort['order'],
                                $aSort['escape']
                            );
                        }
                    }
                }
            }

        }
    }

    // --------------------------------------------------------------------------

    /**
     * Parse the sort variable
     *
     * @param string|array $mSort The sort variable
     *
     * @return array
     */
    protected function getCountCommonParseSort($mSort): array
    {
        $aOut = [
            'column' => '',
            'order'  => '',
            'escape' => true,
        ];

        // --------------------------------------------------------------------------

        if (is_string($mSort)) {
            $aOut['column'] = $mSort;
            return $aOut;
        }

        // --------------------------------------------------------------------------

        $aOut['column'] = $mSort['column'] ?? reset($mSort);

        if ($aOut['column']) {
            $aOut['order']  = strtoupper($mSort['order'] ?? $mSort[1] ?? $aOut['order']);
            $aOut['escape'] = (bool) ($mSort['escape'] ?? $mSort[2] ?? $aOut['escape']);
        }

        // --------------------------------------------------------------------------

        //  Filter out bad sort values
        if (!empty($aOut['order']) && !in_array($aOut['order'], ['ASC', 'DESC', 'RANDOM'])) {
            $aOut['order'] = '';
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the group element into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileGroupBy(array &$aData): void
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        $aMap = [
            GroupBy::class => 'group',
        ];

        $this->parseUtilityClasses($aData, $aMap);

        if (!empty($aData['group'])) {
            if (is_string($aData['group'])) {
                $oDb->order_by($aData['group']);
            } elseif (is_array($aData['group'])) {
                foreach ($aData['group'] as $sColumn) {
                    $oDb->group_by($sColumn);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the limit element into the query
     *
     * @param array &$aData The data array
     */
    protected function getCountCommonCompileLimit(array &$aData): void
    {
        $aMap = [
            Paginate::class => 'limit',
        ];

        $this->parseUtilityClasses($aData, $aMap, false);
    }

    // --------------------------------------------------------------------------

    /**
     * Parse utility clases into their appropriate array element
     *
     * @param array $aData    The data array
     * @param array $aMap     The map of classes to keys
     * @param bool  $bIsArray Whether the target item is an array
     */
    protected function parseUtilityClasses(array &$aData, array $aMap, bool $bIsArray = true)
    {
        $aClasses = array_keys($aMap);

        foreach ($aData as &$aDatum) {

            if (is_object($aDatum)) {

                $sDatumClass = get_class($aDatum);

                if (in_array($sDatumClass, $aClasses)) {
                    if (!array_key_exists($aMap[$sDatumClass], $aData)) {
                        $aData[$aMap[$sDatumClass]] = $bIsArray ? [] : null;
                    }

                    if ($bIsArray) {
                        $aData[$aMap[$sDatumClass]][] = $aDatum->compile();
                    } else {
                        $aData[$aMap[$sDatumClass]] = $aDatum->compile();
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Protects a column if it is a reserved word
     *
     * @param string $sColumn The column to protect
     *
     * @return string
     * @throws FactoryException
     */
    protected function protectColumn(string $sColumn): string
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        return in_array(strtoupper($sColumn), $oDb->getReservedWords())
            ? '`' . $sColumn . '`'
            : $sColumn;
    }
    // --------------------------------------------------------------------------

    /**
     * Extracts the column value from the query data
     *
     * @param array $aData the query data
     *
     * @return string
     */
    protected function extractColumn($aData): string
    {
        $mColumn = isset($aData['column'])
            ? $aData['column'] :
            '[NAILS-COL-NOT-FOUND]';

        if ($mColumn === '[NAILS-COL-NOT-FOUND]') {
            $mColumn = isset($aData[0]) && is_string($aData[0]) ? $aData[0] : null;
        }

        //  If the $mColumn is an array then we should concat them together
        $sColumn = is_array($mColumn)
            ? 'CONCAT_WS(" ", ' . implode(',', $sColumn) . ')'
            : $mColumn;

        //  Test if there's an SQL operator
        if (!(bool) preg_match('/(<=|>=|<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($sColumn))) {
            $sColumn = trim($sColumn);

        } else {
            preg_match('/(.*)(<=|>=|<|>)$/i', trim($sColumn), $aMatches);
            $sColumn = trim($aMatches[1]);
        }

        return $this->protectColumn($sColumn);
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the operator value from the query data
     *
     * @param array $aData The query data
     *
     * @return string
     */
    protected function extractOperator($aData): string
    {
        $mColumn = isset($aData['column']) ? $aData['column'] : '[NAILS-COL-NOT-FOUND]';

        if ($mColumn === '[NAILS-COL-NOT-FOUND]') {
            $mColumn = isset($aData[0]) && is_string($aData[0]) ? $aData[0] : null;
        }

        $mVal = $this->extractValue($aData);

        //  Test if there's an SQL operator
        if (!(bool) preg_match('/(<=|>=|<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($mColumn))) {
            $sOperator = is_null($mVal) ? ' IS ' : '=';

        } else {
            preg_match('/(.*)(<=|>=|<|>)$/i', trim($mColumn), $matches);
            $sOperator = trim($matches[2] ?? '=');
        }

        return $sOperator;
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the value from the query data
     *
     * @param array $aData The query data
     *
     * @return mixed
     */
    protected function extractValue($aData)
    {
        //  Work out value
        $mVal = isset($aData['value']) ? $aData['value'] : '[NAILS-VAL-NOT-FOUND]';

        if ($mVal === '[NAILS-VAL-NOT-FOUND]') {
            $mVal = isset($aData[1]) ? $aData[1] : null;
        }

        return $mVal;
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the escape value from the query data
     *
     * @param array $aData The query data
     *
     * @return bool
     */
    protected function extractEscape($aData): bool
    {
        $bEscape = isset($aData['escape']) ? (bool) $aData['escape'] : '[NAILS-ESCAPE-NOT-FOUND]';

        if ($bEscape === '[NAILS-ESCAPE-NOT-FOUND]') {
            $bEscape = isset($aData[2]) ? $aData[2] : true;
        }

        return $bEscape;
    }
}
