<?php

namespace Nails\Common\Helper\Model;

/**
 * Class GroupBy
 *
 * @package Nails\Common\Helper\Model
 */
class GroupBy
{
    private string $sColumn;

    // --------------------------------------------------------------------------

    /**
     * GroupBy constructor.
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
