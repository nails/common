<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Model\Base;

/**
 * Trait User
 *
 * @package Nails\Common\Traits\Model
 */
trait User
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
     * Returns whether this model automatically sets created/modified users or not
     *
     * @return bool
     */
    public function isAutoSetUsers()
    {
        return (defined('static::AUTO_SET_USER') ? static::AUTO_SET_USER : true)
            && function_exists('activeUser')
            && function_exists('isLoggedIn');
    }

    // --------------------------------------------------------------------------

    /**
     * Set the created/modified user IDs on the $aData array
     *
     * @param array $aData       The data being passed to the model
     * @param bool  $bSetCreated Whether to set the created user or not
     *
     * @return $this
     */
    protected function setDataUsers(array &$aData, bool $bSetCreated = true): self
    {
        if ($this->isAutoSetUsers()) {
            if (isLoggedIn()) {
                if ($bSetCreated && empty($aData[$this->getColumnCreatedBy()])) {
                    $aData[$this->getColumnCreatedBy()] = activeUser('id');
                }
                if (empty($aData[$this->getColumnModifiedBy()])) {
                    $aData[$this->getColumnModifiedBy()] = activeUser('id');
                }
            } else {
                if ($bSetCreated && empty($aData[$this->getColumnCreatedBy()])) {
                    $aData[$this->getColumnCreatedBy()] = null;
                }
                if (empty($aData[$this->getColumnModifiedBy()])) {
                    $aData[$this->getColumnModifiedBy()] = null;
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for created_by
     *
     * @return string
     */
    public function getColumnCreatedBy(): string
    {
        return $this->getColumn('created_by', 'created_by');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for created_by
     *
     * @return string
     */
    public function getColumnModifiedBy(): string
    {
        return $this->getColumn('modified_by', 'modified_by');
    }
}
