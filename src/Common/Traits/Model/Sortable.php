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
     * Force implementators to provide a getColumn() method
     * @return string
     */
    abstract public function getColumn();

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
