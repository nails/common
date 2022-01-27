<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Factory\Model\Field;

/**
 * Trait HasDataColumns
 *
 * @package Nails\Common\Traits\Model
 */
trait HasDataColumns
{
    /**
     * Describes the fields for this model automatically and with some guesswork;
     * for more fine grained control models should overload this method.
     *
     * @param string|null $sTable The database table to query
     *
     * @return Field[]
     */
    abstract public function describeFields($sTable = null);

    /**
     * Returns the columns which contain large data
     *
     * @return string[]
     */
    abstract public function getDataColumns(): array;

    // --------------------------------------------------------------------------

    /**
     * Describes the fields excluding the data fields (which can be very big and cause memory issues)
     *
     * @return Field[]
     */
    public function describeFieldsExcludingData()
    {
        return array_filter(array_keys($this->describeFields()), function (string $sField) {
            return !in_array($sField, $this->getDataColumns());
        });
    }
}
