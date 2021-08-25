<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Condition
 *
 * @package Nails\Common\Helper\Model
 */
class Condition
{
    private string $sColumn;
    private string $sDirection;

    // --------------------------------------------------------------------------

    /**
     * Condition constructor.
     *
     * @param string $sColumn
     */
    public function __construct(string $sColumn)
    {
        $this->sColumn = $sColumn;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return $this->sColumn;
    }
}
