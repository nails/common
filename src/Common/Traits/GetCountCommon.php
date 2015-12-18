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
     * @param  array  $aData Data passed from the calling method
     * @return void
     **/
    protected function getCountCommon($aData = array())
    {
        $this->getCountCommonCompileFilters($aData);
        $this->getCountCommonCompileWheres($aData);
        $this->getCountCommonCompileLikes($aData);
        $this->getCountCommonCompileSort($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any active filters back into the $data array
     * @param  array  &$data The data array
     * @return void
     */
    protected function getCountCommonCompileFilters(&$data)
    {
        $oDb = Factory::service('Database');

        /**
         * Handle filters
         * Filters are basically an easy way to modify the query's where element.
         */

        //  Checkbox Filters
        if (!empty($data['cbFilters'])) {

            foreach ($data['cbFilters'] as $filterIndex => $filter) {

                /**
                 * If a column isn't specified by the filter then ignore it. This is a
                 * feature/hack to allow the dev to add items to the search box but not
                 * force them to use the default filtering mechanism
                 */

                if (empty($filter->column)) {
                    continue;
                }

                $whereFilter = array()  ;
                foreach ($filter->options as $optionIndex => $option) {

                    if (!empty($_GET['cbF'][$filterIndex][$optionIndex])) {

                        //  Filtering is happening and the item is to be filtered
                        $whereFilter[] = $oDb->escape_str($filter->column, false) . ' = ' . $oDb->escape($option->value);

                    } elseif (empty($_GET['cbF']) && $option->checked) {

                        //  There's no filtering happening and the item is checked by default
                        $whereFilter[] = $oDb->escape_str($filter->column, false) . ' = ' . $oDb->escape($option->value);
                    }
                }

                if (!empty($whereFilter)) {

                    if (!isset($data['where'])) {
                        $data['where'] = array();
                    }

                    $data['where'][] = '(' . implode(' OR ', $whereFilter) . ')';
                }
            }
        }

        //  Dropdown Filters
        //  @todo: implement these
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any where's into the query
     * @param  array  &$data   The data array
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

        $wheres = array(
            'where' => 'AND',
            'or_where' => 'OR',
            'where_in' => 'AND',
            'or_where_in' => 'OR',
            'where_not_in' => 'AND',
            'or_where_not_in' => 'OR'
        );

        $whereCompiled = array();

        foreach ($wheres as $whereType => $whereGlue) {

            if (!empty($data[$whereType])) {

                $whereCompiled[$whereType] = array();

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
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            //  If the $col is an array then we should concat them together
                            if (is_array($col)) {
                                $col = 'CONCAT_WS(" ", ' . implode(',', $col) . ')';
                            }

                            //  What's the operator?
                            if (!$oDb->_has_operator($col)) {

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

            $whereStr = array();

            foreach ($whereCompiled as $whereType => $value) {
                $whereStr[] = '(' . implode(' ' . $wheres[$whereType] . ' ', $value) . ')';
            }

            //  And reduce $whereStr to an actual string, like the name suggests
            $whereStr = implode(' AND ', $whereStr);
            $oDb->where($whereStr);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any like's into the query
     * @param  array  &$data   The data array
     * @return void
     */
    protected function getCountCommonCompileLikes(&$data)
    {
        $oDb = Factory::service('Database');

        $likes = array(
            'like' => 'AND',
            'or_like' => 'OR',
            'not_like' => 'AND',
            'or_not_like' => 'OR'
        );

        $likeCompiled = array();

        foreach ($likes as $likeType => $likeGlue) {

            if (!empty($data[$likeType])) {

                $likeCompiled[$likeType] = array();

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

            $whereStr = array();

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
     * @param  array  &$data The data array
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
        $aOut = array('column' => null, 'order' => null);

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
