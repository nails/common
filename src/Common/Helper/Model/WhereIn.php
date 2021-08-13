<?php

namespace Nails\Common\Helper\Model;

/**
 * Class WhereIn
 *
 * @package Nails\Common\Helper\Model
 */
class WhereIn
{
    private string $sColumn;
    private array  $aValues;

    // --------------------------------------------------------------------------

    /**
     * WhereIn constructor.
     *
     * @param string     $sColumn
     * @param array|null $aValues
     */
    public function __construct(string $sColumn, array $aValues = null)
    {
        $this->sColumn = $sColumn;
        $this->aValues = $aValues;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the where statement
     *
     * @return array|string
     */
    public function compile()
    {
        return [$this->sColumn, $this->aValues];
    }
}
