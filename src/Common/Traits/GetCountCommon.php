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

use Nails\Factory;

trait GetCountCommon
{
    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     *
     * @param  array $aData Data passed from the calling method
     *
     * @return void
     **/
    protected function getCountCommon($aData = [])
    {
        //  @deprecated - searching should use the search() method, but this in place
        //  as a quick fix for loads of admin controllers
        if (!empty($aData['keywords']) && !empty($this->searchableFields)) {
            if (empty($aData['or_like'])) {
                $aData['or_like'] = [];
            }

            $sAlias = $this->getTableAlias(true);

            foreach ($this->searchableFields as $mField) {

                //  If the field is an array then search across the columns concatenated together
                if (is_array($mField)) {

                    $sMappedFields = array_map(function ($sInput) use ($sAlias) {
                        if (strpos($sInput, '.') !== false) {
                            return $sInput;
                        } else {
                            return $sAlias . $sInput;
                        }
                    }, $mField);

                    $aData['or_like'][] = ['CONCAT_WS(" ", ' . implode(',', $sMappedFields) . ')', $aData['keywords']];

                } else {
                    if (strpos($mField, '.') !== false) {
                        $aData['or_like'][] = [$mField, $aData['keywords']];
                    } else {
                        $aData['or_like'][] = [$sAlias . $mField, $aData['keywords']];
                    }
                }
            }
        }

        // --------------------------------------------------------------------------

        $this->getCountCommonCompileSelect($aData);
        $this->getCountCommonCompileFilters($aData);
        $this->getCountCommonCompileWheres($aData);
        $this->getCountCommonCompileLikes($aData);
        $this->getCountCommonCompileSort($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the select statement
     *
     * @param  array &$aData The data array
     *
     * @return void
     */
    protected function getCountCommonCompileSelect(&$aData)
    {
        if (!empty($aData['select'])) {
            $oDb = Factory::service('Database');
            $oDb->select($aData['select']);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any active filters back into the $data array
     *
     * @param  array &$aData The data array
     *
     * @return void
     */
    protected function getCountCommonCompileFilters(&$aData)
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

                if (empty($oFilter->column)) {
                    continue;
                }

                $aWhereFilter = [];
                foreach ($oFilter->options as $iOptionIndex => $oOption) {
                    if (!empty($_GET['cbF'][$iFilterIndex][$iOptionIndex])) {
                        //  Filtering is happening and the item is to be filtered
                        $aWhereFilter[] = $this->getCountCommonCompileFiltersString($oFilter->column, $oOption->value);
                    } elseif (empty($_GET['cbF']) && $oOption->checked) {
                        //  There's no filtering happening and the item is checked by default
                        $aWhereFilter[] = $this->getCountCommonCompileFiltersString($oFilter->column, $oOption->value);
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

                if (empty($oFilter->column)) {
                    continue;
                }

                $aWhereFilter = [];

                //  Are we even filtering this filter?
                if (isset($_GET['ddF'][$iFilterIndex])) {

                    //  Does the option exist, if so, filter by it
                    $iSelectedIndex = !empty((int) $_GET['ddF'][$iFilterIndex]) ? (int) $_GET['ddF'][$iFilterIndex] : 0;
                    if (!empty($oFilter->options[$iSelectedIndex]->value)) {
                        $aWhereFilter = [$oFilter->column, $oFilter->options[$iSelectedIndex]->value];
                    }

                } else {

                    //  No filtering happening but does this item have an item checked by default?
                    foreach ($oFilter->options as $oOption) {
                        if (!empty($oOption->checked)) {
                            $aWhereFilter = [$oFilter->column, $oOption->value];
                            break;
                        }
                    }
                }

                if (!empty($aWhereFilter)) {

                    if (!isset($aData['where'])) {
                        $aData['where'] = [];
                    }

                    $aData['where'][] = $aWhereFilter;
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    protected function getCountCommonCompileFiltersString($sColumn, $mValue)
    {
        $oDb = Factory::service('Database');
        if (!is_array($mValue)) {
            $aBits = [
                $oDb->escape_str($sColumn, false),
                '=',
                $oDb->escape($mValue),
            ];
        } else {
            $sOperator = getFromArray(0, $mValue);
            $sValue    = getFromArray(1, $mValue);
            $bEscape   = (bool) getFromArray(2, $mValue, true);
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
     * @param  array &$data The data array
     *
     * @return void
     */
    protected function getCountCommonCompileWheres(&$data)
    {
        $oDb = Factory::service('Database');

        /**
         * Handle where's
         *
         * This is an array of the various type of where that can be passed in via $data.
         * The first element is the key and the second is how multiple items within the
         * group should be glued together.
         *
         * Each type of where is grouped in it's own set of parenthesis, multiple groups
         * are glued together with ANDs
         */

        $wheres = [
            'where'           => 'AND',
            'or_where'        => 'OR',
            'where_in'        => 'AND',
            'or_where_in'     => 'OR',
            'where_not_in'    => 'AND',
            'or_where_not_in' => 'OR',
        ];

        $whereCompiled = [];

        foreach ($wheres as $whereType => $whereGlue) {

            if (!empty($data[$whereType])) {

                $whereCompiled[$whereType] = [];

                if (is_array($data[$whereType])) {

                    /**
                     * The value is an array. For each element we need to compile as appropriate
                     * and add to $whereCompiled.
                     */

                    foreach ($data[$whereType] as $where) {

                        if (is_string($where)) {

                            /**
                             * The value is a straight up string, assume this is a compiled
                             * where string,
                             */

                            $whereCompiled[$whereType][] = $where;

                        } else {

                            /**
                             * The value is an array, try and determine the various parts
                             * of the query. We use strings which are unlikely to be found
                             * as falsey values (such as null) are perfectly likely.
                             */

                            //  Work out column
                            $col = isset($where['column']) ? $where['column'] : '[NAILS-COL-NOT-FOUND]';

                            if ($col === '[NAILS-COL-NOT-FOUND]') {
                                $col = isset($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $val = isset($where['value']) ? $where['value'] : '[NAILS-VAL-NOT-FOUND]';

                            if ($val === '[NAILS-VAL-NOT-FOUND]') {
                                $val = isset($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : '[NAILS-ESCAPE-NOT-FOUND]';

                            if ($escape === '[NAILS-ESCAPE-NOT-FOUND]') {
                                $escape = isset($where[2]) ? $where[2] : true;
                            }

                            //  If the $col is an array then we should concat them together
                            if (is_array($col)) {
                                $col = 'CONCAT_WS(" ", ' . implode(',', $col) . ')';
                            }

                            //  Test if there's an SQL operator
                            if (!(bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($col))) {

                                $operator = is_null($val) ? ' IS ' : '=';

                            } else {

                                $operator = '';
                            }

                            //  Got something?
                            if ($col) {

                                switch ($whereType) {

                                    case 'where' :
                                    case 'or_where' :

                                        if ($escape) {
                                            $val = $oDb->escape($val);
                                        }

                                        $whereCompiled[$whereType][] = $col . $operator . $val;
                                        break;

                                    case 'where_in' :
                                    case 'or_where_in' :

                                        if (!is_array($val)) {
                                            $val = (array) $val;
                                        }

                                        if (!empty($val)) {
                                            if ($escape) {
                                                foreach ($val as &$value) {
                                                    $value = $oDb->escape($value);
                                                }
                                            }

                                            $whereCompiled[$whereType][] = $col . ' IN (' . implode(',', $val) . ')';
                                        }
                                        break;

                                    case 'where_not_in' :
                                    case 'or_where_not_in' :

                                        if (!is_array($val)) {
                                            $val = (array) $val;
                                        }

                                        if (!empty($val)) {
                                            if ($escape) {
                                                foreach ($val as &$value) {
                                                    $value = $oDb->escape($value);
                                                }
                                            }

                                            $whereCompiled[$whereType][] = $col . ' NOT IN (' . implode(',', $val) . ')';
                                        }
                                        break;
                                }
                            }
                        }
                    }

                } elseif (is_string($data[$whereType])) {

                    /**
                     * The value is a straight up string, assume this is a compiled
                     * where string,
                     */

                    $whereCompiled[$whereType][] = $data[$whereType];
                }
            }
        }

        /**
         * Now we need to compile all the conditionals into one big super query.
         * $whereStr is an array of the compressed where strings... will make
         * sense shortly...
         */

        if (!empty($whereCompiled)) {

            $whereStr = [];

            foreach ($whereCompiled as $whereType => $value) {
                if (!empty($value)) {
                    $whereStr[] = '(' . implode(' ' . $wheres[$whereType] . ' ', $value) . ')';
                }
            }

            //  And reduce $whereStr to an actual string, like the name suggests
            if (!empty($whereStr)) {
                $whereStr = implode(' AND ', $whereStr);
                $oDb->where($whereStr);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any like's into the query
     *
     * @param  array &$data The data array
     *
     * @return void
     */
    protected function getCountCommonCompileLikes(&$data)
    {
        $oDb = Factory::service('Database');

        $likes = [
            'like'        => 'AND',
            'or_like'     => 'OR',
            'not_like'    => 'AND',
            'or_not_like' => 'OR',
        ];

        $likeCompiled = [];

        foreach ($likes as $likeType => $likeGlue) {

            if (!empty($data[$likeType])) {

                $likeCompiled[$likeType] = [];

                if (is_array($data[$likeType])) {

                    /**
                     * The value is an array. For each element we need to compile as appropriate
                     * and add to $likeCompiled.
                     */

                    foreach ($data[$likeType] as $where) {

                        if (is_string($where)) {

                            /**
                             * The value is a straight up string, assume this is a compiled
                             * where string,
                             */

                            $likeCompiled[$likeType][] = $where;

                        } else {

                            /**
                             * The value is an array, try and determine the various parts
                             * of the query. We use strings which are unlikely to be found
                             * as falsey values (such as null) are perfectly likely.
                             */

                            //  Work out column
                            $col = isset($where['column']) ? $where['column'] : '[NAILS-COL-NOT-FOUND]';

                            if ($col === '[NAILS-COL-NOT-FOUND]') {
                                $col = isset($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $val = isset($where['value']) ? $where['value'] : '[NAILS-VAL-NOT-FOUND]';

                            if ($val === '[NAILS-VAL-NOT-FOUND]') {
                                $val = isset($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            if ($escape) {
                                $val = $oDb->escape_like_str($val);
                            }

                            //  If the $col is an array then we should concat them together
                            if (is_array($col)) {

                                $col = 'CONCAT_WS(" ", ' . implode(',', $col) . ')';
                            }

                            //  Got something?
                            if ($col) {
                                switch ($likeType) {

                                    case 'like' :
                                    case 'or_like' :

                                        $likeCompiled[$likeType][] = $col . ' LIKE "%' . $val . '%"';
                                        break;

                                    case 'not_like' :
                                    case 'or_not_like' :

                                        $likeCompiled[$likeType][] = $col . ' NOT LIKE "%' . $val . '%"';
                                        break;
                                }
                            }
                        }
                    }

                } elseif (is_string($data[$likeType])) {

                    /**
                     * The value is a straight up string, assume this is a compiled
                     * where string,
                     */

                    $likeCompiled[$likeType][] = $data[$likeType];
                }
            }
        }

        /**
         * Now we need to compile all the conditionals into one big super query.
         * $whereStr is an array of the compressed where strings... will make
         * sense shortly...
         */

        if (!empty($likeCompiled)) {

            $whereStr = [];

            foreach ($likeCompiled as $likeType => $value) {
                $whereStr[] = '(' . implode(' ' . $likes[$likeType] . ' ', $value) . ')';
            }

            //  And reduce $whereStr to an actual string, like the name suggests
            $whereStr = implode(' AND ', $whereStr);
            $oDb->where($whereStr);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the sort element into the query
     *
     * @param  array &$data The data array
     *
     * @return void
     */
    protected function getCountCommonCompileSort(&$data)
    {
        $oDb = Factory::service('Database');

        if (!empty($data['sort'])) {

            /**
             * How we handle sorting
             * =====================
             *
             * - If $data['sort'] is a string assume it's the field to sort on, use the default order
             * - If $data['sort'] is a single dimension array then assume the first element (or the element
             *   named 'column') is the column; and the second element (or the element named 'order') is the
             *   direction to sort in
             * - If $data['sort'] is a multidimensional array then loop each element and test as above.
             *
             **/

            if (is_string($data['sort'])) {

                //  String
                $oDb->order_by($data['sort']);

            } elseif (is_array($data['sort'])) {

                $mFirst = reset($data['sort']);

                if (is_string($mFirst)) {

                    //  Single dimension array
                    $aSort = $this->getCountCommonParseSort($data['sort']);

                    if (!empty($aSort['column'])) {
                        $oDb->order_by($aSort['column'], $aSort['order']);
                    }

                } else {

                    //  Multi dimension array
                    foreach ($data['sort'] as $sort) {

                        $aSort = $this->getCountCommonParseSort($sort);

                        if (!empty($aSort['column'])) {
                            $oDb->order_by($aSort['column'], $aSort['order']);
                        }
                    }
                }
            }

        }
    }

    // --------------------------------------------------------------------------

    protected function getCountCommonParseSort($sort)
    {
        $aOut = ['column' => null, 'order' => null];

        // --------------------------------------------------------------------------

        if (is_string($sort)) {

            $aOut['column'] = $sort;
            return $aOut;

        } elseif (isset($sort['column'])) {

            $aOut['column'] = $sort['column'];

        } else {

            //  Take the first element
            $aOut['column'] = reset($sort);
            $aOut['column'] = is_string($aOut['column']) ? $aOut['column'] : null;
        }

        if ($aOut['column']) {

            //  Determine order
            if (isset($sort['order'])) {

                $aOut['order'] = $sort['order'];

            } elseif (count($sort) > 1) {

                //  Take the last element
                $aOut['order'] = end($sort);
                $aOut['order'] = is_string($aOut['order']) ? $aOut['order'] : null;
            }
        }

        return $aOut;
    }
}
