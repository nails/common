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
     * Applies common conditionals
     *
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param string $data Data passed from the calling method
     * @param string $_caller The name of the calling method
     * @return void
     **/
    protected function _getcount_common($data = array(), $_caller = null)
    {
        //  Handle wheres
        $wheres = array('where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in');

        foreach ($wheres as $whereType) {

            if (!empty($data[$whereType])) {

                if (is_array($data[$whereType])) {

                    /**
                     * If it's a single dimensional array then just bung that into
                     * the db->where(). If not, loop it and parse.
                     */

                    $_first = reset($data[$whereType]);

                    if (is_string($_first)) {

                        $this->db->$whereType($data[$whereType]);

                    } else {

                        foreach ($data[$whereType] as $where) {

                            //  Work out column
                            $column = !empty($where['column']) ? $where['column'] : null;

                            if (is_null($column)) {

                                $column = !empty($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $value = isset($where['value']) ? $where['value'] : null;

                            if (is_null($value)) {

                                $value = !empty($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            if ($column) {

                                $this->db->$whereType($column, $value, $escape);
                            }
                        }
                    }

                } elseif (is_string($data[$whereType])) {

                    $this->db->$whereType($data[$whereType]);
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Handle Likes
        if (!empty($data['like'])) {

            if (is_string($data['like'])) {

                $this->db->like($data['like']);

            } elseif (is_array($data['like'])) {

                foreach ($data['like'] as $like) {

                    //  Work out column
                    $column = !empty($like['column']) ? $like['column'] : null;

                    if (is_null($column)) {

                        $column = !empty($like[0]) && is_string($like[0]) ? $like[0] : null;
                    }

                    //  Work out value
                    $value = isset($like['value']) ? $like['value'] : null;

                    if (is_null($value)) {

                        $value = !empty($like[1]) ? $like[1] : null;
                    }

                    //  Escaped?
                    $escape = isset($like['escape']) ? (bool) $like['escape'] : false;

                    if ($column) {

                        $this->db->like($column, $value, $escape);
                    }
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Handle sorting
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
