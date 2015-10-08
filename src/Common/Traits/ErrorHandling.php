<?php

/**
 * Implements a common API for error handling in classes
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Traits;

trait ErrorHandling
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