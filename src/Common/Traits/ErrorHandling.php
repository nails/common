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
    protected $aErrors = array();

    // --------------------------------------------------------------------------

    /**
     * Set a generic error
     * @param string $error The error message
     */
    protected function setError($sError)
    {
        $this->aErrors[] = $sError;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the error array
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the last error
     * @return string
     */
    public function lastError()
    {
        return end($this->aErrors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears the last error
     * @return mixed
     */
    public function clearLastError()
    {
        return array_pop($this->aErrors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears all errors
     * @return void
     */
    public function clearErrors()
    {
        $this->aErrors = array();
    }
}
