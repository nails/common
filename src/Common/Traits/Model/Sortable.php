<?php

namespace Nails\Common\Traits\Model;

use Nails\Factory;

/**
 * Trait Sortable
 * @package Nails\Common\Traits\Model
 */
trait Sortable
{
    /**
     * Returns the column name for specific columns of interest
     *
     * @param string $sColumn  The column to query
     * @param string $sDefault The default value if not defined
     *
     * @return string
     */
    abstract public function getColumn($sColumn, $sDefault = null);

    // --------------------------------------------------------------------------

    /**
     * Returns the column on which to sort
     * @return string
     */
    public function getSortableColumn()
    {
        return $this->getColumn('sortable', 'order');
    }
}
