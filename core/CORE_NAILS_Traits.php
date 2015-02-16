<?php

/**
 * This file provides traits used internally by Nails
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 * @link
 */

/**
 * Implements a common API for error handling in classes
 */
trait NAILS_COMMON_TRAIT_ERROR_HANDLING
{
    protected $_errors = array();

    // --------------------------------------------------------------------------

    /**
     * Set a generic error
     * @param string $error The error message
     */
    protected function _set_error($error)
    {
        $this->_errors[] = $error;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the error array
     * @return array
     */
    public function get_errors()
    {
        return $this->_errors;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the last error
     * @return string
     */
    public function last_error()
    {
        return end($this->_errors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears the last error
     * @return mixed
     */
    public function clear_last_error()
    {
        return array_pop($this->_errors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears all errors
     * @return void
     */
    public function clear_errors()
    {
        $this->_errors = array();
    }
}

// --------------------------------------------------------------------------

/**
 * Implements a common API for caching in classes
 */
trait NAILS_COMMON_TRAIT_CACHING
{
    protected $_cache_values    = array();
    protected $_cache_keys      = array();
    protected $_cache_method    = 'LOCAL';

    // --------------------------------------------------------------------------

    /**
     * Saves an item to the cache
     * @param string $key   The cache key
     * @param mixed  $value The data to be cached
     */
    protected function _set_cache($key, $value)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                $this->_cache_values[md5($_prefix . $key)] = serialize($value);
                $this->_cache_keys[] = $key;
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches an item from the cache
     * @param  string $key The cache key
     * @return mixed
     */
    protected function _get_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                if (isset($this->_cache_values[md5($_prefix . $key)])) {

                    return unserialize($this->_cache_values[md5($_prefix . $key)]);

                } else {

                    return false;

                }
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an item from the cache
     * @param  string $key The cache key
     * @return boolean
     */
    protected function _unset_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                unset($this->_cache_values[md5($_prefix . $key)]);

                $_key = array_search($key, $this->_cache_keys);

                if ($_key !== false) {

                    unset($this->_cache_keys[$_key]);
                }
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * In order to avoid collission between classes a prefix is used; this method
     * defines the cache key prefix using the calling class' name.
     * @return string
     */
    protected function _cache_prefix()
    {
        return get_called_class();
    }
}

/**
 * Implements the common getcount_common() and _getcount_common_parse_sort() methods
 */
trait NAILS_COMMON_TRAIT_GETCOUNT_COMMON
{
    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param array  $data    Data passed from the calling method
     * @param string $_caller The name of the calling method
     * @return void
     **/
    protected function _getcount_common($data = array(), $_caller = null)
    {
        $this->_getcount_compile_filters($data);
        $this->_getcount_compile_wheres($data);
        $this->_getcount_compile_likes($data);
        $this->_getcount_compile_sort($data);
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any active filters back into the $data array
     * @param  array &$data The data array
     * @return void
     */
    protected function _getcount_compile_filters(&$data)
    {
        /**
         * Handle filters
         *
         * Filters are basically an easy way to modify the query's where element.
         */

        if (!empty($data['filters'])) {

            foreach ($data['filters'] as $filterIndex => $filter) {

                $whereFilter = array()  ;
                foreach ($filter->options as $optionIndex => $option) {

                    if (!empty($_GET['filter'][$filterIndex][$optionIndex])) {

                        //  Filtering is happening and the item is to be filtered
                        $whereFilter[] = $this->db->escape_str($filter->column, false) . ' = ' . $this->db->escape($option->value);

                    } elseif (empty($_GET['filter']) && $option->checked) {

                        //  There's no filtering happening and the item is checked by default
                        $whereFilter[] = $this->db->escape_str($filter->column, false) . ' = ' . $this->db->escape($option->value);
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
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any where's into the query
     * @param  array &$data The data array
     * @return void
     */
    protected function _getcount_compile_wheres(&$data)
    {
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
                            $col = !empty($where['column']) ? $where['column'] : '[NAILS-COL-NOT-FOUND]';

                            if ($col === '[NAILS-COL-NOT-FOUND]') {

                                $col = !empty($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $val = isset($where['value']) ? $where['value'] : '[NAILS-VAL-NOT-FOUND]';

                            if ($val === '[NAILS-VAL-NOT-FOUND]') {

                                $val = !empty($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            //  If the $col is an array then we should concat them together
                            if (is_array($col)) {

                                $col = 'CONCAT_WS(" ", ' . implode(',', $col) . ')';
                            }

                            //  What's the operator?
                            if (!$this->db->_has_operator($col)) {

                                $operator = '=';

                            } else {

                                $operator = '';
                            }

                            //  Got something?
                            if ($col) {

                                switch ($whereType) {

                                    case 'where' :
                                    case 'or_where' :

                                        if ($escape) {

                                            $val = $this->db->escape($val);
                                        }

                                        $whereCompiled[$whereType][] = $col . $operator . $val;
                                        break;

                                    case 'where_in' :
                                    case 'or_where_in' :

                                        if (!is_array($val)) {

                                            $val = (array) $val;
                                        }

                                        if ($escape) {

                                            foreach ($val as &$value) {

                                                $value = $this->db->escape($value);
                                            }
                                        }

                                        $whereCompiled[$whereType][] = $col . ' IN (' . implode(',', $val) . ')';
                                        break;

                                    case 'where_not_in' :
                                    case 'or_where_not_in' :

                                        if (!is_array($val)) {

                                            $val = (array) $val;
                                        }

                                        if ($escape) {

                                            foreach ($val as &$value) {

                                                $value = $this->db->escape($value);
                                            }
                                        }

                                        $whereCompiled[$whereType][] = $col . ' NOT IN (' . implode(',', $val) . ')';
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
            $this->db->where($whereStr);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles any like's into the query
     * @param  array &$data The data array
     * @return void
     */
    protected function _getcount_compile_likes(&$data)
    {
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
                            $col = !empty($where['column']) ? $where['column'] : '[NAILS-COL-NOT-FOUND]';

                            if ($col === '[NAILS-COL-NOT-FOUND]') {

                                $col = !empty($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $val = isset($where['value']) ? $where['value'] : '[NAILS-VAL-NOT-FOUND]';

                            if ($val === '[NAILS-VAL-NOT-FOUND]') {

                                $val = !empty($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            if ($escape) {

                                $val = $this->db->escape_like_str($val);
                            }

                            //  If the $col is an array then we should concat them together
                            if (is_array($col)) {

                                $col = 'CONCAT_WS(" ", ' . implode(',', $col) . ')';
                            }

                            //  What's the operator?
                            if (!$this->db->_has_operator($col)) {

                                $operator = '=';

                            } else {

                                $operator = '';
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
            $this->db->where($whereStr);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the sort element into the query
     * @param  array &$data The data array
     * @return void
     */
    protected function _getcount_compile_sort(&$data)
    {
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
                $this->db->order_by($data['sort']);

            } elseif (is_array($data['sort'])) {

                $_first = reset($data['sort']);

                if (is_string($_first)) {

                    //  Single dimension array
                    $_sort = $this->_getcount_common_parse_sort($data['sort']);

                    if (!empty($_sort['column'])) {

                        $this->db->order_by($_sort['column'], $_sort['order']);

                    }

                } else {

                    //  Multi dimension array
                    foreach ($data['sort'] as $sort) {

                        $_sort = $this->_getcount_common_parse_sort($sort);

                        if (!empty($_sort['column'])) {

                            $this->db->order_by($_sort['column'], $_sort['order']);
                        }
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    protected function _getcount_common_parse_sort($sort)
    {
        $_out = array('column' => null, 'order' => null);

        // --------------------------------------------------------------------------

        if (is_string($sort)) {

            $_out['column'] = $sort;
            return $_out;

        } elseif (isset($sort['column'])) {

            $_out['column'] = $sort['column'];

        } else {

            //  Take the first element
            $_out['column'] = reset($sort);
            $_out['column'] = is_string($_out['column']) ? $_out['column'] : null;
        }

        if ($_out['column']) {

            //  Determine order
            if (isset($sort['order'])) {

                $_out['order'] = $sort['order'];

            } elseif (count($sort) > 1) {

                //  Take the last element
                $_out['order'] = end($sort);
                $_out['order'] = is_string($_out['order']) ? $_out['order'] : null;
            }
        }

        // --------------------------------------------------------------------------

        return $_out;
    }
}
